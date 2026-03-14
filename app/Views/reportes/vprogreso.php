<div class="mb-4"><h4 class="fw-bold mb-1">📊 Reporte de Progreso</h4>
<p class="text-cl-muted small">Estadísticas generales de la plataforma</p></div>

<!-- Stats cards -->
<div class="row g-3 mb-4">
<?php
$st=[
    ['label'=>'Alumnos Activos','val'=>$stats['total_alumnos'],'icon'=>'ti-user','bg'=>'rgba(124,58,237,.15)','color'=>'var(--cl-accent2)'],
    ['label'=>'Profesores','val'=>$stats['total_profesores'],'icon'=>'ti-graduation','bg'=>'rgba(6,182,212,.15)','color'=>'#06b6d4'],
    ['label'=>'Cursos Activos','val'=>$stats['total_cursos'],'icon'=>'ti-book','bg'=>'rgba(16,185,129,.15)','color'=>'#10b981'],
    ['label'=>'Matrículas','val'=>$stats['total_matriculas'],'icon'=>'ti-check-box','bg'=>'rgba(245,158,11,.15)','color'=>'#f59e0b'],
    ['label'=>'Completados','val'=>$stats['total_completados'],'icon'=>'ti-star','bg'=>'rgba(239,68,68,.15)','color'=>'#ef4444'],
];
foreach($st as $s): ?>
<div class="col-xl col-md-4 col-6">
<div class="stat-card d-flex align-items-center gap-3">
    <div class="stat-icon" style="background:<?=$s['bg']?>;color:<?=$s['color']?>;"><i class="<?=$s['icon']?>"></i></div>
    <div><div class="fw-bold fs-4"><?=$s['val']?></div><div class="text-cl-muted small"><?=$s['label']?></div></div>
</div>
</div>
<?php endforeach; ?>
</div>

<div class="row g-3">
<!-- Cursos más populares -->
<div class="col-lg-5">
<div class="card"><div class="card-header"><span class="fw-semibold">🏆 Cursos más populares</span></div>
<div class="card-body p-0">
<table class="table mb-0">
<thead><tr><th>#</th><th>Curso</th><th>Alumnos</th></tr></thead>
<tbody>
<?php foreach($cursos_pop as $idx=>$c): ?>
<tr><td><span class="fw-bold" style="color:var(--cl-accent2);"><?=$idx+1?></span></td>
<td><?=htmlspecialchars($c->curs_nombre)?></td>
<td><span class="badge" style="background:rgba(124,58,237,.15);color:var(--cl-accent2);"><?=$c->total?></span></td></tr>
<?php endforeach; ?>
<?php if(empty($cursos_pop)): ?><tr><td colspan="3" class="text-center py-3 text-cl-muted">Sin datos</td></tr><?php endif; ?>
</tbody></table>
</div></div>
</div>

<!-- Progreso de alumnos -->
<div class="col-lg-7">
<div class="card"><div class="card-header"><span class="fw-semibold">📈 Actividad de Alumnos</span></div>
<div class="card-body p-0">
<table class="table mb-0">
<thead><tr><th>Alumno</th><th>Inscritos</th><th>Completados</th><th>Lecciones vistas</th></tr></thead>
<tbody>
<?php foreach($alumnos_prog as $a): ?>
<tr>
    <td><span class="fw-medium"><?=htmlspecialchars($a->usua_paterno.', '.$a->usua_nombres)?></span></td>
    <td><?=$a->cursos_inscritos?></td>
    <td><span class="badge" style="background:rgba(16,185,129,.15);color:#10b981;"><?=$a->cursos_completados?></span></td>
    <td>
        <div class="d-flex align-items-center gap-2">
            <div class="progress flex-grow-1" style="max-width:80px;"><div class="progress-bar" style="width:<?=min(100,$a->lecciones_vistas*5)?>%"></div></div>
            <small><?=$a->lecciones_vistas?></small>
        </div>
    </td>
</tr>
<?php endforeach; ?>
<?php if(empty($alumnos_prog)): ?><tr><td colspan="4" class="text-center py-3 text-cl-muted">Sin datos</td></tr><?php endif; ?>
</tbody></table>
</div></div>
</div>
</div>
