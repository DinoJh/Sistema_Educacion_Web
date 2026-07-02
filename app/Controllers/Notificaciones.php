<?php
namespace App\Controllers;

/**
 * Controller Notificaciones
 * Endpoints AJAX usados por cp-widgets.js
 */
class Notificaciones extends BaseController
{
    public $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) {
            echo json_encode(['ok'=>false]); exit(0);
        }
    }

    // GET /notificaciones/count  → {"count": N}
    public function count()
    {
        $db = \Config\Database::connect();
        $n  = $db->table('notificaciones')
            ->where('noti_usua_ide', $this->session->usua_ide)
            ->where('noti_leida', 0)
            ->countAllResults();
        echo json_encode(['count' => (int)$n]);
    }

    // GET /notificaciones/lista  → {notifs: [...]}
    public function lista()
    {
        $db     = \Config\Database::connect();
        $notifs = $db->table('notificaciones')
            ->where('noti_usua_ide', $this->session->usua_ide)
            ->orderBy('noti_create_at', 'DESC')
            ->limit(25)
            ->get()->getResult();

        $out = [];
        foreach ($notifs as $n) {
            $out[] = [
                'ide'       => $n->noti_ide,
                'tipo'      => $n->noti_tipo,
                'titulo'    => $n->noti_titulo,
                'mensaje'   => $n->noti_mensaje,
                'link'      => $n->noti_link,
                'link_label'=> $n->noti_link_label,
                'leida'     => (bool)$n->noti_leida,
                'fecha'     => date('d/m/Y H:i', strtotime($n->noti_create_at)),
            ];
        }

        echo json_encode(['notifs' => $out]);
    }

    // POST /notificaciones/marcar  →  marca una o todas como leídas
    public function marcar()
    {
        $db  = \Config\Database::connect();
        $ide = $this->request->getPost('ide'); // null = marcar todas

        $q = $db->table('notificaciones')
            ->where('noti_usua_ide', $this->session->usua_ide);

        if ($ide && $ide !== 'todas') {
            $q->where('noti_ide', (int)$ide);
        }

        $q->update(['noti_leida' => 1]);

        echo json_encode(['ok' => true]);
    }
}
