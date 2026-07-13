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

        $extra    = null;
        $aluInfo  = null;   // solo para alumnos

        if ($perf_ide == 2) {
            $extra = $db->table('profesores')->where('prof_usua_ide', $usua_ide)->get()->getRow();
        } elseif ($perf_ide == 1) {
            $extra   = $db->table('perfil_alumno')->where('pale_usua_ide', $usua_ide)->get()->getRow();
            // Datos de UGEL/colegio
            $aluInfo = $db->table('alumno_info ai')
                ->select('ai.*, u.ugel_nombre, u.ugel_ciudad, c.cole_nombre, c.cole_ciudad, ai.alui_cole_texto, ai.alui_sin_colegio')
                ->join('ugeles u',   'u.ugel_ide=ai.alui_ugel_ide', 'left')
                ->join('colegios c', 'c.cole_ide=ai.alui_cole_ide', 'left')
                ->where('ai.alui_usua_ide', $usua_ide)
                ->get()->getRow();
        }

        $view = $perf_ide == 2 ? 'perfil/vprofesor'
              : ($perf_ide == 3 ? 'perfil/vadmin' : 'perfil/valumno');

        echo view($view, [
            'usuario' => $usuario,
            'extra'   => $extra,
            'aluInfo' => $aluInfo,
            'session' => $this->session,
            'base'    => base_url('public'),
        ]);
    }

    // ─────────────────────────────────────────────────────
    // Ver perfil de CUALQUIER usuario (solo lectura) — AJAX
    // GET /perfil/ver/{usua_ide}
    // Solo accesible por ADMIN (perf_ide=3) o ASESOR (perf_ide=4)
    // ─────────────────────────────────────────────────────
    public function ver($usua_ide)
    {
        if (!in_array($this->session->perf_ide, [3, 4])) {
            echo json_encode(['ok' => false, 'html' => '<p>Sin acceso.</p>']);
            return;
        }

        $db      = \Config\Database::connect();
        $usuario = $db->table('usuarios u')
            ->select('u.*, p.perf_nombre')
            ->join('perfiles p', 'p.perf_ide=u.usua_perf_ide', 'left')
            ->where('u.usua_ide', (int)$usua_ide)
            ->get()->getRow();

        if (!$usuario) {
            echo json_encode(['ok' => false, 'html' => '<p>Usuario no encontrado.</p>']);
            return;
        }

        // Datos extra según perfil
        $extra   = null;
        $aluInfo = null;

        if ($usuario->usua_perf_ide == 2) {
            $extra = $db->table('profesores')->where('prof_usua_ide', $usua_ide)->get()->getRow();
        } elseif ($usuario->usua_perf_ide == 1) {
            $extra   = $db->table('perfil_alumno')->where('pale_usua_ide', $usua_ide)->get()->getRow();
            $aluInfo = $db->table('alumno_info ai')
                ->select('ai.*, u2.ugel_nombre, u2.ugel_ciudad, c.cole_nombre, c.cole_ciudad, ai.alui_cole_texto, ai.alui_sin_colegio')
                ->join('ugeles u2',  'u2.ugel_ide=ai.alui_ugel_ide', 'left')
                ->join('colegios c', 'c.cole_ide=ai.alui_cole_ide',  'left')
                ->where('ai.alui_usua_ide', $usua_ide)
                ->get()->getRow();
        } elseif ($usuario->usua_perf_ide == 4) {
            $extra = $db->table('asesores')->where('ases_usua_ide', $usua_ide)->get()->getRow();
        }

        // Estadísticas
        $stats = [];
        if ($usuario->usua_perf_ide == 1) {
            $stats['cursos'] = $db->table('matriculas')
                ->where('matr_usua_ide', $usua_ide)->where('matr_esta_ide', 1)->countAllResults();
            $stats['completados'] = $db->table('matriculas')
                ->where('matr_usua_ide', $usua_ide)->where('matr_completado', 1)->countAllResults();
            $stats['lecciones'] = $db->table('progreso')
                ->where('prog_usua_ide', $usua_ide)->where('prog_completado', 1)->countAllResults();
        } elseif ($usuario->usua_perf_ide == 2) {
            $stats['cursos'] = $db->table('cursos c')
                ->join('profesores p','p.prof_ide=c.curs_prof_ide')
                ->where('p.prof_usua_ide', $usua_ide)->where('c.curs_esta_ide', 1)->countAllResults();
            $stats['alumnos'] = $db->table('matriculas m')
                ->join('cursos c','c.curs_ide=m.matr_curs_ide')
                ->join('profesores p','p.prof_ide=c.curs_prof_ide')
                ->where('p.prof_usua_ide', $usua_ide)->countAllResults();
        } elseif ($usuario->usua_perf_ide == 4) {
            $stats['grupos'] = $db->table('grupos_asesor ga')
                ->join('asesores a','a.ases_ide=ga.grup_ases_ide')
                ->where('a.ases_usua_ide', $usua_ide)->countAllResults();
            $stats['alumnos'] = $db->table('grupo_alumnos grua')
                ->join('grupos_asesor ga2','ga2.grup_ide=grua.grua_grup_ide')
                ->join('asesores a2','a2.ases_ide=ga2.grup_ases_ide')
                ->where('a2.ases_usua_ide', $usua_ide)->countAllResults();
        }

        $html = view('perfil/vmodal_ver', [
            'usuario' => $usuario,
            'extra'   => $extra,
            'aluInfo' => $aluInfo,
            'stats'   => $stats,
            'base'    => base_url('public'),
        ]);

        echo json_encode(['ok' => true, 'html' => $html, 'nombre' => $usuario->usua_paterno.' '.$usuario->usua_nombres]);
    }

    public function guardar()
    {
        $db       = \Config\Database::connect();
        $perf_ide = $this->session->perf_ide;
        $usua_ide = $this->session->usua_ide;
        $p        = $this->request->getPost();

        $dataUsuario = [
            'usua_nombres'   => strtoupper(trim($p['nombres'] ?? '')),
            'usua_paterno'   => strtoupper(trim($p['paterno'] ?? '')),
            'usua_materno'   => strtoupper(trim($p['materno'] ?? '')),
            'usua_dni'       => trim($p['dni'] ?? '') ?: null,
            'usua_celular'   => trim($p['celular'] ?? ''),
            'usua_email'     => trim($p['email'] ?? ''),
            'usua_update_at' => date('Y-m-d H:i:s'),
        ];
        $emailExiste = $db->table('usuarios')
            ->where('usua_email', $dataUsuario['usua_email'])
            ->where('usua_ide !=', $usua_ide)->countAllResults();
        if ($emailExiste) { echo json_encode(['ok'=>false,'msg'=>'Ese email ya está en uso por otro usuario.']); return; }
        $db->table('usuarios')->where('usua_ide', $usua_ide)->update($dataUsuario);

        if ($perf_ide == 2) {
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
            if ($existe) { $db->table('profesores')->where('prof_usua_ide', $usua_ide)->update($dataProf); }
            else { $dataProf['prof_usua_ide']=$usua_ide; $dataProf['prof_esta_ide']=1; $dataProf['prof_create_at']=date('Y-m-d H:i:s'); $db->table('profesores')->insert($dataProf); }
        } elseif ($perf_ide == 1) {
            $dataAlumno = [
                'pale_tipo'        => trim($p['tipo'] ?? 'OTRO'),
                'pale_institucion' => trim($p['institucion'] ?? ''),
                'pale_carrera'     => trim($p['carrera'] ?? ''),
                'pale_descripcion' => trim($p['descripcion'] ?? ''),
                'pale_update_at'   => date('Y-m-d H:i:s'),
            ];
            $existe = $db->table('perfil_alumno')->where('pale_usua_ide', $usua_ide)->countAllResults();
            if ($existe) { $db->table('perfil_alumno')->where('pale_usua_ide', $usua_ide)->update($dataAlumno); }
            else { $dataAlumno['pale_usua_ide']=$usua_ide; $dataAlumno['pale_create_at']=date('Y-m-d H:i:s'); $db->table('perfil_alumno')->insert($dataAlumno); }
        }

        $u = $db->table('usuarios')->where('usua_ide', $usua_ide)->get()->getRow();
        $this->session->set('datos', $u->usua_nombres.', '.$u->usua_paterno);
        echo json_encode(['ok'=>true,'msg'=>'Perfil actualizado correctamente.']);
    }

    public function cambiarPassword()
    {
        $db       = \Config\Database::connect();
        $usua_ide = $this->session->usua_ide;
        $actual   = trim($this->request->getPost('actual') ?? '');
        $nueva    = trim($this->request->getPost('nueva') ?? '');
        $repite   = trim($this->request->getPost('repite') ?? '');
        if (!$actual||!$nueva||!$repite) { echo json_encode(['ok'=>false,'msg'=>'Completa todos los campos.']); return; }
        if ($nueva !== $repite) { echo json_encode(['ok'=>false,'msg'=>'La nueva contraseña y la confirmación no coinciden.']); return; }
        if (strlen($nueva) < 3) { echo json_encode(['ok'=>false,'msg'=>'La contraseña debe tener al menos 3 caracteres.']); return; }
        $usuario = $db->table('usuarios')->where(['usua_ide'=>$usua_ide,'usua_pass'=>$actual])->get()->getRow();
        if (!$usuario) { echo json_encode(['ok'=>false,'msg'=>'La contraseña actual es incorrecta.']); return; }
        $db->table('usuarios')->where('usua_ide',$usua_ide)->update(['usua_pass'=>$nueva]);
        echo json_encode(['ok'=>true,'msg'=>'Contraseña cambiada correctamente.']);
    }
}
