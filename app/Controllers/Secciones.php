<?php
namespace App\Controllers;
use App\Models\General;

class Secciones extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }

    public function guardar()
    {
        $curs_ide = $this->request->getPost('curs_ide');
        $nombre   = trim($this->request->getPost('nombre'));
        $ide      = $this->request->getPost('ide');
        if ($ide) {
            General::actualizar('secciones', ['secc_ide' => $ide], ['secc_nombre' => $nombre]);
        } else {
            $db = \Config\Database::connect();
            $max = $db->table('secciones')->selectMax('secc_orden','max_orden')
                ->where('secc_curs_ide', $curs_ide)->get()->getRow();
            General::insertar('secciones', [
                'secc_curs_ide' => $curs_ide,
                'secc_nombre'   => $nombre,
                'secc_orden'    => ($max->max_orden ?? 0) + 1
            ]);
        }
        echo json_encode(['ok' => true]);
    }

    public function eliminar()
    {
        $ide = $this->request->getPost('ide');
        $db  = \Config\Database::connect();
        // Soft delete en lecciones de esa sección
        $db->table('lecciones')->where('lecc_secc_ide', $ide)->update(['lecc_esta_ide' => 2]);
        $db->table('secciones')->where('secc_ide', $ide)->delete();
        echo json_encode(['ok' => true]);
    }
}
