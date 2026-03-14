<?php
namespace App\Controllers;
use App\Models\General;

class Lecciones extends BaseController
{
    public $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) { echo "inactivo"; exit(0); }
    }

    public function index()
    {
        // Devuelve el listado de cursos del profesor para elegir
        $db   = \Config\Database::connect();
        $prof = $db->table('profesores')->where('prof_usua_ide', $this->session->usua_ide)->get()->getRow();
        $cursos = $prof ? General::getData('*', 'cursos', ['curs_prof_ide' => $prof->prof_ide, 'curs_esta_ide' => 1], 'curs_nombre') : [];
        echo view('lecciones/vlecciones', ['cursos' => $cursos, 'base' => base_url('public')]);
    }

    public function guardar()
    {
        $ide = $this->request->getPost('ide');
        $data = [
            'lecc_curs_ide'    => $this->request->getPost('curs_ide'),
            'lecc_secc_ide'    => $this->request->getPost('secc_ide') ?: null,
            'lecc_titulo'      => trim($this->request->getPost('titulo')),
            'lecc_descripcion' => trim($this->request->getPost('descripcion')),
            'lecc_tipo'        => $this->request->getPost('tipo'),
            'lecc_url'         => trim($this->request->getPost('url')),
            'lecc_contenido'   => $this->request->getPost('contenido'),
            'lecc_archivo_url' => trim($this->request->getPost('archivo_url')),
            'lecc_es_preview'  => $this->request->getPost('es_preview') ? 1 : 0,
            'lecc_orden'       => (int)$this->request->getPost('orden'),
            'lecc_duracion'    => $this->request->getPost('duracion') ?: null,
            'lecc_esta_ide'    => 1,
        ];
        if ($ide) {
            General::actualizar('lecciones', ['lecc_ide' => $ide], $data);
            echo json_encode(['ok' => true, 'msg' => 'Lección actualizada.']);
        } else {
            $data['lecc_create_at'] = date('Y-m-d H:i:s');
            General::insertar('lecciones', $data);
            echo json_encode(['ok' => true, 'msg' => 'Lección creada.']);
        }
    }

    public function eliminar()
    {
        General::actualizar('lecciones', ['lecc_ide' => $this->request->getPost('ide')], ['lecc_esta_ide' => 2]);
        echo json_encode(['ok' => true]);
    }
}
