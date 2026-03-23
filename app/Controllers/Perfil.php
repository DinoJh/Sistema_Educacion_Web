<?php
namespace App\Controllers;
use App\Models\General;

class Perfil extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }

    public function index()
    {
        $db       = \Config\Database::connect();
        $perf_ide = $this->session->perf_ide;
        $usua_ide = $this->session->usua_ide;

        $usuario = $db->table('usuarios')->where('usua_ide', $usua_ide)->get()->getRow();

        $extra = null;
        if ($perf_ide == 2) {
            // PROFESOR
            $extra = $db->table('profesores')->where('prof_usua_ide', $usua_ide)->get()->getRow();
        } elseif ($perf_ide == 1) {
            // ALUMNO — tabla perfil_alumno
            $extra = $db->table('perfil_alumno')->where('pale_usua_ide', $usua_ide)->get()->getRow();
        }

        $view = $perf_ide == 2 ? 'perfil/vprofesor'
              : ($perf_ide == 3 ? 'perfil/vadmin' : 'perfil/valumno');

        echo view($view, [
            'usuario' => $usuario,
            'extra'   => $extra,
            'session' => $this->session,
            'base'    => base_url('public'),
        ]);
    }

    public function guardar()
    {
        $db       = \Config\Database::connect();
        $perf_ide = $this->session->perf_ide;
        $usua_ide = $this->session->usua_ide;
        $p        = $this->request->getPost();

        // Datos básicos (todos los perfiles)
        $dataUsuario = [
            'usua_nombres' => strtoupper(trim($p['nombres'] ?? '')),
            'usua_paterno' => strtoupper(trim($p['paterno'] ?? '')),
            'usua_materno' => strtoupper(trim($p['materno'] ?? '')),
            'usua_celular' => trim($p['celular'] ?? ''),
            'usua_email'   => trim($p['email'] ?? ''),
            'usua_update_at' => date('Y-m-d H:i:s'),
        ];
        // Solo actualiza email si no está en uso por otro usuario
        $emailExiste = $db->table('usuarios')
            ->where('usua_email', $dataUsuario['usua_email'])
            ->where('usua_ide !=', $usua_ide)
            ->countAllResults();
        if ($emailExiste) {
            echo json_encode(['ok'=>false,'msg'=>'Ese email ya está en uso por otro usuario.']);
            return;
        }
        $db->table('usuarios')->where('usua_ide', $usua_ide)->update($dataUsuario);

        // Datos específicos por perfil
        if ($perf_ide == 2) {
            // PROFESOR
            $dataProf = [
                'prof_especialidad' => trim($p['especialidad'] ?? ''),
                'prof_grado'        => trim($p['grado'] ?? ''),
                'prof_area'         => trim($p['area'] ?? ''),
                'prof_biografia'    => trim($p['biografia'] ?? ''),
                'prof_web'          => trim($p['web'] ?? ''),
                'prof_linkedin'     => trim($p['linkedin'] ?? ''),
                'prof_youtube'      => trim($p['youtube'] ?? ''),
            ];
            $existe = $db->table('profesores')->where('prof_usua_ide', $usua_ide)->countAllResults();
            if ($existe) {
                $db->table('profesores')->where('prof_usua_ide', $usua_ide)->update($dataProf);
            } else {
                $dataProf['prof_usua_ide']  = $usua_ide;
                $dataProf['prof_esta_ide']  = 1;
                $dataProf['prof_create_at'] = date('Y-m-d H:i:s');
                $db->table('profesores')->insert($dataProf);
            }
        } elseif ($perf_ide == 1) {
            // ALUMNO
            $dataAlumno = [
                'pale_tipo'        => trim($p['tipo'] ?? 'OTRO'),
                'pale_institucion' => trim($p['institucion'] ?? ''),
                'pale_carrera'     => trim($p['carrera'] ?? ''),
                'pale_descripcion' => trim($p['descripcion'] ?? ''),
                'pale_update_at'   => date('Y-m-d H:i:s'),
            ];
            $existe = $db->table('perfil_alumno')->where('pale_usua_ide', $usua_ide)->countAllResults();
            if ($existe) {
                $db->table('perfil_alumno')->where('pale_usua_ide', $usua_ide)->update($dataAlumno);
            } else {
                $dataAlumno['pale_usua_ide']  = $usua_ide;
                $dataAlumno['pale_create_at'] = date('Y-m-d H:i:s');
                $db->table('perfil_alumno')->insert($dataAlumno);
            }
        }
        // Actualizar datos en sesión
        $u = $db->table('usuarios')->where('usua_ide', $usua_ide)->get()->getRow();
        $this->session->set('datos', $u->usua_nombres . ', ' . $u->usua_paterno);

        echo json_encode(['ok'=>true,'msg'=>'Perfil actualizado correctamente.']);
    }

    public function cambiarPassword()
    {
        $db       = \Config\Database::connect();
        $usua_ide = $this->session->usua_ide;
        $actual   = trim($this->request->getPost('actual') ?? '');
        $nueva    = trim($this->request->getPost('nueva') ?? '');
        $repite   = trim($this->request->getPost('repite') ?? '');

        if (!$actual || !$nueva || !$repite) {
            echo json_encode(['ok'=>false,'msg'=>'Completa todos los campos.']); return;
        }
        if ($nueva !== $repite) {
            echo json_encode(['ok'=>false,'msg'=>'La nueva contraseña y la confirmación no coinciden.']); return;
        }
        if (strlen($nueva) < 3) {
            echo json_encode(['ok'=>false,'msg'=>'La contraseña debe tener al menos 3 caracteres.']); return;
        }
        $usuario = $db->table('usuarios')
            ->where(['usua_ide'=>$usua_ide, 'usua_pass'=>$actual])->get()->getRow();
        if (!$usuario) {
            echo json_encode(['ok'=>false,'msg'=>'La contraseña actual es incorrecta.']); return;
        }
        $db->table('usuarios')->where('usua_ide', $usua_ide)->update(['usua_pass'=>$nueva]);
        echo json_encode(['ok'=>true,'msg'=>'Contraseña cambiada correctamente.']);
    }
}
