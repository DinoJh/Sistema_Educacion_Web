<?php
namespace App\Controllers;
use App\Models\General;
use App\Libraries\Componente;

class Usuarios extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }

    public function alumnos()
    {
        $db = \Config\Database::connect();
        $alumnos = $db->table('usuarios u')
            ->select('u.usua_ide, u.usua_dni, u.usua_nombres, u.usua_paterno, u.usua_materno,
                u.usua_email, u.usua_celular, u.usua_user, e.esta_nombre, e.esta_clase,
                (SELECT COUNT(*) FROM matriculas m WHERE m.matr_usua_ide=u.usua_ide) as total_cursos')
            ->join('estados e','e.esta_ide=u.usua_esta_ide','left')
            ->where('u.usua_perf_ide', 1)
            ->where("u.usua_deleted_at IS NULL")
            ->orderBy('u.usua_paterno')
            ->get()->getResult();
        echo view('usuarios/valumnos', [
            'usuarios' => $alumnos,
            'base'     => base_url('public'),
            'session'  => $this->session
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
            ->join('estados e','e.esta_ide=u.usua_esta_ide','left')
            ->join('profesores p','p.prof_usua_ide=u.usua_ide','left')
            ->where('u.usua_perf_ide', 2)
            ->where("u.usua_deleted_at IS NULL")
            ->orderBy('u.usua_paterno')
            ->get()->getResult();
        echo view('usuarios/vprofesores', [
            'usuarios' => $profes,
            'base'     => base_url('public'),
            'session'  => $this->session
        ]);
    }

    public function misAlumnos()
    {
        $db   = \Config\Database::connect();
        $prof = $db->table('profesores')->where('prof_usua_ide', $this->session->usua_ide)->get()->getRow();
        if (!$prof) {
            echo view('usuarios/vmisalumnos', [
                'alumnos' => [],
                'base'    => base_url('public'),
                'session' => $this->session
            ]);
            return;
        }
        $alumnos = $db->table('matriculas m')
            ->select("u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_materno,
                u.usua_email, u.usua_celular,
                c.curs_nombre, c.curs_nivel, c.curs_ide,
                m.matr_completado, m.matr_fecha,
                (SELECT COUNT(*) FROM progreso pr
                    JOIN lecciones ll ON ll.lecc_ide=pr.prog_lecc_ide
                    WHERE pr.prog_usua_ide=u.usua_ide
                    AND ll.lecc_curs_ide=c.curs_ide
                    AND pr.prog_completado=1) as lecc_hechas,
                (SELECT COUNT(*) FROM lecciones ll2
                    WHERE ll2.lecc_curs_ide=c.curs_ide
                    AND ll2.lecc_esta_ide=1
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
            'session' => $this->session
        ]);
    }

    public function eliminar()
    {
        $ide = $this->request->getPost('ide');
        General::actualizar('usuarios', ['usua_ide'=>$ide], [
            'usua_deleted_at' => date('Y-m-d H:i:s'),
            'usua_esta_ide'   => 2
        ]);
        echo json_encode(['ok'=>true, 'msg'=>'Usuario eliminado.']);
    }

    public function cambiarEstado()
    {
        $ide  = $this->request->getPost('ide');
        $esta = $this->request->getPost('esta');
        General::actualizar('usuarios', ['usua_ide'=>$ide], ['usua_esta_ide'=>$esta]);
        echo json_encode(['ok'=>true]);
    }

    public function nuevo()
    {
        $perf = $this->request->getPost('perf_ide');
        $data = [
            'usua_perf_ide'  => $perf,
            'usua_dni'       => $this->request->getPost('dni'),
            'usua_nombres'   => strtoupper(trim($this->request->getPost('nombres'))),
            'usua_paterno'   => strtoupper(trim($this->request->getPost('paterno'))),
            'usua_materno'   => strtoupper(trim($this->request->getPost('materno'))),
            'usua_email'     => trim($this->request->getPost('email')),
            'usua_celular'   => trim($this->request->getPost('celular')),
            'usua_user'      => trim($this->request->getPost('user')),
            'usua_pass'      => $this->request->getPost('pass'),
            'usua_esta_ide'  => 1,
            'usua_create_at' => date('Y-m-d H:i:s'),
        ];
        $db     = \Config\Database::connect();
        $existe = $db->table('usuarios')
            ->where('usua_user', $data['usua_user'])
            ->orWhere('usua_email', $data['usua_email'])
            ->get()->getRow();
        if ($existe) {
            echo json_encode(['ok'=>false, 'msg'=>'Usuario o email ya existe.']);
            return;
        }
        General::insertar('usuarios', $data);
        $uid = $db->insertID();
        if ($perf == 2) {
            General::insertar('profesores', [
                'prof_usua_ide'  => $uid,
                'prof_esta_ide'  => 1,
                'prof_create_at' => date('Y-m-d H:i:s')
            ]);
        }
        echo json_encode(['ok'=>true, 'msg'=>'Usuario creado exitosamente.']);
    }
}
