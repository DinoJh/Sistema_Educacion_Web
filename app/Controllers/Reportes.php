<?php
namespace App\Controllers;

class Reportes extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }

    public function progreso()
    {
        $db = \Config\Database::connect();
        $stats = [
            'total_alumnos'    => $db->table('usuarios')->where(['usua_perf_ide'=>1,'usua_esta_ide'=>1])->whereNull('usua_deleted_at')->countAllResults(),
            'total_profesores' => $db->table('usuarios')->where(['usua_perf_ide'=>2,'usua_esta_ide'=>1])->countAllResults(),
            'total_cursos'     => $db->table('cursos')->where('curs_esta_ide',1)->countAllResults(),
            'total_matriculas' => $db->table('matriculas')->where('matr_esta_ide',1)->countAllResults(),
            'total_completados'=> $db->table('matriculas')->where(['matr_esta_ide'=>1,'matr_completado'=>1])->countAllResults(),
        ];
        $cursos_pop = $db->table('cursos c')
            ->select('c.curs_nombre, COUNT(m.matr_ide) as total')
            ->join('matriculas m','m.matr_curs_ide=c.curs_ide','left')
            ->where('c.curs_esta_ide',1)
            ->groupBy('c.curs_ide')
            ->orderBy('total','DESC')->limit(5)->get()->getResult();

        $alumnos_prog = $db->table('usuarios u')
            ->select('u.usua_nombres, u.usua_paterno,
                COUNT(DISTINCT m.matr_curs_ide) as cursos_inscritos,
                SUM(m.matr_completado) as cursos_completados,
                COUNT(pr.prog_ide) as lecciones_vistas')
            ->join('matriculas m','m.matr_usua_ide=u.usua_ide','left')
            ->join('progreso pr','pr.prog_usua_ide=u.usua_ide','left')
            ->where('u.usua_perf_ide',1)->whereNull('u.usua_deleted_at')
            ->groupBy('u.usua_ide')->orderBy('lecciones_vistas','DESC')
            ->get()->getResult();

        echo view('reportes/vprogreso',[
            'stats'=>$stats,'cursos_pop'=>$cursos_pop,'alumnos_prog'=>$alumnos_prog,
            'base'=>base_url('public'),'session'=>$this->session
        ]);
    }
}
