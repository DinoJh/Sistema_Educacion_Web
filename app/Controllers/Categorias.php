<?php
namespace App\Controllers;
use App\Models\General;

class Categorias extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }
    public function index()
    {
        $db = \Config\Database::connect();
        $cats = $db->table('categorias c')
            ->select('c.*, e.esta_nombre, e.esta_clase, (SELECT COUNT(*) FROM cursos cu WHERE cu.curs_cate_ide=c.cate_ide AND cu.curs_esta_ide=1) as total_cursos')
            ->join('estados e','e.esta_ide=c.cate_esta_ide','left')
            ->orderBy('c.cate_nombre')->get()->getResult();
        echo view('categorias/vcategorias',['categorias'=>$cats,'base'=>base_url('public')]);
    }
    public function guardar()
    {
        $ide = $this->request->getPost('ide');
        $data = ['cate_nombre'=>strtoupper(trim($this->request->getPost('nombre'))),'cate_icono'=>trim($this->request->getPost('icono')),'cate_esta_ide'=>1];
        if ($ide) General::actualizar('categorias',['cate_ide'=>$ide],$data);
        else General::insertar('categorias',$data);
        echo json_encode(['ok'=>true]);
    }
    public function eliminar()
    {
        General::actualizar('categorias',['cate_ide'=>$this->request->getPost('ide')],['cate_esta_ide'=>2]);
        echo json_encode(['ok'=>true]);
    }
}
