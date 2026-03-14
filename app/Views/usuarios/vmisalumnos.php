<div class="mb-4">
    <h4 class="fw-bold mb-1">👥 Mis Alumnos</h4>
    <p class="text-cl-muted small">Alumnos inscritos en tus cursos</p>
</div>

<?php if(empty($alumnos)): ?>
<div class="card text-center py-5"><div class="card-body">
    <div style="font-size:3rem;margin-bottom:1rem;">👤</div>
    <h5 class="fw-bold mb-2">Aún no tienes alumnos</h5>
    <p class="text-cl-muted mb-0">Cuando alguien se inscriba en tus cursos aparecerá aquí.</p>
</div></div>
<?php else: ?>

<!-- Resumen rápido -->
<?php
$totalAlumnos  = count(array_unique(array_column((array)$alumnos,'usua_ide')));
$totalCursos   = count(array_unique(array_column((array)$alumnos,'curs_ide')));
$completados   = count(array_filter((array)$alumnos, fn($a)=>$a->matr_completado));
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(124,58,237,.15);color:var(--cl-accent2);"><i class="ti-user"></i></div>
            <div><div class="fw-bold fs-4"><?=$totalAlumnos?></div><div class="text-cl-muted small">Alumnos únicos</div></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(6,182,212,.15);color:#06b6d4;"><i class="ti-book"></i></div>
            <div><div class="fw-bold fs-4"><?=$totalCursos?></div><div class="text-cl-muted small">Cursos con inscritos</div></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:rgba(16,185,129,.15);color:#10b981;"><i class="ti-check"></i></div>
            <div><div class="fw-bold fs-4"><?=$completados?></div><div class="text-cl-muted small">Completaron un curso</div></div>
        </div>
    </div>
</div>

<!-- Buscador -->
<div class="mb-3">
    <input type="text" class="form-control" style="max-width:320px;" placeholder="Buscar alumno por nombre..." oninput="filtrarAlumnos(this.value)">
</div>

<div class="card"><div class="card-body p-0">
<table class="table mb-0" id="tablaAlumnos">
<thead><tr>
    <th>Alumno</th>
    <th>Contacto</th>
    <th>Curso</th>
    <th>Progreso</th>
    <th>Estado</th>
    <th>Inscripción</th>
</tr></thead>
<tbody>
<?php foreach($alumnos as $a):
    $pct = $a->total_lecc > 0 ? round(($a->lecc_hechas/$a->total_lecc)*100) : 0; ?>
<tr class="fila-alumno" data-nombre="<?=strtolower($a->usua_paterno.' '.$a->usua_nombres)?>">
    <td>
        <div class="d-flex align-items-center gap-2">
            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--cl-accent),#06b6d4);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;"><?=strtoupper(substr($a->usua_nombres,0,1))?></div>
            <div>
                <div class="fw-medium"><?=htmlspecialchars($a->usua_paterno.' '.$a->usua_materno.', '.$a->usua_nombres)?></div>
                <small class="text-cl-muted"><?=$a->usua_email?></small>
            </div>
        </div>
    </td>
    <td><small class="text-cl-muted"><i class="ti-mobile me-1"></i><?=$a->usua_celular?:'-'?></small></td>
    <td>
        <div class="fw-medium small"><?=htmlspecialchars($a->curs_nombre)?></div>
        <span class="badge badge-nivel-<?=$a->curs_nivel?>" style="font-size:.6rem;"><?=$a->curs_nivel?></span>
    </td>
    <td style="min-width:140px;">
        <div class="d-flex align-items-center gap-2">
            <div class="progress flex-grow-1"><div class="progress-bar" style="width:<?=$pct?>%"></div></div>
            <small class="fw-semibold" style="min-width:32px;"><?=$pct?>%</small>
        </div>
        <small class="text-cl-muted"><?=$a->lecc_hechas?>/<?=$a->total_lecc?> lecciones</small>
    </td>
    <td>
        <?php if($a->matr_completado): ?>
        <span class="badge" style="background:rgba(16,185,129,.2);color:#10b981;">🎓 Completado</span>
        <?php else: ?>
        <span class="badge" style="background:rgba(245,158,11,.15);color:#f59e0b;">⏳ En progreso</span>
        <?php endif; ?>
    </td>
    <td><small class="text-cl-muted"><?=date('d/m/Y', strtotime($a->matr_fecha))?></small></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div>
<?php endif; ?>

<script>
function filtrarAlumnos(texto){
    texto = texto.toLowerCase();
    document.querySelectorAll('.fila-alumno').forEach(function(tr){
        tr.style.display = (!texto || (tr.dataset.nombre||'').includes(texto)) ? '' : 'none';
    });
}
</script>
