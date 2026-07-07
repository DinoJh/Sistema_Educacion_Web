<?php
namespace App\Controllers;

use App\Models\Usuarios;
use App\Models\Generaldb;
use App\Libraries\Funciones;

class Login extends BaseController
{
    public function index()
    {
        $db     = \Config\Database::connect();
        $ugeles = $db->table('ugeles')->where('ugel_esta_ide', 1)->orderBy('ugel_nombre')->get()->getResult();

        $data = [
            'error'  => $this->request->getVar('error'),
            'exito'  => $this->request->getVar('exito'),
            'ugeles' => $ugeles,
            'fondo'  => 'fondo-dre-puno2.jpg',
            'logo'   => 'logo-drepuno.gif',
            'sigla'  => '',
        ];
        return view('login/vlogin', $data);
    }

    public function verificar()
    {
        $where = [
            'usua_user'     => $this->request->getPost('user'),
            'usua_pass'     => $this->request->getPost('pass'),
            'usua_esta_ide' => 1,
        ];
        $objeto   = new Usuarios();
        $usuarios = $objeto->where($where)->get()->getResult();

        if (count($usuarios) == 1) {
            $fecha = Funciones::get_ahora_fecha();
            $data_session = [
                'login'     => md5('L0g¡NS!st3M4'),
                'perf_ide'  => $usuarios[0]->usua_perf_ide,
                'usua_ide'  => $usuarios[0]->usua_ide,
                'datos'     => $usuarios[0]->usua_nombres . ', ' . $usuarios[0]->usua_paterno . ' ' . $usuarios[0]->usua_materno,
                'usuario'   => $usuarios[0]->usua_user,
                'siglas'    => 'CodePuno',
                'icono'     => 'ti-bar-chart-alt',
                'ini_fecha' => Funciones::get_fecha_letras($fecha),
                'ini_hora'  => Funciones::get_ahora_hora(),
            ];

            $login = [
                'logi_usua_ide' => $usuarios[0]->usua_ide,
                'logi_user'     => $usuarios[0]->usua_user,
                'logi_pass'     => $usuarios[0]->usua_pass,
                'logi_accedio'  => 'SI',
                'logi_datos'    => $data_session['datos'],
                'logi_create_at'=> Funciones::get_ahora(),
            ];
            $general = new Generaldb();
            $general->insertData('logins', $login);

            $session = \Config\Services::session();
            $session->set($data_session);
            return redirect()->to(base_url('/application'));
        } else {
            $login = [
                'logi_usua_ide' => null,
                'logi_user'     => $this->request->getPost('user'),
                'logi_pass'     => $this->request->getPost('pass'),
                'logi_accedio'  => 'NO',
                'logi_datos'    => '',
                'logi_create_at'=> Funciones::get_ahora(),
            ];
            $general = new Generaldb();
            $general->insertData('logins', $login);
            return redirect()->to(base_url('/login?error=true'));
        }
    }

    // ─────────────────────────────────────────
    // AJAX: colegios según UGEL seleccionada
    // GET /login/colegios/{ugel_ide}
    // ─────────────────────────────────────────
    public function colegios($ugel_ide)
    {
        $db      = \Config\Database::connect();
        $colegios = $db->table('colegios')
            ->where('cole_ugel_ide', (int)$ugel_ide)
            ->where('cole_esta_ide', 1)
            ->orderBy('cole_nombre')
            ->get()->getResult();

        $out = [];
        foreach ($colegios as $c) {
            $out[] = ['ide' => $c->cole_ide, 'nombre' => $c->cole_nombre, 'ciudad' => $c->cole_ciudad];
        }
        echo json_encode($out);
    }

    // ─────────────────────────────────────────
    // AJAX: POST /login/registrar — auto-registro de alumno
    // ─────────────────────────────────────────
    public function registrar()
    {
        $db = \Config\Database::connect();

        $nombres = strtoupper(trim($this->request->getPost('nombres') ?? ''));
        $paterno = strtoupper(trim($this->request->getPost('paterno') ?? ''));
        $materno = strtoupper(trim($this->request->getPost('materno') ?? ''));
        $email   = trim($this->request->getPost('email') ?? '');
        $celular = trim($this->request->getPost('celular') ?? '');
        $user    = trim($this->request->getPost('user') ?? '');
        $pass    = trim($this->request->getPost('pass') ?? '');
        $pass2   = trim($this->request->getPost('pass2') ?? '');

        $sinColegio = (int)($this->request->getPost('sin_colegio') ?? 0);
        $ugelIde    = (int)($this->request->getPost('ugel_ide') ?? 0);
        $coleIde    = (int)($this->request->getPost('cole_ide') ?? 0);  // 0 = "Mi colegio no está"
        $coleTxt    = trim($this->request->getPost('cole_texto') ?? '');

        // ── Validaciones ──
        if (!$nombres || !$paterno || !$user || !$pass) {
            echo json_encode(['ok' => false, 'msg' => 'Completa los campos obligatorios: nombres, apellido paterno, usuario y contraseña.']);
            return;
        }
        if (strlen($pass) < 6) {
            echo json_encode(['ok' => false, 'msg' => 'La contraseña debe tener al menos 6 caracteres.']);
            return;
        }
        if ($pass !== $pass2) {
            echo json_encode(['ok' => false, 'msg' => 'Las contraseñas no coinciden.']);
            return;
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => false, 'msg' => 'El correo electrónico no tiene un formato válido.']);
            return;
        }

        // ── Verificar usuario/email duplicados ──
        $existeUser = $db->table('usuarios')->where('usua_user', $user)->countAllResults();
        if ($existeUser > 0) {
            echo json_encode(['ok' => false, 'msg' => 'El nombre de usuario "' . htmlspecialchars($user) . '" ya está en uso. Elige otro.']);
            return;
        }
        if ($email) {
            $existeEmail = $db->table('usuarios')->where('usua_email', $email)->countAllResults();
            if ($existeEmail > 0) {
                echo json_encode(['ok' => false, 'msg' => 'El correo "' . htmlspecialchars($email) . '" ya está registrado.']);
                return;
            }
        }

        // ── Validar info de colegio ──
        if (!$sinColegio) {
            if (!$ugelIde) {
                echo json_encode(['ok' => false, 'msg' => 'Selecciona tu UGEL o marca la opción "No soy alumno de colegio".']);
                return;
            }
            // Si eligió "Mi colegio no está en la lista", debe haber escrito el nombre
            if ($coleIde === 0 && !$coleTxt) {
                echo json_encode(['ok' => false, 'msg' => 'Escribe el nombre de tu colegio en el campo de texto.']);
                return;
            }
        }

        // ── Insertar usuario (perf_ide = 1 = ALUMNO) ──
        $db->table('usuarios')->insert([
            'usua_perf_ide'  => 1,
            'usua_nombres'   => $nombres,
            'usua_paterno'   => $paterno,
            'usua_materno'   => $materno,
            'usua_email'     => $email ?: null,
            'usua_celular'   => $celular ?: null,
            'usua_user'      => $user,
            'usua_pass'      => $pass,
            'usua_esta_ide'  => 1,
            'usua_create_at' => date('Y-m-d H:i:s'),
        ]);
        $usua_ide = $db->insertID();

        // ── Insertar info de colegio ──
        $db->table('alumno_info')->insert([
            'alui_usua_ide'    => $usua_ide,
            'alui_ugel_ide'    => (!$sinColegio && $ugelIde) ? $ugelIde : null,
            'alui_cole_ide'    => (!$sinColegio && $coleIde > 0) ? $coleIde : null,
            'alui_cole_texto'  => (!$sinColegio && $coleIde === 0 && $coleTxt) ? $coleTxt : null,
            'alui_sin_colegio' => $sinColegio ? 1 : 0,
            'alui_create_at'   => date('Y-m-d H:i:s'),
        ]);

        echo json_encode([
            'ok'  => true,
            'msg' => '¡Cuenta creada exitosamente! Ya puedes ingresar con tu usuario y contraseña.',
        ]);
    }
}
