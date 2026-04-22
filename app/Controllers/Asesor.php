<?php
namespace App\Controllers;
use App\Models\General;

/**
 * Controller Asesor
 * Gestiona toda la funcionalidad del perfil ASESOR:
 *  - Ver cursos activos de todos los profesores
 *  - Ver alumnos de un curso (con estado de asesoría)
 *  - Formar grupos de alumnos
 *  - Ver y gestionar grupos propios
 *  - Mensajería de grupo
 * El ADMIN también puede acceder a adminGrupos() y chat() para ver todo.
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
        // Solo ASESOR (perf_ide=4) y ADMIN (perf_ide=3)
        if (!in_array($this->session->perf_ide, [3, 4])) {
            echo "Sin acceso"; exit(0);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // ASESOR: listado de todos los cursos activos para asesorar
    // ──────────────────────────────────────────────────────────────
    public function cursos()
    {
        $db     = \Config\Database::connect();
        $cursos = $db->table('cursos c')
            ->select('c.curs_ide, c.curs_nombre, c.curs_descripcion, c.curs_nivel,
                      cat.cate_nombre, cat.cate_icono,
                      u.usua_nombres, u.usua_paterno, u.usua_materno,
                      (SELECT COUNT(*) FROM matriculas m
                         WHERE m.matr_curs_ide=c.curs_ide
                         AND   m.matr_esta_ide=1) as total_alumnos')
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

    // ──────────────────────────────────────────────────────────────
    // ASESOR: alumnos de un curso con estado de asesoría
    // ──────────────────────────────────────────────────────────────
    public function alumnos($curs_ide)
    {
        $db    = \Config\Database::connect();
        $curso = $db->table('cursos c')
            ->select('c.curs_ide, c.curs_nombre, c.curs_nivel,
                      u.usua_nombres as prof_nombres,
                      u.usua_paterno as prof_paterno,
                      u.usua_materno as prof_materno')
            ->join('profesores p', 'p.prof_ide=c.curs_prof_ide', 'left')
            ->join('usuarios u',   'u.usua_ide=p.prof_usua_ide', 'left')
            ->where('c.curs_ide', $curs_ide)
            ->get()->getRow();

        if (!$curso) { echo "Curso no encontrado."; exit; }

        // Alumnos matriculados + estado de asesoría en este curso
        // La subconsulta grupo_ide devuelve el id del grupo si ya tiene asesor en este curso
        $ci  = (int)$curs_ide; // cast seguro para subconsultas
        $alumnos = $db->table('matriculas m')
            ->select("u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_materno,
                      u.usua_email, u.usua_celular,
                      m.matr_completado, m.matr_fecha,
                      (SELECT COUNT(*) FROM progreso pr
                           JOIN lecciones ll ON ll.lecc_ide = pr.prog_lecc_ide
                           WHERE pr.prog_usua_ide = u.usua_ide
                           AND   ll.lecc_curs_ide = {$ci}
                           AND   pr.prog_completado = 1) AS lecc_hechas,
                      (SELECT COUNT(*) FROM lecciones ll2
                           WHERE ll2.lecc_curs_ide = {$ci}
                           AND   ll2.lecc_esta_ide = 1
                           AND   ll2.lecc_tipo != 'QUIZ') AS total_lecc,
                      (SELECT ga.grua_grup_ide FROM grupo_alumnos ga
                           JOIN grupos_asesor g ON g.grup_ide = ga.grua_grup_ide
                           WHERE ga.grua_usua_ide = u.usua_ide
                           AND   g.grup_curs_ide  = {$ci}
                           LIMIT 1) AS grupo_ide,
                      (SELECT CONCAT(ua.usua_paterno,' ',ua.usua_nombres)
                           FROM grupo_alumnos ga2
                           JOIN grupos_asesor g2 ON g2.grup_ide   = ga2.grua_grup_ide
                           JOIN asesores a2      ON a2.ases_ide   = g2.grup_ases_ide
                           JOIN usuarios ua      ON ua.usua_ide   = a2.ases_usua_ide
                           WHERE ga2.grua_usua_ide = u.usua_ide
                           AND   g2.grup_curs_ide  = {$ci}
                           LIMIT 1) AS asesor_nombre")
            ->join('usuarios u', 'u.usua_ide=m.matr_usua_ide')
            ->where('m.matr_curs_ide', $curs_ide)
            ->where('m.matr_esta_ide', 1)
            ->where('u.usua_perf_ide', 1)    // Solo alumnos
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

    // ──────────────────────────────────────────────────────────────
    // ASESOR: crear grupo con alumnos seleccionados (POST)
    // ──────────────────────────────────────────────────────────────
    public function formarGrupo()
    {
        $db       = \Config\Database::connect();
        $curs_ide = (int)$this->request->getPost('curs_ide');
        $nombre   = trim($this->request->getPost('nombre_grupo'));
        $alumnos  = $this->request->getPost('alumnos'); // array usua_ide

        if (!$curs_ide || !$nombre || empty($alumnos) || !is_array($alumnos)) {
            echo json_encode(['ok'=>false,'msg'=>'Completa todos los campos y selecciona al menos un alumno.']);
            return;
        }

        // Obtener o crear registro en tabla asesores
        $ases = $db->table('asesores')
            ->where('ases_usua_ide', $this->session->usua_ide)
            ->get()->getRow();

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

        // Filtrar solo alumnos que NO tienen asesor en este curso
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

        // Crear grupo
        $db->table('grupos_asesor')->insert([
            'grup_nombre'    => $nombre,
            'grup_ases_ide'  => $ases_ide,
            'grup_curs_ide'  => $curs_ide,
            'grup_create_at' => date('Y-m-d H:i:s'),
        ]);
        $grup_ide = $db->insertID();

        // Insertar alumnos válidos en el grupo
        foreach ($validos as $uid) {
            $db->table('grupo_alumnos')->insert([
                'grua_grup_ide'  => $grup_ide,
                'grua_usua_ide'  => $uid,
                'grua_create_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $omitidos = count($alumnos) - count($validos);
        $msg = 'Grupo "'.htmlspecialchars($nombre).'" formado con '.count($validos).' alumno(s).';
        if ($omitidos > 0) $msg .= ' ('.$omitidos.' ya tenían asesor y fueron omitidos)';

        echo json_encode(['ok'=>true,'msg'=>$msg,'grup_ide'=>$grup_ide]);
    }

    // ──────────────────────────────────────────────────────────────
    // ASESOR: lista de grupos propios
    // ──────────────────────────────────────────────────────────────
    public function grupos()
    {
        $db   = \Config\Database::connect();
        $ases = $db->table('asesores')
            ->where('ases_usua_ide', $this->session->usua_ide)
            ->get()->getRow();

        $grupos = [];
        if ($ases) {
            $grupos = $db->table('grupos_asesor g')
                ->select('g.grup_ide, g.grup_nombre, g.grup_create_at,
                          c.curs_nombre, c.curs_nivel,
                          u.usua_nombres as prof_nombres, u.usua_paterno as prof_paterno,
                          (SELECT COUNT(*) FROM grupo_alumnos ga  WHERE ga.grua_grup_ide=g.grup_ide) as total_alumnos,
                          (SELECT COUNT(*) FROM grupo_mensajes gm WHERE gm.grum_grup_ide=g.grup_ide) as total_mensajes')
                ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',   'left')
                ->join('profesores p','p.prof_ide=c.curs_prof_ide',   'left')
                ->join('usuarios u',  'u.usua_ide=p.prof_usua_ide',   'left')
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

    // ──────────────────────────────────────────────────────────────
    // ASESOR / ADMIN: ver chat y detalle de un grupo
    // ──────────────────────────────────────────────────────────────
    public function chat($grup_ide)
    {
        $db    = \Config\Database::connect();
        $grupo = $db->table('grupos_asesor g')
            ->select('g.grup_ide, g.grup_nombre, g.grup_create_at, g.grup_curs_ide,
                      c.curs_nombre, c.curs_nivel,
                      pu.usua_nombres as prof_nombres, pu.usua_paterno as prof_paterno,
                      au.usua_nombres as ases_nombres, au.usua_paterno as ases_paterno,
                      (SELECT COUNT(*) FROM grupo_alumnos ga WHERE ga.grua_grup_ide=g.grup_ide) as total_alumnos')
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',   'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',   'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide',  'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',   'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide',  'left')
            ->where('g.grup_ide', $grup_ide)
            ->get()->getRow();

        if (!$grupo) { echo "Grupo no encontrado."; exit; }

        // Validar acceso: solo el asesor dueño del grupo o ADMIN
        if ($this->session->perf_ide != 3) {
            $ases  = $db->table('asesores')->where('ases_usua_ide', $this->session->usua_ide)->get()->getRow();
            $miGrp = $db->table('grupos_asesor')
                ->where('grup_ide',     $grup_ide)
                ->where('grup_ases_ide', $ases ? $ases->ases_ide : 0)
                ->countAllResults();
            if (!$miGrp) { echo "Sin acceso a este grupo."; exit; }
        }

        $ci = (int)$grupo->grup_curs_ide;

        $alumnos = $db->table('grupo_alumnos ga')
            ->select("u.usua_ide, u.usua_nombres, u.usua_paterno, u.usua_materno, u.usua_email,
                      (SELECT COUNT(*) FROM progreso pr
                           JOIN lecciones ll ON ll.lecc_ide=pr.prog_lecc_ide
                           WHERE pr.prog_usua_ide=u.usua_ide
                           AND   ll.lecc_curs_ide={$ci}
                           AND   pr.prog_completado=1) AS lecc_hechas,
                      (SELECT COUNT(*) FROM lecciones ll2
                           WHERE ll2.lecc_curs_ide={$ci}
                           AND   ll2.lecc_esta_ide=1
                           AND   ll2.lecc_tipo!='QUIZ') AS total_lecc")
            ->join('usuarios u','u.usua_ide=ga.grua_usua_ide')
            ->where('ga.grua_grup_ide', $grup_ide)
            ->orderBy('u.usua_paterno')
            ->get()->getResult();

        $mensajes = $db->table('grupo_mensajes gm')
            ->select('gm.grum_ide, gm.grum_mensaje, gm.grum_create_at,
                      u.usua_nombres, u.usua_paterno, u.usua_perf_ide')
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

    // ──────────────────────────────────────────────────────────────
    // ASESOR: enviar mensaje al grupo (POST)
    // ──────────────────────────────────────────────────────────────
    public function enviarMensaje()
    {
        $db       = \Config\Database::connect();
        $grup_ide = (int)$this->request->getPost('grup_ide');
        $mensaje  = trim($this->request->getPost('mensaje'));

        if (!$grup_ide || !$mensaje) {
            echo json_encode(['ok'=>false,'msg'=>'Escribe un mensaje antes de enviar.']);
            return;
        }

        // Validar acceso: solo el dueño del grupo o ADMIN
        if ($this->session->perf_ide != 3) {
            $ases = $db->table('asesores')->where('ases_usua_ide',$this->session->usua_ide)->get()->getRow();
            $ok   = $db->table('grupos_asesor')
                ->where('grup_ide',     $grup_ide)
                ->where('grup_ases_ide', $ases ? $ases->ases_ide : 0)
                ->countAllResults();
            if (!$ok) { echo json_encode(['ok'=>false,'msg'=>'Sin acceso.']); return; }
        }

        $db->table('grupo_mensajes')->insert([
            'grum_grup_ide'  => $grup_ide,
            'grum_usua_ide'  => $this->session->usua_ide,
            'grum_mensaje'   => $mensaje,
            'grum_create_at' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode(['ok'=>true,'msg'=>'Mensaje enviado.']);
    }

    // ──────────────────────────────────────────────────────────────
    // ADMIN: ver todos los grupos de todos los asesores
    // ──────────────────────────────────────────────────────────────
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
            ->join('cursos c',    'c.curs_ide=g.grup_curs_ide',   'left')
            ->join('profesores p','p.prof_ide=c.curs_prof_ide',   'left')
            ->join('usuarios pu', 'pu.usua_ide=p.prof_usua_ide',  'left')
            ->join('asesores a',  'a.ases_ide=g.grup_ases_ide',   'left')
            ->join('usuarios au', 'au.usua_ide=a.ases_usua_ide',  'left')
            ->orderBy('g.grup_create_at', 'DESC')
            ->get()->getResult();

        echo view('asesor/vadmin_grupos', [
            'grupos'  => $grupos,
            'base'    => base_url('public'),
            'session' => $this->session,
        ]);
    }
}
