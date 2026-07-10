<?php
namespace App\Controllers;
use App\Models\General;

/**
 * Controller Asesor — v2
 * Ahora incluye:
 *   - Acceso ALUMNO al chat del grupo (si pertenece a él)
 *   - Notificaciones al formar grupo y al enviar mensaje
 *   - gruposAlumno(): lista de grupos para el alumno
 */
class Asesor extends BaseController
{
    public $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->login != md5("L0g¡NS!st3M4")) {
            echo "inactivo"; exit(0);
        }
        // Permitir ALUMNO(1), ADMIN(3) y ASESOR(4)
        if (!in_array($this->session->perf_ide, [1, 3, 4])) {
            echo "Sin acceso"; exit(0);
        }
    }

    // ─────────────────────────────────────────
    // Helper: crear una notificación
    // ─────────────────────────────────────────
    private function crearNotificacion($usua_ide, $tipo, $titulo, $mensaje, $link = null, $link_label = null)
    {
        $db = \Config\Database::connect();
        $db->table('notificaciones')->insert([
            'noti_usua_ide'   => $usua_ide,
            'noti_tipo'       => $tipo,
            'noti_titulo'     => $titulo,
            'noti_mensaje'    => $mensaje,
            'noti_link'       => $link,
            'noti_link_label' => $link_label,
            'noti_leida'      => 0,
            'noti_create_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    // ─────────────────────────────────────────
    // ASESOR: listado de todos los cursos activos
    // ─────────────────────────────────────────
    public function cursos()
    {
        if (!in_array($this->session->perf_ide, [3, 4])) { echo "Sin acceso"; exit; }
        $db     = \Config\Database::connect();
        $cursos = $db->table('cursos c')
            ->select('c.curs_ide, c.curs_nombre, c.curs_descripcion, c.curs_nivel,
                      cat.cate_nombre, cat.cate_icono,
                      u.usua_nombres, u.usua_paterno, u.usua_materno,
                      (SELECT COUNT(*) FROM matriculas m WHERE m.matr_curs_ide=c.curs_ide AND m.matr_esta_ide=1) as total_alumnos')
            ->join('categorias cat', 'cat.cate_ide=c.curs_cate_ide', 'left')
            ->join('profesores p',   'p.prof_ide=c.curs_prof_ide',   'left')
            ->join('usuarios u',     'u.usua_ide=p.prof_usua_ide',   'left')
            ->where('c.curs_esta_ide', 1)
            ->orderBy('c.curs_nombre')
            ->get()->getResult();

        echo view('asesor/vcursos', [
            'cursos'  => $cursos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }

    // ─────────────────────────────────────────
    // ASESOR: alumnos de un curso con estado de asesoría
    // ─────────────────────────────────────────
    public function alumnos($curs_ide)
    {
        if (!in_array($this->session->perf_ide, [3, 4])) { echo "Sin acceso"; exit; }
        $db    = \Config\Database::connect();
        $curso = $db->table('cursos c')
            ->select('c.curs_ide, c.curs_nombre, c.curs_nivel,
                      u.usua_nombres as prof_nombres, u.usua_paterno as prof_paterno, u.usua_materno as prof_materno')
            ->join('profesores p', 'p.prof_ide=c.curs_prof_ide', 'left')
            ->join('usuarios u',   'u.usua_ide=p.prof_usua_ide', 'left')
            ->where('c.curs_ide', $curs_ide)
            ->get()->getRow();
        if (!$curso) { echo "Curso no encontrado."; exit; }

        $ci = (int)$curs_ide;
        $alumnos = $db->table('matriculas m')
            ->select("u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_materno,
                      u.usua_email, u.usua_celular,
                      m.matr_completado, m.matr_fecha,
                      (SELECT COUNT(*) FROM progreso pr
                           JOIN lecciones ll ON ll.lecc_ide=pr.prog_lecc_ide
                           WHERE pr.prog_usua_ide=u.usua_ide AND ll.lecc_curs_ide={$ci}
                           AND pr.prog_completado=1) AS lecc_hechas,
                      (SELECT COUNT(*) FROM lecciones ll2
                           WHERE ll2.lecc_curs_ide={$ci} AND ll2.lecc_esta_ide=1
                           AND ll2.lecc_tipo!='QUIZ') AS total_lecc,
                      (SELECT ga.grua_grup_ide FROM grupo_alumnos ga
                           JOIN grupos_asesor g ON g.grup_ide=ga.grua_grup_ide
                           WHERE ga.grua_usua_ide=u.usua_ide AND g.grup_curs_ide={$ci} LIMIT 1) AS grupo_ide,
                      (SELECT CONCAT(ua.usua_paterno,' ',ua.usua_nombres)
                           FROM grupo_alumnos ga2 JOIN grupos_asesor g2 ON g2.grup_ide=ga2.grua_grup_ide
                           JOIN asesores a2 ON a2.ases_ide=g2.grup_ases_ide
                           JOIN usuarios ua ON ua.usua_ide=a2.ases_usua_ide
                           WHERE ga2.grua_usua_ide=u.usua_ide AND g2.grup_curs_ide={$ci} LIMIT 1) AS asesor_nombre")
            ->join('usuarios u', 'u.usua_ide=m.matr_usua_ide')
            ->where('m.matr_curs_ide', $curs_ide)
            ->where('m.matr_esta_ide', 1)
            ->where('u.usua_perf_ide', 1)
            ->where('u.usua_deleted_at IS NULL')
            ->orderBy('u.usua_paterno')
            ->get()->getResult();

        echo view('asesor/valumnos', [
            'curso'    => $curso,
            'alumnos'  => $alumnos,
            'base'     => base_url('public'),
            'session'  => $this->session,
        ]);
    }

    // ─────────────────────────────────────────
    // ASESOR: crear grupo + notificar alumnos
    // ─────────────────────────────────────────
    public function formarGrupo()
    {
        if (!in_array($this->session->perf_ide, [3, 4])) { echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return; }

        $db       = \Config\Database::connect();
        $curs_ide = (int)$this->request->getPost('curs_ide');
        $nombre   = trim($this->request->getPost('nombre_grupo'));
        $alumnos  = $this->request->getPost('alumnos');

        if (!$curs_ide || !$nombre || empty($alumnos) || !is_array($alumnos)) {
            echo json_encode(['ok'=>false,'msg'=>'Completa todos los campos y selecciona al menos un alumno.']);
            return;
        }

        // Obtener o crear registro en tabla asesores
        $ases = $db->table('asesores')->where('ases_usua_ide', $this->session->usua_ide)->get()->getRow();
        if (!$ases) {
            $db->table('asesores')->insert([
                'ases_usua_ide'  => $this->session->usua_ide,
                'ases_esta_ide'  => 1,
                'ases_create_at' => date('Y-m-d H:i:s'),
            ]);
            $ases_ide = $db->insertID();
        } else {
            $ases_ide = $ases->ases_ide;
        }

        // Filtrar alumnos sin asesor en este curso
        $validos = [];
        foreach ($alumnos as $uid) {
            $uid = (int)$uid;
            if ($uid <= 0) continue;
            $existe = $db->table('grupo_alumnos ga')
                ->join('grupos_asesor g', 'g.grup_ide=ga.grua_grup_ide')
                ->where('ga.grua_usua_ide', $uid)
                ->where('g.grup_curs_ide',  $curs_ide)
                ->countAllResults();
            if ($existe == 0) $validos[] = $uid;
        }

        if (empty($validos)) {
            echo json_encode(['ok'=>false,'msg'=>'Todos los alumnos seleccionados ya tienen asesor en este curso.']);
            return;
        }

        // Datos del curso y asesor para el mensaje de notificación
        $curso = $db->table('cursos c')
            ->select('c.curs_nombre, u.usua_nombres as prof_n, u.usua_paterno as prof_p')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
            ->join('usuarios u','u.usua_ide=p.prof_usua_ide','left')
            ->where('c.curs_ide', $curs_ide)->get()->getRow();

        $asesorNombre = $db->table('usuarios')
            ->select('usua_paterno, usua_nombres')
            ->where('usua_ide', $this->session->usua_ide)
            ->get()->getRow();

        // Crear grupo
        $db->table('grupos_asesor')->insert([
            'grup_nombre'    => $nombre,
            'grup_ases_ide'  => $ases_ide,
            'grup_curs_ide'  => $curs_ide,
            'grup_create_at' => date('Y-m-d H:i:s'),
        ]);
        $grup_ide = $db->insertID();

        // Insertar alumnos y NOTIFICARLES
        foreach ($validos as $uid) {
            $db->table('grupo_alumnos')->insert([
                'grua_grup_ide'  => $grup_ide,
                'grua_usua_ide'  => $uid,
                'grua_create_at' => date('Y-m-d H:i:s'),
            ]);

            $this->crearNotificacion(
                $uid,
                'GRUPO_NUEVO',
                '¡Has sido añadido a un grupo de asesoría!',
                'Grupo: "' . $nombre . '" · Curso: ' . ($curso->curs_nombre ?? '') .
                ' · Asesor: ' . ($asesorNombre ? $asesorNombre->usua_paterno.' '.$asesorNombre->usua_nombres : ''),
                '/alumno/grupos',
                'Ver mis grupos'
            );
        }

        $omitidos = count($alumnos) - count($validos);
        $msg = 'Grupo "'.htmlspecialchars($nombre).'" formado con '.count($validos).' alumno(s).';
        if ($omitidos > 0) $msg .= ' ('.$omitidos.' ya tenían asesor y fueron omitidos)';

        echo json_encode(['ok'=>true,'msg'=>$msg,'grup_ide'=>$grup_ide]);
    }

    // ─────────────────────────────────────────
    // ASESOR: lista de grupos propios
    // ─────────────────────────────────────────
    public function grupos()
    {
        if (!in_array($this->session->perf_ide, [3, 4])) { echo "Sin acceso"; exit; }
        $db   = \Config\Database::connect();
        $ases = $db->table('asesores')->where('ases_usua_ide', $this->session->usua_ide)->get()->getRow();

        $grupos = [];
        if ($ases) {
            $grupos = $db->table('grupos_asesor g')
                ->select('g.grup_ide, g.grup_nombre, g.grup_create_at,
                          c.curs_nombre, c.curs_nivel,
                          u.usua_nombres as prof_nombres, u.usua_paterno as prof_paterno,
                          (SELECT COUNT(*) FROM grupo_alumnos ga  WHERE ga.grua_grup_ide=g.grup_ide) as total_alumnos,
                          (SELECT COUNT(*) FROM grupo_mensajes gm WHERE gm.grum_grup_ide=g.grup_ide) as total_mensajes')
                ->join('cursos c',    'c.curs_ide=g.grup_curs_ide', 'left')
                ->join('profesores p','p.prof_ide=c.curs_prof_ide', 'left')
                ->join('usuarios u',  'u.usua_ide=p.prof_usua_ide', 'left')
                ->where('g.grup_ases_ide', $ases->ases_ide)
                ->orderBy('g.grup_create_at', 'DESC')
                ->get()->getResult();
        }

        echo view('asesor/vgrupos', [
            'grupos'  => $grupos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }

    // ─────────────────────────────────────────
    // ALUMNO: lista de grupos en los que participa
    // ─────────────────────────────────────────
    public function gruposAlumno()
    {
        if ($this->session->perf_ide != 1) { echo "Sin acceso"; exit; }
        $db = \Config\Database::connect();

        $grupos = $db->table('grupo_alumnos ga')
            ->select('g.grup_ide, g.grup_nombre, g.grup_create_at,
                      c.curs_nombre, c.curs_nivel, c.curs_ide,
                      pu.usua_nombres as prof_nombres, pu.usua_paterno as prof_paterno,
                      au.usua_nombres as ases_nombres, au.usua_paterno as ases_paterno,
                      (SELECT COUNT(*) FROM grupo_alumnos ga2 WHERE ga2.grua_grup_ide=g.grup_ide) as total_miembros,
                      (SELECT COUNT(*) FROM grupo_mensajes gm WHERE gm.grum_grup_ide=g.grup_ide) as total_mensajes,
                      (SELECT COUNT(*) FROM grupo_mensajes gm2
                           WHERE gm2.grum_grup_ide=g.grup_ide
                           AND gm2.grum_create_at > IFNULL(
                               (SELECT noti_create_at FROM notificaciones n2
                                WHERE n2.noti_usua_ide='.$this->session->usua_ide.'
                                AND n2.noti_tipo="MENSAJE_NUEVO"
                                AND n2.noti_link LIKE CONCAT("%/asesor/chat/",g.grup_ide,"%")
                                AND n2.noti_leida=1 ORDER BY n2.noti_create_at DESC LIMIT 1),
                               "2000-01-01"
                           )) as msgs_nuevos')
            ->join('grupos_asesor g', 'g.grup_ide=ga.grua_grup_ide')
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',   'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',   'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide',  'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',   'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide',  'left')
            ->where('ga.grua_usua_ide', $this->session->usua_ide)
            ->orderBy('g.grup_create_at', 'DESC')
            ->get()->getResult();

        echo view('alumno/vgrupos', [
            'grupos'  => $grupos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }

    // ─────────────────────────────────────────
    // ASESOR / ADMIN / ALUMNO: chat del grupo
    // ─────────────────────────────────────────
    public function chat($grup_ide)
    {
        $db    = \Config\Database::connect();
        $grupo = $db->table('grupos_asesor g')
            ->select('g.grup_ide, g.grup_nombre, g.grup_create_at, g.grup_curs_ide,
                      c.curs_nombre, c.curs_nivel,
                      pu.usua_nombres as prof_nombres, pu.usua_paterno as prof_paterno,
                      au.usua_nombres as ases_nombres, au.usua_paterno as ases_paterno,
                      (SELECT COUNT(*) FROM grupo_alumnos ga WHERE ga.grua_grup_ide=g.grup_ide) as total_alumnos')
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',  'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',  'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide', 'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',  'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide', 'left')
            ->where('g.grup_ide', $grup_ide)
            ->get()->getRow();
        if (!$grupo) { echo "Grupo no encontrado."; exit; }

        // Validar acceso según perfil
        $perf = $this->session->perf_ide;
        if ($perf == 3) {
            // ADMIN: acceso total
        } elseif ($perf == 4) {
            // ASESOR: solo sus propios grupos
            $ases  = $db->table('asesores')->where('ases_usua_ide', $this->session->usua_ide)->get()->getRow();
            $ok    = $db->table('grupos_asesor')
                ->where('grup_ide',     $grup_ide)
                ->where('grup_ases_ide', $ases ? $ases->ases_ide : 0)
                ->countAllResults();
            if (!$ok) { echo "Sin acceso a este grupo."; exit; }
        } elseif ($perf == 1) {
            // ALUMNO: solo si pertenece al grupo
            $ok = $db->table('grupo_alumnos')
                ->where('grua_grup_ide', $grup_ide)
                ->where('grua_usua_ide', $this->session->usua_ide)
                ->countAllResults();
            if (!$ok) { echo "No perteneces a este grupo."; exit; }

            // Marcar notificaciones de este grupo como leídas
            $db->table('notificaciones')
                ->where('noti_usua_ide', $this->session->usua_ide)
                ->where('noti_tipo', 'MENSAJE_NUEVO')
                ->like('noti_link', '/asesor/chat/'.$grup_ide)
                ->update(['noti_leida' => 1]);
        } else {
            echo "Sin acceso."; exit;
        }

        $ci = (int)$grupo->grup_curs_ide;

        $alumnos = $db->table('grupo_alumnos ga')
            ->select("u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_materno, u.usua_email,
                      (SELECT COUNT(*) FROM progreso pr
                           JOIN lecciones ll ON ll.lecc_ide=pr.prog_lecc_ide
                           WHERE pr.prog_usua_ide=u.usua_ide AND ll.lecc_curs_ide={$ci}
                           AND pr.prog_completado=1) AS lecc_hechas,
                      (SELECT COUNT(*) FROM lecciones ll2
                           WHERE ll2.lecc_curs_ide={$ci} AND ll2.lecc_esta_ide=1
                           AND ll2.lecc_tipo!='QUIZ') AS total_lecc")
            ->join('usuarios u','u.usua_ide=ga.grua_usua_ide')
            ->where('ga.grua_grup_ide', $grup_ide)
            ->orderBy('u.usua_paterno')
            ->get()->getResult();

        $mensajes = $db->table('grupo_mensajes gm')
            ->select('gm.grum_ide, gm.grum_mensaje, gm.grum_create_at,
                      u.usua_nombres, u.usua_paterno, u.usua_perf_ide, u.usua_ide')
            ->join('usuarios u','u.usua_ide=gm.grum_usua_ide')
            ->where('gm.grum_grup_ide', $grup_ide)
            ->orderBy('gm.grum_create_at', 'ASC')
            ->get()->getResult();

        echo view('asesor/vchat', [
            'grupo'    => $grupo,
            'alumnos'  => $alumnos,
            'mensajes' => $mensajes,
            'base'     => base_url('public'),
            'session'  => $this->session,
        ]);
    }

    // ─────────────────────────────────────────
    // ASESOR / ADMIN / ALUMNO: enviar mensaje + notificar
    // ─────────────────────────────────────────
    public function enviarMensaje()
    {
        $db       = \Config\Database::connect();
        $grup_ide = (int)$this->request->getPost('grup_ide');
        $mensaje  = trim($this->request->getPost('mensaje'));
        $perf     = $this->session->perf_ide;

        if (!$grup_ide || !$mensaje) {
            echo json_encode(['ok'=>false,'msg'=>'Escribe un mensaje antes de enviar.']);
            return;
        }

        // Validar acceso
        if ($perf == 3) {
            // ADMIN: siempre OK
        } elseif ($perf == 4) {
            $ases = $db->table('asesores')->where('ases_usua_ide',$this->session->usua_ide)->get()->getRow();
            $ok   = $db->table('grupos_asesor')
                ->where('grup_ide',     $grup_ide)
                ->where('grup_ases_ide', $ases ? $ases->ases_ide : 0)
                ->countAllResults();
            if (!$ok) { echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return; }
        } elseif ($perf == 1) {
            $ok = $db->table('grupo_alumnos')
                ->where('grua_grup_ide', $grup_ide)
                ->where('grua_usua_ide', $this->session->usua_ide)
                ->countAllResults();
            if (!$ok) { echo json_encode(['ok'=>false,'msg'=>'No perteneces a este grupo.']); return; }
        } else {
            echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return;
        }

        // Guardar mensaje
        $db->table('grupo_mensajes')->insert([
            'grum_grup_ide'  => $grup_ide,
            'grum_usua_ide'  => $this->session->usua_ide,
            'grum_mensaje'   => $mensaje,
            'grum_create_at' => date('Y-m-d H:i:s'),
        ]);

        // Datos del grupo para el mensaje de notificación
        $grupo = $db->table('grupos_asesor g')
            ->select('g.grup_nombre, c.curs_nombre')
            ->join('cursos c','c.curs_ide=g.grup_curs_ide','left')
            ->where('g.grup_ide', $grup_ide)->get()->getRow();

        $remitente = $db->table('usuarios')
            ->select('usua_paterno, usua_nombres')
            ->where('usua_ide', $this->session->usua_ide)
            ->get()->getRow();
        $remNombre = $remitente ? $remitente->usua_paterno.' '.$remitente->usua_nombres : 'Alguien';

        // Notificar a todos los miembros del grupo excepto al emisor
        // — alumnos —
        $alumnos = $db->table('grupo_alumnos')
            ->where('grua_grup_ide', $grup_ide)
            ->where('grua_usua_ide !=', $this->session->usua_ide)
            ->get()->getResult();
        foreach ($alumnos as $a) {
            $this->crearNotificacion(
                $a->grua_usua_ide,
                'MENSAJE_NUEVO',
                'Nuevo mensaje en tu grupo de asesoría',
                $remNombre . ' escribió en "'.($grupo->grup_nombre??'').'": "'.mb_substr($mensaje,0,80).(mb_strlen($mensaje)>80?'…':'"'),
                '/asesor/chat/'.$grup_ide,
                'Ver chat'
            );
        }
        // — asesor (si el que escribe es alumno o admin) —
        if ($perf != 4) {
            $asesorUsua = $db->table('grupos_asesor ga')
                ->select('u.usua_ide')
                ->join('asesores a','a.ases_ide=ga.grup_ases_ide')
                ->join('usuarios u','u.usua_ide=a.ases_usua_ide')
                ->where('ga.grup_ide', $grup_ide)
                ->get()->getRow();
            if ($asesorUsua && $asesorUsua->usua_ide != $this->session->usua_ide) {
                $this->crearNotificacion(
                    $asesorUsua->usua_ide,
                    'MENSAJE_NUEVO',
                    'Nuevo mensaje en tu grupo',
                    $remNombre . ' escribió en "'.($grupo->grup_nombre??'').'": "'.mb_substr($mensaje,0,80).(mb_strlen($mensaje)>80?'…':'"'),
                    '/asesor/chat/'.$grup_ide,
                    'Ver chat'
                );
            }
        }

        echo json_encode(['ok'=>true,'msg'=>'Mensaje enviado.']);
    }

    // ─────────────────────────────────────────
    // ADMIN: ver todos los grupos
    // ─────────────────────────────────────────
    public function adminGrupos()
    {
        if ($this->session->perf_ide != 3) { echo "Sin acceso"; exit; }
        $db     = \Config\Database::connect();
        $grupos = $db->table('grupos_asesor g')
            ->select('g.grup_ide, g.grup_nombre, g.grup_create_at,
                      c.curs_nombre, c.curs_nivel,
                      au.usua_nombres as ases_nombres, au.usua_paterno as ases_paterno,
                      pu.usua_nombres as prof_nombres, pu.usua_paterno as prof_paterno,
                      (SELECT COUNT(*) FROM grupo_alumnos ga  WHERE ga.grua_grup_ide=g.grup_ide) as total_alumnos,
                      (SELECT COUNT(*) FROM grupo_mensajes gm WHERE gm.grum_grup_ide=g.grup_ide) as total_mensajes')
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',  'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',  'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide', 'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',  'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide', 'left')
            ->orderBy('g.grup_create_at', 'DESC')
            ->get()->getResult();

        echo view('asesor/vadmin_grupos', [
            'grupos'  => $grupos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }
}
