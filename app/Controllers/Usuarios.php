<?php
namespace App\Controllers;
use App\Models\General;

class Usuarios extends BaseController
{
    public $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }

    // ── ALUMNOS — con UGEL y colegio ─────────────────────────────
    public function alumnos()
    {
        $db = \Config\Database::connect();
        $alumnos = $db->table('usuarios u')
            ->select('u.usua_ide, u.usua_dni, u.usua_nombres, u.usua_paterno, u.usua_materno,
                u.usua_email, u.usua_celular, u.usua_user, e.esta_nombre, e.esta_clase,
                ai.alui_sin_colegio, ai.alui_cole_texto,
                ug.ugel_nombre, ug.ugel_ciudad,
                co.cole_nombre,
                (SELECT COUNT(*) FROM matriculas m WHERE m.matr_usua_ide=u.usua_ide) as total_cursos')
            ->join('estados e',      'e.esta_ide=u.usua_esta_ide',    'left')
            ->join('alumno_info ai', 'ai.alui_usua_ide=u.usua_ide',  'left')
            ->join('ugeles ug',      'ug.ugel_ide=ai.alui_ugel_ide', 'left')
            ->join('colegios co',    'co.cole_ide=ai.alui_cole_ide',  'left')
            ->where('u.usua_perf_ide', 1)
            ->where("u.usua_deleted_at IS NULL")
            ->orderBy('u.usua_paterno')
            ->get()->getResult();

        echo view('usuarios/valumnos', [
            'usuarios' => $alumnos,
            'base'     => base_url('public'),
            'session'  => $this->session,
        ]);
    }

    public function profesores()
    {
        $db = \Config\Database::connect();
        $profes = $db->table('usuarios u')
            ->select('u.usua_ide, u.usua_dni, u.usua_nombres, u.usua_paterno, u.usua_materno,
                u.usua_email, u.usua_user, e.esta_nombre, e.esta_clase,
                p.prof_especialidad, p.prof_grado,
                (SELECT COUNT(*) FROM cursos c WHERE c.curs_prof_ide=p.prof_ide AND c.curs_esta_ide=1) as total_cursos')
            ->join('estados e',    'e.esta_ide=u.usua_esta_ide',  'left')
            ->join('profesores p', 'p.prof_usua_ide=u.usua_ide', 'left')
            ->where('u.usua_perf_ide', 2)
            ->where("u.usua_deleted_at IS NULL")
            ->orderBy('u.usua_paterno')
            ->get()->getResult();

        echo view('usuarios/vprofesores', [
            'usuarios' => $profes,
            'base'     => base_url('public'),
            'session'  => $this->session,
        ]);
    }

    public function asesores()
    {
        if ($this->session->perf_ide != 3) { echo "Sin acceso"; exit(0); }
        $db = \Config\Database::connect();
        $asesores = $db->table('usuarios u')
            ->select('u.usua_ide, u.usua_dni, u.usua_nombres, u.usua_paterno, u.usua_materno,
                u.usua_email, u.usua_celular, u.usua_user, e.esta_nombre, e.esta_clase,
                (SELECT COUNT(*) FROM grupos_asesor ga
                    JOIN asesores a ON a.ases_ide=ga.grup_ases_ide
                    WHERE a.ases_usua_ide=u.usua_ide) as total_grupos,
                (SELECT COUNT(*) FROM grupo_alumnos grua
                    JOIN grupos_asesor ga2 ON ga2.grup_ide=grua.grua_grup_ide
                    JOIN asesores a2 ON a2.ases_ide=ga2.grup_ases_ide
                    WHERE a2.ases_usua_ide=u.usua_ide) as total_alumnos_asesorados')
            ->join('estados e','e.esta_ide=u.usua_esta_ide','left')
            ->where('u.usua_perf_ide', 4)
            ->where("u.usua_deleted_at IS NULL")
            ->orderBy('u.usua_paterno')
            ->get()->getResult();

        echo view('usuarios/vasesores', [
            'usuarios' => $asesores,
            'base'     => base_url('public'),
            'session'  => $this->session,
        ]);
    }

    public function misAlumnos()
    {
        $db   = \Config\Database::connect();
        $prof = $db->table('profesores')->where('prof_usua_ide', $this->session->usua_ide)->get()->getRow();
        if (!$prof) {
            echo view('usuarios/vmisalumnos', ['alumnos'=>[],'base'=>base_url('public'),'session'=>$this->session]);
            return;
        }
        $alumnos = $db->table('matriculas m')
            ->select("u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_materno,
                u.usua_email, u.usua_celular,
                c.curs_nombre, c.curs_nivel, c.curs_ide,
                m.matr_completado, m.matr_fecha,
                (SELECT COUNT(*) FROM progreso pr
                    JOIN lecciones ll ON ll.lecc_ide=pr.prog_lecc_ide
                    WHERE pr.prog_usua_ide=u.usua_ide AND ll.lecc_curs_ide=c.curs_ide
                    AND pr.prog_completado=1) as lecc_hechas,
                (SELECT COUNT(*) FROM lecciones ll2
                    WHERE ll2.lecc_curs_ide=c.curs_ide AND ll2.lecc_esta_ide=1
                    AND ll2.lecc_tipo != 'QUIZ') as total_lecc")
            ->join('usuarios u','u.usua_ide=m.matr_usua_ide')
            ->join('cursos c','c.curs_ide=m.matr_curs_ide')
            ->where('c.curs_prof_ide', $prof->prof_ide)
            ->where('m.matr_esta_ide', 1)
            ->orderBy('u.usua_paterno, c.curs_nombre')
            ->get()->getResult();

        echo view('usuarios/vmisalumnos', [
            'alumnos' => $alumnos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }

    public function eliminar()
    {
        $ide = $this->request->getPost('ide');
        General::actualizar('usuarios', ['usua_ide'=>$ide], [
            'usua_deleted_at' => date('Y-m-d H:i:s'),
            'usua_esta_ide'   => 2,
        ]);
        echo json_encode(['ok'=>true,'msg'=>'Usuario eliminado.']);
    }

    public function cambiarEstado()
    {
        $ide  = $this->request->getPost('ide');
        $esta = $this->request->getPost('esta');
        General::actualizar('usuarios', ['usua_ide'=>$ide], ['usua_esta_ide'=>$esta]);
        echo json_encode(['ok'=>true]);
    }

    // ══════════════════════════════════════════════════════════════
    //  NUEVO USUARIO — con DNI como usuario + email de bienvenida
    // ══════════════════════════════════════════════════════════════
    public function nuevo()
    {
        $perf        = (int)$this->request->getPost('perf_ide');
        $dni         = trim($this->request->getPost('dni') ?? '');
        $nombres     = strtoupper(trim($this->request->getPost('nombres') ?? ''));
        $paterno     = strtoupper(trim($this->request->getPost('paterno') ?? ''));
        $materno     = strtoupper(trim($this->request->getPost('materno') ?? ''));
        $email       = trim($this->request->getPost('email') ?? '');
        $celular     = trim($this->request->getPost('celular') ?? '');
        $pass        = trim($this->request->getPost('pass') ?? '');
        $enviarEmail = (int)($this->request->getPost('enviar_email') ?? 0);

        // Para alumnos el usuario es el DNI; si no hay DNI usa el campo user manual
        $userManual = trim($this->request->getPost('user') ?? '');
        $user       = ($perf == 1 && $dni) ? $dni : $userManual;

        if (!$nombres || !$user || !$pass) {
            echo json_encode(['ok'=>false,'msg'=>'Faltan campos obligatorios: nombres, usuario y contraseña.']);
            return;
        }
        if ($perf == 1 && !$dni) {
            echo json_encode(['ok'=>false,'msg'=>'El DNI es obligatorio para alumnos.']);
            return;
        }

        $db = \Config\Database::connect();

        // Verificar duplicados
        $existeUser = $db->table('usuarios')->where('usua_user', $user)->countAllResults();
        if ($existeUser) {
            echo json_encode(['ok'=>false,'msg'=>'El usuario "'.$user.'" ya existe. Si el DNI ya fue registrado, verifica la lista.']);
            return;
        }
        if ($email) {
            $existeEmail = $db->table('usuarios')->where('usua_email', $email)->countAllResults();
            if ($existeEmail) {
                echo json_encode(['ok'=>false,'msg'=>'El email "'.$email.'" ya está registrado.']);
                return;
            }
        }
        if ($dni) {
            $existeDni = $db->table('usuarios')->where('usua_dni', $dni)->countAllResults();
            if ($existeDni) {
                echo json_encode(['ok'=>false,'msg'=>'El DNI "'.$dni.'" ya está registrado.']);
                return;
            }
        }

        // Insertar usuario
        $data = [
            'usua_perf_ide'  => $perf,
            'usua_dni'       => $dni ?: null,
            'usua_nombres'   => $nombres,
            'usua_paterno'   => $paterno,
            'usua_materno'   => $materno,
            'usua_email'     => $email ?: null,
            'usua_celular'   => $celular ?: null,
            'usua_user'      => $user,
            'usua_pass'      => $pass,
            'usua_esta_ide'  => 1,
            'usua_create_at' => date('Y-m-d H:i:s'),
        ];
        General::insertar('usuarios', $data);
        $uid = $db->insertID();

        // Registro en tabla de perfil según perfil
        if ($perf == 2) {
            General::insertar('profesores', ['prof_usua_ide'=>$uid,'prof_esta_ide'=>1,'prof_create_at'=>date('Y-m-d H:i:s')]);
        } elseif ($perf == 4) {
            General::insertar('asesores', ['ases_usua_ide'=>$uid,'ases_esta_ide'=>1,'ases_create_at'=>date('Y-m-d H:i:s')]);
        } elseif ($perf == 1) {
            // alumno_info vacío (no tiene UGEL porque lo creó el admin)
            $db->table('alumno_info')->insert([
                'alui_usua_ide'    => $uid,
                'alui_sin_colegio' => 1, // por defecto independiente; el alumno puede actualizar
                'alui_create_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        // ── Enviar correo de bienvenida al alumno ──
        $emailResult = null;
        if ($perf == 1 && $enviarEmail && $email) {
            $emailResult = $this->enviarBienvenida($uid, $nombres.' '.$paterno, $email, $user, $pass);
        }

        $msg = 'Usuario "'.$nombres.' '.$paterno.'" creado correctamente.';
        if ($emailResult === true) {
            $msg .= ' Se envió el correo de bienvenida a '.$email.'.';
        } elseif ($emailResult === false) {
            $msg .= ' ⚠️ No se pudo enviar el correo (verifica la config SMTP).';
        }

        echo json_encode(['ok'=>true,'msg'=>$msg,'uid'=>$uid]);
    }

    // ── Helper: correo de bienvenida ─────────────────────────────
    private function enviarBienvenida($uid, $nombre, $email, $user, $pass): bool
    {
        $loginUrl  = base_url('/login');
        // Magic link: pre-rellena usuario y contraseña en la URL
        $magicLink = base_url('/login?u='.urlencode($user).'&p='.urlencode($pass));
        $year      = date('Y');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Bienvenido a CodePuno</title></head>
<body style="margin:0;padding:0;background:#0d0f18;font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#0d0f18;padding:32px 12px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  <tr><td style="background:linear-gradient(135deg,#1e1b4b,#0d0f18);border-radius:12px 12px 0 0;
                 padding:28px 32px;text-align:center;border-bottom:3px solid #7c3aed;">
    <div style="font-size:22px;font-weight:800;color:#a78bfa;">🎓 CodePuno</div>
    <div style="font-size:12px;color:#64748b;margin-top:4px;">Plataforma de cursos de programación · Puno</div>
  </td></tr>

  <tr><td style="background:#161826;padding:32px;">
    <p style="margin:0 0 8px 0;font-size:14px;color:#e2e8f0;">
      Hola, <strong>{$nombre}</strong> 👋
    </p>
    <p style="margin:0 0 24px 0;font-size:13px;color:#94a3b8;">
      Tu cuenta en <strong>CodePuno</strong> ha sido creada por el administrador. Aquí están tus datos de acceso:
    </p>

    <!-- Credenciales -->
    <div style="background:#1e2035;border-radius:10px;padding:20px;margin-bottom:24px;">
      <table width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;">
        <tr>
          <td style="color:#94a3b8;width:130px;">Usuario:</td>
          <td style="color:#a78bfa;font-weight:700;font-family:monospace;font-size:16px;">{$user}</td>
        </tr>
        <tr>
          <td style="color:#94a3b8;">Contraseña:</td>
          <td style="color:#10b981;font-weight:700;font-family:monospace;font-size:16px;">{$pass}</td>
        </tr>
        <tr>
          <td style="color:#94a3b8;">Plataforma:</td>
          <td><a href="{$loginUrl}" style="color:#60a5fa;">{$loginUrl}</a></td>
        </tr>
      </table>
    </div>

    <!-- Botón de acceso directo (magic link) -->
    <div style="text-align:center;margin-bottom:24px;">
      <a href="{$magicLink}"
         style="display:inline-block;background:linear-gradient(135deg,#7c3aed,#6d28d9);
                color:#fff;text-decoration:none;padding:14px 32px;
                border-radius:10px;font-weight:700;font-size:15px;
                box-shadow:0 6px 20px rgba(124,58,237,.4);">
        🚀 Ingresar a CodePuno
      </a>
      <div style="font-size:11px;color:#475569;margin-top:8px;">
        Este botón ya tiene tu usuario y contraseña listos.
      </div>
    </div>

    <!-- Instrucciones -->
    <div style="background:rgba(16,185,129,.08);border-left:3px solid #10b981;border-radius:0 8px 8px 0;padding:12px 16px;margin-bottom:20px;">
      <div style="font-size:12px;color:#6ee7b7;font-weight:600;margin-bottom:6px;">📋 PASOS PARA INGRESAR</div>
      <ol style="margin:0;padding-left:18px;font-size:13px;color:#cbd5e1;line-height:1.8;">
        <li>Haz clic en el botón verde <strong>"Ingresar a CodePuno"</strong> de arriba.</li>
        <li>Verifica que tu usuario (<code style="color:#a78bfa;">{$user}</code>) y contraseña estén ingresados.</li>
        <li>Haz clic en <strong>"Ingresa al sistema"</strong>.</li>
        <li>Una vez dentro, te recomendamos <strong>cambiar tu contraseña</strong> desde <em>Mi Cuenta → Mi Perfil</em>.</li>
      </ol>
    </div>

    <p style="font-size:11px;color:#475569;line-height:1.5;margin:0;">
      Si tienes algún problema para ingresar, contacta al administrador de la plataforma.
    </p>
  </td></tr>

  <tr><td style="background:#0d0f18;border-radius:0 0 12px 12px;padding:16px 32px;text-align:center;border-top:1px solid rgba(255,255,255,.06);">
    <div style="font-size:11px;color:#334155;">CodePuno &copy; {$year} — Puno, Perú</div>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>
HTML;

        try {
            $emailSvc = \Config\Services::email();
            $emailSvc->clear();
            $emailSvc->setTo($email, $nombre);
            $emailSvc->setSubject('[CodePuno] Bienvenido — Tus datos de acceso');
            $emailSvc->setMessage($html);
            return $emailSvc->send(false);
        } catch (\Exception $e) {
            log_message('error', 'Correo bienvenida fallo: '.$e->getMessage());
            return false;
        }
    }
}

    // POST /usuarios/reenviar-bienvenida — reenvía correo de acceso
    public function reenviarBienvenida()
    {
        if ($this->session->perf_ide != 3) {
            echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return;
        }
        $uid  = (int)$this->request->getPost('uid');
        $pass = trim($this->request->getPost('pass') ?? '');
        if (!$uid || !$pass) {
            echo json_encode(['ok'=>false,'msg'=>'Faltan datos.']); return;
        }
        $db      = \Config\Database::connect();
        $usuario = $db->table('usuarios')->where('usua_ide',$uid)->get()->getRow();
        if (!$usuario || !$usuario->usua_email) {
            echo json_encode(['ok'=>false,'msg'=>'El alumno no tiene email registrado.']); return;
        }
        $nombre   = $usuario->usua_nombres.' '.$usuario->usua_paterno;
        $resultado = $this->enviarBienvenida($uid, $nombre, $usuario->usua_email, $usuario->usua_user, $pass);
        if ($resultado) {
            echo json_encode(['ok'=>true,'msg'=>'Correo reenviado a '.$usuario->usua_email.' exitosamente.']);
        } else {
            echo json_encode(['ok'=>false,'msg'=>'No se pudo enviar el correo. Verifica la configuración SMTP.']);
        }
    }
}
