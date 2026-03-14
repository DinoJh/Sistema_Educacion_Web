<?php
namespace App\Controllers;
use App\Models\General;
use App\Libraries\Componente;

class Cursos extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) {
            echo "inactivo"; exit(0);
        }
    }

    // Lista cursos (PROFESOR ve los suyos, ADMIN ve todos)
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('cursos c');
        $builder->select('c.*, cat.cate_nombre, u.usua_nombres, u.usua_paterno,
            (SELECT COUNT(*) FROM secciones s WHERE s.secc_curs_ide = c.curs_ide) as total_secciones,
            (SELECT COUNT(*) FROM lecciones l WHERE l.lecc_curs_ide = c.curs_ide) as total_lecciones,
            (SELECT COUNT(*) FROM matriculas m WHERE m.matr_curs_ide = c.curs_ide) as total_alumnos');
        $builder->join('categorias cat', 'cat.cate_ide = c.curs_cate_ide', 'left');
        $builder->join('profesores p', 'p.prof_ide = c.curs_prof_ide', 'left');
        $builder->join('usuarios u', 'u.usua_ide = p.prof_usua_ide', 'left');

        if ($this->session->perf_ide == 2) { // PROFESOR
            $prof = $db->table('profesores')->where('prof_usua_ide', $this->session->usua_ide)->get()->getRow();
            if ($prof) $builder->where('c.curs_prof_ide', $prof->prof_ide);
        }
        $builder->orderBy('c.curs_create_at', 'DESC');
        $cursos = $builder->get()->getResult();

        $categorias = General::getData('*', 'categorias', ['cate_esta_ide' => 1], 'cate_nombre');
        echo view('cursos/vcursos', [
            'cursos'    => $cursos,
            'categorias'=> $categorias,
            'session'   => $this->session,
            'base'      => base_url('public')
        ]);
    }

    public function crear()
    {
        $categorias = General::getData('*', 'categorias', ['cate_esta_ide' => 1], 'cate_nombre');
        echo view('cursos/vformcurso', [
            'curso'     => null,
            'categorias'=> $categorias,
            'base'      => base_url('public'),
            'modo'      => 'crear'
        ]);
    }

    public function guardar()
    {
        $db = \Config\Database::connect();
        // Obtener prof_ide del usuario logueado
        $prof = $db->table('profesores')->where('prof_usua_ide', $this->session->usua_ide)->get()->getRow();
        if (!$prof) {
            echo json_encode(['ok' => false, 'msg' => 'No tienes perfil de profesor registrado.']); return;
        }
        $data = [
            'curs_cate_ide'    => $this->request->getPost('cate_ide'),
            'curs_prof_ide'    => $prof->prof_ide,
            'curs_nombre'      => trim($this->request->getPost('nombre')),
            'curs_descripcion' => trim($this->request->getPost('descripcion')),
            'curs_nivel'       => $this->request->getPost('nivel'),
            'curs_esta_ide'    => 1,
            'curs_create_at'   => date('Y-m-d H:i:s'),
        ];
        General::insertar('cursos', $data);
        $curs_ide = $db->insertID();
        // Insertar en curso_profesores
        General::insertar('curso_profesores', [
            'cupr_curs_ide'  => $curs_ide,
            'cupr_prof_ide'  => $prof->prof_ide,
            'cupr_rol'       => 'PRINCIPAL',
            'cupr_create_at' => date('Y-m-d H:i:s')
        ]);
        echo json_encode(['ok' => true, 'msg' => 'Curso creado exitosamente.', 'ide' => $curs_ide]);
    }

    public function editar($ide)
    {
        $curso = General::getData('c.*, cat.cate_nombre', 'cursos c',
            ['c.curs_ide' => $ide], 'c.curs_ide',
            false, ['categorias cat', 'cat.cate_ide = c.curs_cate_ide', 'left']);
        $categorias = General::getData('*', 'categorias', ['cate_esta_ide' => 1], 'cate_nombre');
        echo view('cursos/vformcurso', [
            'curso'     => $curso ? $curso[0] : null,
            'categorias'=> $categorias,
            'base'      => base_url('public'),
            'modo'      => 'editar'
        ]);
    }

    public function actualizar()
    {
        $ide = $this->request->getPost('ide');
        $data = [
            'curs_cate_ide'    => $this->request->getPost('cate_ide'),
            'curs_nombre'      => trim($this->request->getPost('nombre')),
            'curs_descripcion' => trim($this->request->getPost('descripcion')),
            'curs_nivel'       => $this->request->getPost('nivel'),
            'curs_update_at'   => date('Y-m-d H:i:s'),
        ];
        General::actualizar('cursos', ['curs_ide' => $ide], $data);
        echo json_encode(['ok' => true, 'msg' => 'Curso actualizado.']);
    }

    public function eliminar()
    {
        $ide = $this->request->getPost('ide');
        General::actualizar('cursos', ['curs_ide' => $ide], ['curs_esta_ide' => 2]);
        echo json_encode(['ok' => true, 'msg' => 'Curso eliminado.']);
    }

    // Vista principal del curso con secciones + lecciones (PROFESOR gestiona aquí)
    public function ver($ide)
    {
        $db = \Config\Database::connect();
        $curso = $db->table('cursos c')
            ->select('c.*, cat.cate_nombre, u.usua_nombres, u.usua_paterno')
            ->join('categorias cat', 'cat.cate_ide = c.curs_cate_ide', 'left')
            ->join('profesores p', 'p.prof_ide = c.curs_prof_ide', 'left')
            ->join('usuarios u', 'u.usua_ide = p.prof_usua_ide', 'left')
            ->where('c.curs_ide', $ide)
            ->get()->getRow();

        $secciones = $db->table('secciones')
            ->where('secc_curs_ide', $ide)
            ->orderBy('secc_orden')->get()->getResult();

        $lecciones = $db->table('lecciones')
            ->where('lecc_curs_ide', $ide)
            ->where('lecc_esta_ide', 1)
            ->orderBy('lecc_secc_ide, lecc_orden')->get()->getResult();

        echo view('cursos/vvercurso', [
            'curso'    => $curso,
            'secciones'=> $secciones,
            'lecciones'=> $lecciones,
            'base'     => base_url('public'),
            'session'  => $this->session
        ]);
    }
}
