<?php
namespace App\Controllers;
use App\Models\General;

class Panel extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }

    // Catálogo de cursos para alumno
    public function cursos()
    {
        $db = \Config\Database::connect();
        $cursos = $db->table('cursos c')
            ->select('c.*, cat.cate_nombre, u.usua_nombres, u.usua_paterno,
                (SELECT COUNT(*) FROM lecciones l WHERE l.lecc_curs_ide=c.curs_ide AND l.lecc_esta_ide=1) as total_lecciones,
                (SELECT COUNT(*) FROM matriculas m WHERE m.matr_curs_ide=c.curs_ide) as total_alumnos,
                (SELECT ROUND(AVG(rese_calificacion),1) FROM resenas r WHERE r.rese_curs_ide=c.curs_ide) as promedio_nota,
                (SELECT matr_ide FROM matriculas m2 WHERE m2.matr_curs_ide=c.curs_ide AND m2.matr_usua_ide='.$this->session->usua_ide.' LIMIT 1) as ya_matriculado')
            ->join('categorias cat','cat.cate_ide=c.curs_cate_ide','left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
            ->join('usuarios u','u.usua_ide=p.prof_usua_ide','left')
            ->where('c.curs_esta_ide', 1)
            ->orderBy('c.curs_create_at','DESC')
            ->get()->getResult();

        $categorias = General::getData('*','categorias',['cate_esta_ide'=>1],'cate_nombre');
        echo view('panel/vmiscursos', [
            'cursos'    => $cursos,
            'categorias'=> $categorias,
            'session'   => $this->session,
            'base'      => base_url('public')
        ]);
    }

    // Ver curso con video player
    public function ver($curs_ide)
    {
        $db = \Config\Database::connect();
        $curso = $db->table('cursos c')
            ->select('c.*, cat.cate_nombre, u.usua_nombres, u.usua_paterno, u.usua_email, p.prof_biografia, p.prof_especialidad')
            ->join('categorias cat','cat.cate_ide=c.curs_cate_ide','left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
            ->join('usuarios u','u.usua_ide=p.prof_usua_ide','left')
            ->where('c.curs_ide', $curs_ide)->get()->getRow();

        $secciones = $db->table('secciones')->where('secc_curs_ide',$curs_ide)->orderBy('secc_orden')->get()->getResult();
        $lecciones = $db->table('lecciones')->where('lecc_curs_ide',$curs_ide)->where('lecc_esta_ide',1)->orderBy('lecc_secc_ide, lecc_orden')->get()->getResult();

        $matricula = $db->table('matriculas')
            ->where(['matr_usua_ide'=>$this->session->usua_ide,'matr_curs_ide'=>$curs_ide])->get()->getRow();

        // Progreso del alumno
        $progreso = [];
        if ($matricula) {
            $prog = $db->table('progreso')->where('prog_usua_ide',$this->session->usua_ide)->get()->getResult();
            foreach ($prog as $p) $progreso[$p->prog_lecc_ide] = $p->prog_completado;
        }

        echo view('panel/vvercurso', [
            'curso'    => $curso,
            'secciones'=> $secciones,
            'lecciones'=> $lecciones,
            'matricula'=> $matricula,
            'progreso' => $progreso,
            'session'  => $this->session,
            'base'     => base_url('public')
        ]);
    }

    public function matricular()
    {
        $curs_ide = $this->request->getPost('curs_ide');
        $usua_ide = $this->session->usua_ide;
        $db = \Config\Database::connect();
        $existe = $db->table('matriculas')
            ->where(['matr_usua_ide'=>$usua_ide,'matr_curs_ide'=>$curs_ide])->get()->getRow();
        if (!$existe) {
            General::insertar('matriculas',[
                'matr_usua_ide'=>$usua_ide,
                'matr_curs_ide'=>$curs_ide,
                'matr_fecha'=>date('Y-m-d H:i:s'),
                'matr_esta_ide'=>1
            ]);
        }
        echo json_encode(['ok'=>true,'msg'=>'Te has matriculado exitosamente.']);
    }

    public function marcar()
    {
        $lecc_ide  = $this->request->getPost('lecc_ide');
        $usua_ide  = $this->session->usua_ide;
        $completado= (int)$this->request->getPost('completado');
        $db = \Config\Database::connect();
        $existe = $db->table('progreso')->where(['prog_usua_ide'=>$usua_ide,'prog_lecc_ide'=>$lecc_ide])->get()->getRow();
        if ($existe) {
            General::actualizar('progreso',['prog_ide'=>$existe->prog_ide],['prog_completado'=>$completado,'prog_update_at'=>date('Y-m-d H:i:s')]);
        } else {
            General::insertar('progreso',['prog_usua_ide'=>$usua_ide,'prog_lecc_ide'=>$lecc_ide,'prog_completado'=>$completado,'prog_fecha'=>date('Y-m-d H:i:s')]);
        }
        // Verificar si completó el curso
        $lecc = $db->table('lecciones')->where('lecc_ide',$lecc_ide)->get()->getRow();
        if ($lecc) {
            $total = $db->table('lecciones')->where(['lecc_curs_ide'=>$lecc->lecc_curs_ide,'lecc_esta_ide'=>1])->countAllResults();
            $hechas= $db->table('progreso p')
                ->join('lecciones l','l.lecc_ide=p.prog_lecc_ide')
                ->where(['p.prog_usua_ide'=>$usua_ide,'l.lecc_curs_ide'=>$lecc->lecc_curs_ide,'p.prog_completado'=>1])->countAllResults();
            if ($total > 0 && $hechas >= $total) {
                General::actualizar('matriculas',['matr_usua_ide'=>$usua_ide,'matr_curs_ide'=>$lecc->lecc_curs_ide],['matr_completado'=>1,'matr_fecha_completado'=>date('Y-m-d H:i:s')]);
            }
        }
        echo json_encode(['ok'=>true]);
    }

    public function progreso()
    {
        $db = \Config\Database::connect();
        $usua_ide = $this->session->usua_ide;
        $matriculas = $db->table('matriculas m')
            ->select('m.*, c.curs_nombre, c.curs_nivel, c.curs_ide,
                (SELECT COUNT(*) FROM lecciones l WHERE l.lecc_curs_ide=c.curs_ide AND l.lecc_esta_ide=1) as total_lecc,
                (SELECT COUNT(*) FROM progreso pr JOIN lecciones ll ON ll.lecc_ide=pr.prog_lecc_ide WHERE pr.prog_usua_ide=m.matr_usua_ide AND ll.lecc_curs_ide=c.curs_ide AND pr.prog_completado=1) as lecc_hechas,
                u2.usua_nombres as prof_nombre, u2.usua_paterno as prof_paterno')
            ->join('cursos c','c.curs_ide=m.matr_curs_ide')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
            ->join('usuarios u2','u2.usua_ide=p.prof_usua_ide','left')
            ->where('m.matr_usua_ide',$usua_ide)
            ->orderBy('m.matr_fecha','DESC')
            ->get()->getResult();

        echo view('panel/vprogreso',['matriculas'=>$matriculas,'session'=>$this->session,'base'=>base_url('public')]);
    }
}
