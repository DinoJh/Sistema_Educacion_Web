<?php
namespace App\Controllers;

/**
 * Controller Contacto
 * Cualquier perfil puede enviar un mensaje al administrador.
 * El admin ve todos los mensajes y puede responder.
 */
class Contacto extends BaseController
{
    public $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) {
            echo "inactivo"; exit(0);
        }
    }

    // POST /contacto/enviar  — cualquier usuario logueado
    public function enviar()
    {
        $tipo    = $this->request->getPost('tipo');
        $asunto  = trim($this->request->getPost('asunto'));
        $mensaje = trim($this->request->getPost('mensaje'));

        if (!$asunto || !$mensaje) {
            echo json_encode(['ok'=>false,'msg'=>'Completa el asunto y el mensaje.']);
            return;
        }

        $tiposValidos = ['CONSULTA','QUEJA','RECLAMO','SUGERENCIA'];
        if (!in_array($tipo, $tiposValidos)) $tipo = 'CONSULTA';

        $db = \Config\Database::connect();
        $db->table('contacto_admin')->insert([
            'cont_usua_ide'  => $this->session->usua_ide,
            'cont_tipo'      => $tipo,
            'cont_asunto'    => $asunto,
            'cont_mensaje'   => $mensaje,
            'cont_leida'     => 0,
            'cont_create_at' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode(['ok'=>true,'msg'=>'Tu mensaje fue enviado. El administrador lo revisará pronto.']);
    }

    // GET /contacto/admin  — solo ADMIN
    public function admin()
    {
        if ($this->session->perf_ide != 3) { echo "Sin acceso"; exit(0); }

        $db = \Config\Database::connect();

        // Marcar como vistas (leida=1) las que llegan como nuevas (leida=0)
        $db->table('contacto_admin')->where('cont_leida', 0)->update(['cont_leida' => 1]);

        $mensajes = $db->table('contacto_admin c')
            ->select('c.cont_ide, c.cont_tipo, c.cont_asunto, c.cont_mensaje,
                      c.cont_respuesta, c.cont_leida, c.cont_create_at, c.cont_resp_at,
                      u.usua_nombres, u.usua_paterno, u.usua_materno, u.usua_email,
                      p.perf_nombre')
            ->join('usuarios u',  'u.usua_ide=c.cont_usua_ide', 'left')
            ->join('perfiles p',  'p.perf_ide=u.usua_perf_ide', 'left')
            ->orderBy('c.cont_create_at', 'DESC')
            ->get()->getResult();

        echo view('contacto/vadmin', [
            'mensajes' => $mensajes,
            'base'     => base_url('public'),
            'session'  => $this->session,
        ]);
    }

    // POST /contacto/responder  — ADMIN responde un mensaje
    public function responder()
    {
        if ($this->session->perf_ide != 3) {
            echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return;
        }

        $db         = \Config\Database::connect();
        $cont_ide   = (int)$this->request->getPost('cont_ide');
        $respuesta  = trim($this->request->getPost('respuesta'));

        if (!$cont_ide || !$respuesta) {
            echo json_encode(['ok'=>false,'msg'=>'Escribe la respuesta antes de enviar.']);
            return;
        }

        // Actualizar mensaje
        $db->table('contacto_admin')
            ->where('cont_ide', $cont_ide)
            ->update([
                'cont_respuesta' => $respuesta,
                'cont_leida'     => 2,
                'cont_resp_at'   => date('Y-m-d H:i:s'),
            ]);

        // Notificar al usuario
        $cont = $db->table('contacto_admin')->where('cont_ide',$cont_ide)->get()->getRow();
        if ($cont) {
            $db->table('notificaciones')->insert([
                'noti_usua_ide'   => $cont->cont_usua_ide,
                'noti_tipo'       => 'CONTACTO_RESP',
                'noti_titulo'     => 'El administrador respondió tu mensaje',
                'noti_mensaje'    => 'Asunto: "'.$cont->cont_asunto.'" → '.mb_substr($respuesta,0,100).(mb_strlen($respuesta)>100?'…':''),
                'noti_link'       => null,
                'noti_link_label' => null,
                'noti_leida'      => 0,
                'noti_create_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        echo json_encode(['ok'=>true,'msg'=>'Respuesta enviada y usuario notificado.']);
    }
}
