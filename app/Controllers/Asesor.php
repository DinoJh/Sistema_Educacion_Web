<?php
namespace App\Controllers;
use App\Models\General;

/**
 * Controller Asesor — v3
 * Agrega: enviarEmail() para enviar correos masivos a los alumnos del grupo.
 * El resto del código es igual al v2; se copian todos los métodos
 * para que sea un reemplazo completo limpio.
 */
class Asesor extends BaseController
{
    public $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) {
            echo "inactivo"; exit(0);
        }
        if (!in_array($this->session->perf_ide, [1, 3, 4])) {
            echo "Sin acceso"; exit(0);
        }
    }

    // ─────────────────────────────────────
    // Helper: crear notificación interna
    // ─────────────────────────────────────
    private function crearNotificacion($usua_ide, $tipo, $titulo, $mensaje, $link = null, $link_label = null)
    {
        $db = \Config\Database::connect();
        $db->table('notificaciones')->insert([
            'noti_usua_ide'   => $usua_ide,
            'noti_tipo'       => $tipo,
            'noti_titulo'     => $titulo,
            'noti_mensaje'    => $mensaje,
            'noti_link'       => $link,
            'noti_link_label' => $link_label,
            'noti_leida'      => 0,
            'noti_create_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    // ─────────────────────────────────────
    // ASESOR: cursos activos
    // ─────────────────────────────────────
    public function cursos()
    {
        if (!in_array($this->session->perf_ide, [3, 4])) { echo "Sin acceso"; exit; }
        $db     = \Config\Database::connect();
        $cursos = $db->table('cursos c')
            ->select('c.curs_ide, c.curs_nombre, c.curs_descripcion, c.curs_nivel,
                      cat.cate_nombre, cat.cate_icono,
                      u.usua_nombres, u.usua_paterno, u.usua_materno,
                      (SELECT COUNT(*) FROM matriculas m WHERE m.matr_curs_ide=c.curs_ide AND m.matr_esta_ide=1) as total_alumnos')
            ->join('categorias cat','cat.cate_ide=c.curs_cate_ide','left')
            ->join('profesores p',  'p.prof_ide=c.curs_prof_ide',  'left')
            ->join('usuarios u',    'u.usua_ide=p.prof_usua_ide',  'left')
            ->where('c.curs_esta_ide', 1)
            ->orderBy('c.curs_nombre')
            ->get()->getResult();

        echo view('asesor/vcursos', [
            'cursos'  => $cursos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }

    // ─────────────────────────────────────
    // ASESOR: alumnos de un curso
    // ─────────────────────────────────────
    public function alumnos($curs_ide)
    {
        if (!in_array($this->session->perf_ide, [3, 4])) { echo "Sin acceso"; exit; }
        $db    = \Config\Database::connect();
        $curso = $db->table('cursos c')
            ->select('c.curs_ide, c.curs_nombre, c.curs_nivel,
                      u.usua_nombres as prof_nombres, u.usua_paterno as prof_paterno, u.usua_materno as prof_materno')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
            ->join('usuarios u',  'u.usua_ide=p.prof_usua_ide','left')
            ->where('c.curs_ide', $curs_ide)
            ->get()->getRow();
        if (!$curso) { echo "Curso no encontrado."; exit; }

        $ci = (int)$curs_ide;
        $alumnos = $db->table('matriculas m')
            ->select("u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_materno,
                      u.usua_email, u.usua_celular,
                      m.matr_completado, m.matr_fecha,
                      (SELECT COUNT(*) FROM progreso pr
                           JOIN lecciones ll ON ll.lecc_ide=pr.prog_lecc_ide
                           WHERE pr.prog_usua_ide=u.usua_ide AND ll.lecc_curs_ide={$ci}
                           AND pr.prog_completado=1) AS lecc_hechas,
                      (SELECT COUNT(*) FROM lecciones ll2
                           WHERE ll2.lecc_curs_ide={$ci} AND ll2.lecc_esta_ide=1
                           AND ll2.lecc_tipo!='QUIZ') AS total_lecc,
                      (SELECT ga.grua_grup_ide FROM grupo_alumnos ga
                           JOIN grupos_asesor g ON g.grup_ide=ga.grua_grup_ide
                           WHERE ga.grua_usua_ide=u.usua_ide AND g.grup_curs_ide={$ci} LIMIT 1) AS grupo_ide,
                      (SELECT CONCAT(ua.usua_paterno,' ',ua.usua_nombres)
                           FROM grupo_alumnos ga2 JOIN grupos_asesor g2 ON g2.grup_ide=ga2.grua_grup_ide
                           JOIN asesores a2 ON a2.ases_ide=g2.grup_ases_ide
                           JOIN usuarios ua ON ua.usua_ide=a2.ases_usua_ide
                           WHERE ga2.grua_usua_ide=u.usua_ide AND g2.grup_curs_ide={$ci} LIMIT 1) AS asesor_nombre")
            ->join('usuarios u','u.usua_ide=m.matr_usua_ide')
            ->where('m.matr_curs_ide', $curs_ide)
            ->where('m.matr_esta_ide', 1)
            ->where('u.usua_perf_ide', 1)
            ->where('u.usua_deleted_at IS NULL')
            ->orderBy('u.usua_paterno')
            ->get()->getResult();

        echo view('asesor/valumnos', [
            'curso'   => $curso,
            'alumnos' => $alumnos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }

    // ─────────────────────────────────────
    // ASESOR: formar grupo + notificar
    // ─────────────────────────────────────
    public function formarGrupo()
    {
        if (!in_array($this->session->perf_ide, [3, 4])) {
            echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return;
        }
        $db       = \Config\Database::connect();
        $curs_ide = (int)$this->request->getPost('curs_ide');
        $nombre   = trim($this->request->getPost('nombre_grupo'));
        $alumnos  = $this->request->getPost('alumnos');

        if (!$curs_ide || !$nombre || empty($alumnos) || !is_array($alumnos)) {
            echo json_encode(['ok'=>false,'msg'=>'Completa todos los campos y selecciona al menos un alumno.']);
            return;
        }

        $ases = $db->table('asesores')->where('ases_usua_ide',$this->session->usua_ide)->get()->getRow();
        if (!$ases) {
            $db->table('asesores')->insert([
                'ases_usua_ide'  => $this->session->usua_ide,
                'ases_esta_ide'  => 1,
                'ases_create_at' => date('Y-m-d H:i:s'),
            ]);
            $ases_ide = $db->insertID();
        } else {
            $ases_ide = $ases->ases_ide;
        }

        $validos = [];
        foreach ($alumnos as $uid) {
            $uid = (int)$uid;
            if ($uid <= 0) continue;
            $existe = $db->table('grupo_alumnos ga')
                ->join('grupos_asesor g','g.grup_ide=ga.grua_grup_ide')
                ->where('ga.grua_usua_ide',$uid)->where('g.grup_curs_ide',$curs_ide)
                ->countAllResults();
            if ($existe == 0) $validos[] = $uid;
        }

        if (empty($validos)) {
            echo json_encode(['ok'=>false,'msg'=>'Todos los alumnos seleccionados ya tienen asesor en este curso.']);
            return;
        }

        $curso = $db->table('cursos c')
            ->select('c.curs_nombre, u.usua_nombres as prof_n, u.usua_paterno as prof_p')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
            ->join('usuarios u','u.usua_ide=p.prof_usua_ide','left')
            ->where('c.curs_ide',$curs_ide)->get()->getRow();

        $asesorReg = $db->table('usuarios')
            ->select('usua_paterno, usua_nombres')
            ->where('usua_ide',$this->session->usua_ide)->get()->getRow();

        $db->table('grupos_asesor')->insert([
            'grup_nombre'    => $nombre,
            'grup_ases_ide'  => $ases_ide,
            'grup_curs_ide'  => $curs_ide,
            'grup_create_at' => date('Y-m-d H:i:s'),
        ]);
        $grup_ide = $db->insertID();

        foreach ($validos as $uid) {
            $db->table('grupo_alumnos')->insert([
                'grua_grup_ide'  => $grup_ide,
                'grua_usua_ide'  => $uid,
                'grua_create_at' => date('Y-m-d H:i:s'),
            ]);
            $this->crearNotificacion(
                $uid, 'GRUPO_NUEVO',
                '¡Has sido añadido a un grupo de asesoría!',
                'Grupo: "'.$nombre.'" · Curso: '.($curso->curs_nombre??'').
                ' · Asesor: '.($asesorReg ? $asesorReg->usua_paterno.' '.$asesorReg->usua_nombres : ''),
                '/alumno/grupos', 'Ver mis grupos'
            );
        }

        $omitidos = count($alumnos) - count($validos);
        $msg = 'Grupo "'.htmlspecialchars($nombre).'" formado con '.count($validos).' alumno(s).';
        if ($omitidos > 0) $msg .= ' ('.$omitidos.' ya tenían asesor y fueron omitidos)';
        echo json_encode(['ok'=>true,'msg'=>$msg,'grup_ide'=>$grup_ide]);
    }

    // ─────────────────────────────────────
    // ASESOR: mis grupos
    // ─────────────────────────────────────
    public function grupos()
    {
        if (!in_array($this->session->perf_ide, [3, 4])) { echo "Sin acceso"; exit; }
        $db   = \Config\Database::connect();
        $ases = $db->table('asesores')->where('ases_usua_ide',$this->session->usua_ide)->get()->getRow();
        $grupos = [];
        if ($ases) {
            $grupos = $db->table('grupos_asesor g')
                ->select('g.grup_ide, g.grup_nombre, g.grup_create_at,
                          c.curs_nombre, c.curs_nivel,
                          u.usua_nombres as prof_nombres, u.usua_paterno as prof_paterno,
                          (SELECT COUNT(*) FROM grupo_alumnos ga  WHERE ga.grua_grup_ide=g.grup_ide) as total_alumnos,
                          (SELECT COUNT(*) FROM grupo_mensajes gm WHERE gm.grum_grup_ide=g.grup_ide) as total_mensajes,
                          (SELECT COUNT(*) FROM grupo_emails   ge WHERE ge.grem_grup_ide=g.grup_ide) as total_emails')
                ->join('cursos c',   'c.curs_ide=g.grup_curs_ide','left')
                ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
                ->join('usuarios u', 'u.usua_ide=p.prof_usua_ide','left')
                ->where('g.grup_ases_ide',$ases->ases_ide)
                ->orderBy('g.grup_create_at','DESC')
                ->get()->getResult();
        }
        echo view('asesor/vgrupos', [
            'grupos'  => $grupos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }

    // ─────────────────────────────────────
    // ALUMNO: sus grupos de asesoría
    // ─────────────────────────────────────
    public function gruposAlumno()
    {
        if ($this->session->perf_ide != 1) { echo "Sin acceso"; exit; }
        $db = \Config\Database::connect();
        $uid = $this->session->usua_ide;

        $grupos = $db->table('grupo_alumnos ga')
            ->select("g.grup_ide, g.grup_nombre, g.grup_create_at,
                      c.curs_nombre, c.curs_nivel, c.curs_ide,
                      pu.usua_nombres as prof_nombres, pu.usua_paterno as prof_paterno,
                      au.usua_nombres as ases_nombres, au.usua_paterno as ases_paterno,
                      (SELECT COUNT(*) FROM grupo_alumnos ga2 WHERE ga2.grua_grup_ide=g.grup_ide) as total_miembros,
                      (SELECT COUNT(*) FROM grupo_mensajes gm WHERE gm.grum_grup_ide=g.grup_ide) as total_mensajes,
                      0 as msgs_nuevos")
            ->join('grupos_asesor g', 'g.grup_ide=ga.grua_grup_ide')
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',  'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',  'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide', 'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',  'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide', 'left')
            ->where('ga.grua_usua_ide', $uid)
            ->orderBy('g.grup_create_at','DESC')
            ->get()->getResult();

        echo view('alumno/vgrupos', [
            'grupos'  => $grupos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }

    // ─────────────────────────────────────
    // ASESOR / ADMIN / ALUMNO: chat del grupo
    // ─────────────────────────────────────
    public function chat($grup_ide)
    {
        $db    = \Config\Database::connect();
        $grupo = $db->table('grupos_asesor g')
            ->select('g.grup_ide, g.grup_nombre, g.grup_create_at, g.grup_curs_ide,
                      c.curs_nombre, c.curs_nivel,
                      pu.usua_nombres as prof_nombres, pu.usua_paterno as prof_paterno,
                      au.usua_nombres as ases_nombres, au.usua_paterno as ases_paterno,
                      a.ases_usua_ide,
                      (SELECT COUNT(*) FROM grupo_alumnos ga WHERE ga.grua_grup_ide=g.grup_ide) as total_alumnos')
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',  'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',  'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide', 'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',  'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide', 'left')
            ->where('g.grup_ide', $grup_ide)
            ->get()->getRow();
        if (!$grupo) { echo "Grupo no encontrado."; exit; }

        $perf = $this->session->perf_ide;
        if ($perf == 3) {
            // ADMIN: todo
        } elseif ($perf == 4) {
            $ases = $db->table('asesores')->where('ases_usua_ide',$this->session->usua_ide)->get()->getRow();
            $ok   = $db->table('grupos_asesor')
                ->where('grup_ide',$grup_ide)->where('grup_ases_ide',$ases ? $ases->ases_ide : 0)
                ->countAllResults();
            if (!$ok) { echo "Sin acceso a este grupo."; exit; }
        } elseif ($perf == 1) {
            $ok = $db->table('grupo_alumnos')
                ->where('grua_grup_ide',$grup_ide)->where('grua_usua_ide',$this->session->usua_ide)
                ->countAllResults();
            if (!$ok) { echo "No perteneces a este grupo."; exit; }
            $db->table('notificaciones')
                ->where('noti_usua_ide',$this->session->usua_ide)
                ->where('noti_tipo','MENSAJE_NUEVO')
                ->like('noti_link','/asesor/chat/'.$grup_ide)
                ->update(['noti_leida'=>1]);
        } else { echo "Sin acceso."; exit; }

        $ci = (int)$grupo->grup_curs_ide;
        $alumnos = $db->table('grupo_alumnos ga')
            ->select("u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_materno, u.usua_email,
                      (SELECT COUNT(*) FROM progreso pr
                           JOIN lecciones ll ON ll.lecc_ide=pr.prog_lecc_ide
                           WHERE pr.prog_usua_ide=u.usua_ide AND ll.lecc_curs_ide={$ci}
                           AND pr.prog_completado=1) AS lecc_hechas,
                      (SELECT COUNT(*) FROM lecciones ll2
                           WHERE ll2.lecc_curs_ide={$ci} AND ll2.lecc_esta_ide=1
                           AND ll2.lecc_tipo!='QUIZ') AS total_lecc")
            ->join('usuarios u','u.usua_ide=ga.grua_usua_ide')
            ->where('ga.grua_grup_ide',$grup_ide)
            ->orderBy('u.usua_paterno')
            ->get()->getResult();

        $mensajes = $db->table('grupo_mensajes gm')
            ->select('gm.grum_ide, gm.grum_mensaje, gm.grum_create_at,
                      u.usua_nombres, u.usua_paterno, u.usua_perf_ide, u.usua_ide')
            ->join('usuarios u','u.usua_ide=gm.grum_usua_ide')
            ->where('gm.grum_grup_ide',$grup_ide)
            ->orderBy('gm.grum_create_at','ASC')
            ->get()->getResult();

        // Historial de correos enviados al grupo
        $emailsLog = $db->table('grupo_emails ge')
            ->select('ge.grem_ide, ge.grem_asunto, ge.grem_total, ge.grem_errores, ge.grem_create_at,
                      u.usua_nombres as env_nombres, u.usua_paterno as env_paterno')
            ->join('usuarios u','u.usua_ide=ge.grem_usua_ide','left')
            ->where('ge.grem_grup_ide',$grup_ide)
            ->orderBy('ge.grem_create_at','DESC')
            ->limit(10)
            ->get()->getResult();

        echo view('asesor/vchat', [
            'grupo'     => $grupo,
            'alumnos'   => $alumnos,
            'mensajes'  => $mensajes,
            'emailsLog' => $emailsLog,
            'base'      => base_url('public'),
            'session'   => $this->session,
        ]);
    }

    // ─────────────────────────────────────
    // Todos: enviar mensaje en chat + notif
    // ─────────────────────────────────────
    public function enviarMensaje()
    {
        $db       = \Config\Database::connect();
        $grup_ide = (int)$this->request->getPost('grup_ide');
        $mensaje  = trim($this->request->getPost('mensaje'));
        $perf     = $this->session->perf_ide;

        if (!$grup_ide || !$mensaje) {
            echo json_encode(['ok'=>false,'msg'=>'Escribe un mensaje antes de enviar.']); return;
        }

        if ($perf == 3) {
            // ADMIN: OK
        } elseif ($perf == 4) {
            $ases = $db->table('asesores')->where('ases_usua_ide',$this->session->usua_ide)->get()->getRow();
            $ok   = $db->table('grupos_asesor')
                ->where('grup_ide',$grup_ide)->where('grup_ases_ide',$ases ? $ases->ases_ide : 0)
                ->countAllResults();
            if (!$ok) { echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return; }
        } elseif ($perf == 1) {
            $ok = $db->table('grupo_alumnos')
                ->where('grua_grup_ide',$grup_ide)->where('grua_usua_ide',$this->session->usua_ide)
                ->countAllResults();
            if (!$ok) { echo json_encode(['ok'=>false,'msg'=>'No perteneces a este grupo.']); return; }
        } else {
            echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return;
        }

        $db->table('grupo_mensajes')->insert([
            'grum_grup_ide'  => $grup_ide,
            'grum_usua_ide'  => $this->session->usua_ide,
            'grum_mensaje'   => $mensaje,
            'grum_create_at' => date('Y-m-d H:i:s'),
        ]);

        $grupo = $db->table('grupos_asesor g')
            ->select('g.grup_nombre, c.curs_nombre')
            ->join('cursos c','c.curs_ide=g.grup_curs_ide','left')
            ->where('g.grup_ide',$grup_ide)->get()->getRow();

        $remitente  = $db->table('usuarios')
            ->select('usua_paterno, usua_nombres')
            ->where('usua_ide',$this->session->usua_ide)->get()->getRow();
        $remNombre  = $remitente ? $remitente->usua_paterno.' '.$remitente->usua_nombres : 'Alguien';
        $extracto   = '"'.mb_substr($mensaje,0,80).(mb_strlen($mensaje)>80?'…':'"');

        $alumnos = $db->table('grupo_alumnos')
            ->where('grua_grup_ide',$grup_ide)->where('grua_usua_ide !=',$this->session->usua_ide)
            ->get()->getResult();
        foreach ($alumnos as $a) {
            $this->crearNotificacion($a->grua_usua_ide,'MENSAJE_NUEVO',
                'Nuevo mensaje en tu grupo de asesoría',
                $remNombre.' escribió en "'.($grupo->grup_nombre??'').'": '.$extracto,
                '/asesor/chat/'.$grup_ide, 'Ver chat');
        }

        if ($perf != 4) {
            $asesorUsua = $db->table('grupos_asesor ga')
                ->select('u.usua_ide')
                ->join('asesores a','a.ases_ide=ga.grup_ases_ide')
                ->join('usuarios u','u.usua_ide=a.ases_usua_ide')
                ->where('ga.grup_ide',$grup_ide)->get()->getRow();
            if ($asesorUsua && $asesorUsua->usua_ide != $this->session->usua_ide) {
                $this->crearNotificacion($asesorUsua->usua_ide,'MENSAJE_NUEVO',
                    'Nuevo mensaje en tu grupo',
                    $remNombre.' escribió en "'.($grupo->grup_nombre??'').'": '.$extracto,
                    '/asesor/chat/'.$grup_ide, 'Ver chat');
            }
        }

        echo json_encode(['ok'=>true,'msg'=>'Mensaje enviado.']);
    }

    // ═══════════════════════════════════════════════════════
    //  ★  NUEVO: ENVIAR EMAIL A LOS ALUMNOS DEL GRUPO  ★
    // ═══════════════════════════════════════════════════════
    /**
     * POST /asesor/enviar-email
     * Solo ASESOR dueño del grupo o ADMIN pueden usar esto.
     * Envía un correo HTML a cada alumno del grupo.
     * Guarda el resultado en grupo_emails para historial.
     */
    public function enviarEmail()
    {
        $db       = \Config\Database::connect();
        $perf     = $this->session->perf_ide;
        $grup_ide = (int)$this->request->getPost('grup_ide');
        $asunto   = trim($this->request->getPost('asunto'));
        $cuerpo   = trim($this->request->getPost('cuerpo'));

        if (!in_array($perf, [3, 4])) {
            echo json_encode(['ok'=>false,'msg'=>'Solo el asesor o un administrador puede enviar correos.']);
            return;
        }

        if (!$grup_ide || !$asunto || !$cuerpo) {
            echo json_encode(['ok'=>false,'msg'=>'Completa el asunto y el cuerpo del correo.']);
            return;
        }

        // Validar que el asesor sea dueño del grupo (ADMIN lo salta)
        if ($perf == 4) {
            $ases = $db->table('asesores')->where('ases_usua_ide',$this->session->usua_ide)->get()->getRow();
            $ok   = $db->table('grupos_asesor')
                ->where('grup_ide',$grup_ide)->where('grup_ases_ide',$ases ? $ases->ases_ide : 0)
                ->countAllResults();
            if (!$ok) {
                echo json_encode(['ok'=>false,'msg'=>'No eres el asesor de este grupo.']);
                return;
            }
        }

        // Datos del grupo y asesor para el cuerpo del email
        $grupo = $db->table('grupos_asesor g')
            ->select('g.grup_nombre, c.curs_nombre, c.curs_nivel,
                      pu.usua_nombres as prof_nombres, pu.usua_paterno as prof_paterno,
                      au.usua_nombres as ases_nombres, au.usua_paterno as ases_paterno,
                      au.usua_email as ases_email')
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',  'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',  'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide', 'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',  'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide', 'left')
            ->where('g.grup_ide',$grup_ide)
            ->get()->getRow();

        if (!$grupo) {
            echo json_encode(['ok'=>false,'msg'=>'Grupo no encontrado.']);
            return;
        }

        // Alumnos del grupo con email
        $alumnos = $db->table('grupo_alumnos ga')
            ->select('u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_email')
            ->join('usuarios u','u.usua_ide=ga.grua_usua_ide')
            ->where('ga.grua_grup_ide',$grup_ide)
            ->where('u.usua_email IS NOT NULL')
            ->where("u.usua_email != ''")
            ->get()->getResult();

        if (empty($alumnos)) {
            echo json_encode(['ok'=>false,'msg'=>'Ningún alumno del grupo tiene correo registrado.']);
            return;
        }

        // Remitente actual
        $remitente = $db->table('usuarios')
            ->select('usua_nombres, usua_paterno, usua_email')
            ->where('usua_ide',$this->session->usua_ide)->get()->getRow();
        $remNombre = $remitente ? $remitente->usua_paterno.' '.$remitente->usua_nombres : 'Asesor';
        $remEmail  = $remitente ? $remitente->usua_email : null;

        // ── Inicializar el servicio de email de CI4 ──
        $emailSvc = \Config\Services::email();

        $enviados   = [];
        $fallidos   = [];
        $totalOk    = 0;
        $totalError = 0;

        foreach ($alumnos as $alumno) {

            // Construir HTML del email
            $htmlEmail = $this->buildEmailHtml(
                $alumno->usua_paterno.' '.$alumno->usua_nombres,
                $asunto,
                $cuerpo,
                $grupo->grup_nombre,
                $grupo->curs_nombre,
                $grupo->curs_nivel,
                $grupo->prof_paterno.' '.$grupo->prof_nombres,
                $grupo->ases_paterno.' '.$grupo->ases_nombres
            );

            try {
                $emailSvc->clear();
                $emailSvc->setTo($alumno->usua_email, $alumno->usua_paterno.' '.$alumno->usua_nombres);
                $emailSvc->setSubject('[CodePuno] '.$asunto);
                $emailSvc->setMessage($htmlEmail);

                // Reply-To al asesor si tiene email
                if ($remEmail) {
                    $emailSvc->setReplyTo($remEmail, $remNombre);
                }

                if ($emailSvc->send(false)) {
                    $totalOk++;
                    $enviados[] = $alumno->usua_email;
                } else {
                    $totalError++;
                    $fallidos[] = $alumno->usua_email;
                }
            } catch (\Exception $e) {
                $totalError++;
                $fallidos[] = $alumno->usua_email.' (error: '.substr($e->getMessage(),0,60).')';
            }
        }

        // Registrar en historial
        $db->table('grupo_emails')->insert([
            'grem_grup_ide'      => $grup_ide,
            'grem_usua_ide'      => $this->session->usua_ide,
            'grem_asunto'        => $asunto,
            'grem_cuerpo'        => $cuerpo,
            'grem_destinatarios' => json_encode([
                'enviados' => $enviados,
                'fallidos' => $fallidos,
            ]),
            'grem_total'      => $totalOk,
            'grem_errores'    => $totalError,
            'grem_create_at'  => date('Y-m-d H:i:s'),
        ]);

        // Respuesta detallada
        if ($totalOk > 0 && $totalError == 0) {
            $msg = "✅ Correo enviado a {$totalOk} alumno(s) exitosamente.";
        } elseif ($totalOk > 0 && $totalError > 0) {
            $msg = "⚠️ Enviado a {$totalOk} alumno(s). Fallaron {$totalError} (ver historial).";
        } else {
            $msg = "❌ No se pudo enviar ningún correo. Verifica la configuración SMTP.";
        }

        echo json_encode([
            'ok'      => $totalOk > 0,
            'msg'     => $msg,
            'enviados'=> $totalOk,
            'errores' => $totalError,
            'fallidos'=> $fallidos,
        ]);
    }

    // ─────────────────────────────────────
    // Helper: construir HTML del email
    // ─────────────────────────────────────
    private function buildEmailHtml(
        string $alumnoNombre,
        string $asunto,
        string $cuerpo,
        string $grupNombre,
        string $cursNombre,
        string $cursNivel,
        string $profNombre,
        string $asesNombre
    ): string {
        // Convertir saltos de línea en <br> y URLs en links
        $cuerpHtml = nl2br(htmlspecialchars($cuerpo));
        $cuerpHtml = preg_replace(
            '/(https?:\/\/[^\s<]+)/',
            '<a href="$1" style="color:#7c3aed;font-weight:600;">$1</a>',
            $cuerpHtml
        );

        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$asunto}</title>
</head>
<body style="margin:0;padding:0;background:#0d0f18;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#0d0f18;padding:32px 12px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  <!-- Header -->
  <tr>
    <td style="background:linear-gradient(135deg,#1e1b4b,#0d0f18);
               border-radius:12px 12px 0 0;
               padding:28px 32px;
               text-align:center;
               border-bottom:3px solid #7c3aed;">
      <div style="font-size:22px;font-weight:800;color:#a78bfa;letter-spacing:1px;">
        🎓 CodePuno
      </div>
      <div style="font-size:12px;color:#64748b;margin-top:4px;">
        Plataforma de cursos de programación · Puno
      </div>
    </td>
  </tr>

  <!-- Cuerpo -->
  <tr>
    <td style="background:#161826;padding:32px;">

      <p style="margin:0 0 8px 0;font-size:13px;color:#94a3b8;">
        Hola, <strong style="color:#e2e8f0;">{$alumnoNombre}</strong>
      </p>
      <p style="margin:0 0 24px 0;font-size:12px;color:#64748b;">
        Tu asesor se ha comunicado contigo a través de la plataforma.
      </p>

      <!-- Asunto destacado -->
      <div style="background:rgba(124,58,237,.12);border-left:4px solid #7c3aed;
                  border-radius:0 8px 8px 0;padding:12px 16px;margin-bottom:20px;">
        <div style="font-size:11px;color:#a78bfa;font-weight:600;text-transform:uppercase;
                    letter-spacing:.06em;margin-bottom:4px;">ASUNTO</div>
        <div style="font-size:16px;font-weight:700;color:#e2e8f0;">{$asunto}</div>
      </div>

      <!-- Mensaje -->
      <div style="background:#1e2035;border-radius:8px;padding:20px;
                  font-size:14px;line-height:1.7;color:#cbd5e1;margin-bottom:24px;">
        {$cuerpHtml}
      </div>

      <!-- Ficha del grupo -->
      <table width="100%" cellpadding="0" cellspacing="0"
             style="background:rgba(255,255,255,.04);border-radius:8px;
                    border:1px solid rgba(255,255,255,.08);margin-bottom:20px;">
        <tr>
          <td style="padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);">
            <span style="font-size:10px;color:#64748b;text-transform:uppercase;
                         font-weight:600;letter-spacing:.05em;">GRUPO DE ASESORÍA</span>
          </td>
        </tr>
        <tr>
          <td style="padding:12px 16px;">
            <table width="100%" cellpadding="4" cellspacing="0" style="font-size:13px;">
              <tr>
                <td style="color:#94a3b8;width:110px;">Grupo:</td>
                <td style="color:#e2e8f0;font-weight:600;">{$grupNombre}</td>
              </tr>
              <tr>
                <td style="color:#94a3b8;">Curso:</td>
                <td style="color:#e2e8f0;">{$cursNombre}
                  <span style="background:#1e3a5f;color:#60a5fa;font-size:10px;
                               padding:1px 7px;border-radius:99px;font-weight:600;margin-left:6px;">
                    {$cursNivel}
                  </span>
                </td>
              </tr>
              <tr>
                <td style="color:#94a3b8;">Profesor:</td>
                <td style="color:#e2e8f0;">{$profNombre}</td>
              </tr>
              <tr>
                <td style="color:#94a3b8;">Asesor:</td>
                <td style="color:#a78bfa;font-weight:600;">{$asesNombre}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

      <p style="font-size:11px;color:#475569;line-height:1.5;margin:0;">
        Este mensaje fue enviado por tu asesor desde la plataforma CodePuno.<br>
        Si tienes dudas, ingresa a la plataforma y responde en el chat del grupo.
      </p>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td style="background:#0d0f18;border-radius:0 0 12px 12px;
               padding:16px 32px;text-align:center;
               border-top:1px solid rgba(255,255,255,.06);">
      <div style="font-size:11px;color:#334155;">
        CodePuno &copy; {$year} — Plataforma de cursos de programación · Puno, Perú
      </div>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
HTML;
    }

    // ─────────────────────────────────────
    // ADMIN: todos los grupos
    // ─────────────────────────────────────
    public function adminGrupos()
    {
        if ($this->session->perf_ide != 3) { echo "Sin acceso"; exit; }
        $db     = \Config\Database::connect();
        $grupos = $db->table('grupos_asesor g')
            ->select('g.grup_ide, g.grup_nombre, g.grup_create_at,
                      c.curs_nombre, c.curs_nivel,
                      au.usua_nombres as ases_nombres, au.usua_paterno as ases_paterno,
                      pu.usua_nombres as prof_nombres, pu.usua_paterno as prof_paterno,
                      (SELECT COUNT(*) FROM grupo_alumnos ga  WHERE ga.grua_grup_ide=g.grup_ide) as total_alumnos,
                      (SELECT COUNT(*) FROM grupo_mensajes gm WHERE gm.grum_grup_ide=g.grup_ide) as total_mensajes')
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',  'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',  'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide', 'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',  'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide', 'left')
            ->orderBy('g.grup_create_at','DESC')
            ->get()->getResult();

        echo view('asesor/vadmin_grupos', [
            'grupos'  => $grupos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }
}
