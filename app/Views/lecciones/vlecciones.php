<?php
// Vista de gestión de lecciones - el profesor elige un curso y ve/agrega lecciones
$db2 = \Config\Database::connect();

// Para ADMIN: todos los cursos activos. Para PROFESOR: ya viene filtrado
$esAdmin = isset($session) && $session->perf_ide == 3;
if ($esAdmin) {
    $cursos = $db2->table('cursos c')
        ->select('c.curs_ide, c.curs_nombre, c.curs_nivel, u.usua_nombres, u.usua_paterno')
        ->join('profesores p','p.prof_ide=c.curs_prof_ide','left')
        ->join('usuarios u','u.usua_ide=p.prof_usua_ide','left')
        ->where('c.curs_esta_ide',1)
        ->orderBy('c.curs_nombre')
        ->get()->getResult();
} else {
    // $cursos ya viene del controlador
}
?>
<div class="mb-4">
    <h4 class="fw-bold mb-1">🎬 Gestionar Lecciones</h4>
    <p class="text-cl-muted small">Selecciona un curso para ver y agregar lecciones</p>
</div>

<?php if(empty($cursos)): ?>
<div class="card text-center py-5"><div class="card-body">
    <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
    <h5 class="fw-bold mb-2">No tienes cursos activos</h5>
    <p class="text-cl-muted mb-3">Crea un curso primero para poder agregarle lecciones.</p>
    <button class="btn btn-primary" onclick="cargarFuncion('/cursos','Cursos','Mis Cursos','')">
        <i class="ti-book me-1"></i>Ir a Cursos
    </button>
</div></div>
<?php else: ?>
<div class="row g-3">
<?php foreach($cursos as $c): ?>
<div class="col-xl-3 col-lg-4 col-md-6">
    <div class="card curso-card h-100" onclick="cargarFuncion('/cursos/ver/<?=$c->curs_ide?>','Cursos','<?=htmlspecialchars(addslashes($c->curs_nombre))?>','Gestiona secciones y lecciones')">
        <div class="card-img-top d-flex align-items-center justify-content-center" style="height:100px;background:linear-gradient(135deg,#1e1b4b,#0d0f18);">
            <i class="ti-video-camera" style="font-size:2.2rem;color:var(--cl-accent2);opacity:.5;"></i>
        </div>
        <div class="card-body">
            <span class="badge badge-nivel-<?=$c->curs_nivel?> mb-2"><?=$c->curs_nivel?></span>
            <h6 class="fw-bold mb-1"><?=htmlspecialchars($c->curs_nombre)?></h6>
            <?php if(isset($c->usua_nombres)): ?>
            <small class="text-cl-muted"><i class="ti-user me-1"></i><?=htmlspecialchars($c->usua_nombres.' '.$c->usua_paterno)?></small>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-outline-primary w-100">
                <i class="ti-pencil me-1"></i>Gestionar lecciones
            </button>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<p class="text-cl-muted small mt-3"><i class="ti-info-alt me-1"></i>Haz clic en un curso para gestionar sus secciones y lecciones.</p>
<?php endif; ?>
