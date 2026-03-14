<?php
namespace App\Controllers;
use App\Models\General;

class Cursos extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }

    // Lista cursos: PROFESOR ve los suyos, ADMIN ve todos
    public function index()
    {
        $db = \Config\Database::connect();

        // Detectar si las columnas nuevas ya existen
        $cols = $db->getFieldNames('cursos');
        $tieneElimCol = in_array('curs_eliminado_por', $cols);

        if ($tieneElimCol) {
            $select = 'c.*, cat.cate_nombre, u.usua_nombres, u.usua_paterno,
                e.esta_nombre, e.esta_clase,
                ue.usua_nombres as elim_nombres, ue.usua_paterno as elim_paterno, pe.perf_nombre as elim_perfil,
                (SELECT COUNT(*) FROM secciones s WHERE s.secc_curs_ide=c.curs_ide) as total_secciones,
                (SELECT COUNT(*) FROM lecciones l WHERE l.lecc_curs_ide=c.curs_ide AND l.lecc_esta_ide=1) as total_lecciones,
                (SELECT COUNT(*) FROM matriculas m WHERE m.matr_curs_ide=c.curs_ide) as total_alumnos';
            $builder = $db->table('cursos c')
                ->select($select)
                ->join('categorias cat','cat.cate_ide=c.curs_cate_ide','left')
                ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
                ->join('usuarios u','u.usua_ide=p.prof_usua_ide','left')
                ->join('estados e','e.esta_ide=c.curs_esta_ide','left')
                ->join('usuarios ue','ue.usua_ide=c.curs_eliminado_por','left')
                ->join('perfiles pe','pe.perf_ide=ue.usua_perf_ide','left');
        } else {
            $select = 'c.*, cat.cate_nombre, u.usua_nombres, u.usua_paterno,
                e.esta_nombre, e.esta_clase,
                NULL as elim_nombres, NULL as elim_paterno, NULL as elim_perfil,
                (SELECT COUNT(*) FROM secciones s WHERE s.secc_curs_ide=c.curs_ide) as total_secciones,
                (SELECT COUNT(*) FROM lecciones l WHERE l.lecc_curs_ide=c.curs_ide AND l.lecc_esta_ide=1) as total_lecciones,
                (SELECT COUNT(*) FROM matriculas m WHERE m.matr_curs_ide=c.curs_ide) as total_alumnos';
            $builder = $db->table('cursos c')
                ->select($select)
                ->join('categorias cat','cat.cate_ide=c.curs_cate_ide','left')
                ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
                ->join('usuarios u','u.usua_ide=p.prof_usua_ide','left')
                ->join('estados e','e.esta_ide=c.curs_esta_ide','left');
        }

        if ($this->session->perf_ide == 2) {
            $prof = $db->table('profesores')->where('prof_usua_ide', $this->session->usua_ide)->get()->getRow();
            if ($prof) $builder->where('c.curs_prof_ide', $prof->prof_ide);
        }

        $builder->orderBy('c.curs_esta_ide ASC, c.curs_create_at DESC');
        $cursos = $builder->get()->getResult();

        $categorias = General::getData('*','categorias',['cate_esta_ide'=>1],'cate_nombre');
        echo view('cursos/vcursos', [
            'cursos'    => $cursos,
            'categorias'=> $categorias,
            'session'   => $this->session,
            'base'      => base_url('public')
        ]);
    }

    public function guardar()
    {
        $db   = \Config\Database::connect();
        $prof = $db->table('profesores')->where('prof_usua_ide', $this->session->usua_ide)->get()->getRow();
        if (!$prof) {
            General::insertar('profesores',['prof_usua_ide'=>$this->session->usua_ide,'prof_esta_ide'=>1,'prof_create_at'=>date('Y-m-d H:i:s')]);
            $prof = $db->table('profesores')->where('prof_usua_ide',$this->session->usua_ide)->get()->getRow();
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
        // curso_profesores - solo si la tabla existe
        $tablas = $db->listTables();
        if (in_array('curso_profesores', $tablas)) {
            General::insertar('curso_profesores',['cupr_curs_ide'=>$curs_ide,'cupr_prof_ide'=>$prof->prof_ide,'cupr_rol'=>'PRINCIPAL','cupr_create_at'=>date('Y-m-d H:i:s')]);
        }
        echo json_encode(['ok'=>true,'msg'=>'Curso creado exitosamente.','ide'=>$curs_ide]);
    }

    public function actualizar()
    {
        $ide  = $this->request->getPost('ide');
        $data = [
            'curs_cate_ide'    => $this->request->getPost('cate_ide'),
            'curs_nombre'      => trim($this->request->getPost('nombre')),
            'curs_descripcion' => trim($this->request->getPost('descripcion')),
            'curs_nivel'       => $this->request->getPost('nivel'),
            'curs_update_at'   => date('Y-m-d H:i:s'),
        ];
        General::actualizar('cursos',['curs_ide'=>$ide],$data);
        echo json_encode(['ok'=>true,'msg'=>'Curso actualizado.']);
    }

    public function eliminar()
    {
        $ide    = $this->request->getPost('ide');
        $motivo = trim($this->request->getPost('motivo') ?? '');
        $db     = \Config\Database::connect();
        $cols   = $db->getFieldNames('cursos');
        $data   = ['curs_esta_ide'=>2,'curs_update_at'=>date('Y-m-d H:i:s')];
        if (in_array('curs_eliminado_por', $cols)) {
            $data['curs_eliminado_por'] = $this->session->usua_ide;
            $data['curs_motivo_baja']   = $motivo ?: 'Sin motivo especificado';
        }
        General::actualizar('cursos',['curs_ide'=>$ide],$data);
        echo json_encode(['ok'=>true,'msg'=>'Curso eliminado.']);
    }

    public function restaurar()
    {
        $ide  = $this->request->getPost('ide');
        $db   = \Config\Database::connect();
        $cols = $db->getFieldNames('cursos');
        $data = ['curs_esta_ide'=>1,'curs_update_at'=>date('Y-m-d H:i:s')];
        if (in_array('curs_eliminado_por', $cols)) {
            $data['curs_eliminado_por'] = null;
            $data['curs_motivo_baja']   = null;
        }
        General::actualizar('cursos',['curs_ide'=>$ide],$data);
        echo json_encode(['ok'=>true,'msg'=>'Curso restaurado.']);
    }

    public function ver($ide)
    {
        $db = \Config\Database::connect();
        $curso = $db->table('cursos c')
            ->select('c.*, cat.cate_nombre, u.usua_nombres, u.usua_paterno')
            ->join('categorias cat','cat.cate_ide=c.curs_cate_ide','left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
            ->join('usuarios u','u.usua_ide=p.prof_usua_ide','left')
            ->where('c.curs_ide',$ide)->get()->getRow();

        $secciones = $db->table('secciones')
            ->where('secc_curs_ide',$ide)->orderBy('secc_orden')->get()->getResult();

        $lecciones = $db->table('lecciones')
            ->where('lecc_curs_ide',$ide)
            ->where('lecc_esta_ide',1)
            ->whereNotIn('lecc_tipo',['QUIZ'])
            ->orderBy('lecc_secc_ide, lecc_orden')->get()->getResult();

        $categorias = General::getData('*','categorias',['cate_esta_ide'=>1],'cate_nombre');

        echo view('cursos/vvercurso',[
            'curso'     => $curso,
            'secciones' => $secciones,
            'lecciones' => $lecciones,
            'categorias'=> $categorias,
            'base'      => base_url('public'),
            'session'   => $this->session
        ]);
    }
}
