<div class="mb-4">
    <h4 class="fw-bold mb-1">📈 Mi Progreso</h4>
    <p class="text-cl-muted small">Resumen de tus cursos inscritos</p>
</div>
<?php if(empty($matriculas)): ?>
<div class="text-center py-5 card"><div class="card-body">
    <i class="ti-book fs-1 text-cl-muted"></i>
    <p class="mt-2 text-cl-muted">Aún no estás inscrito en ningún curso.</p>
    <button class="btn btn-primary" onclick="cargarFuncion('/mi-panel/cursos','Mi Panel','Explorar Cursos','')">Explorar Cursos</button>
</div></div>
<?php endif; ?>
<div class="row g-3">
<?php foreach($matriculas as $m):
    $pct = $m->total_lecc > 0 ? round(($m->lecc_hechas / $m->total_lecc)*100) : 0; ?>
<div class="col-md-6 col-lg-4">
<div class="card h-100">
  <div class="card-body">
    <div class="d-flex justify-content-between mb-2">
      <span class="badge badge-nivel-<?=$m->curs_nivel?>"><?=$m->curs_nivel?></span>
      <?php if($m->matr_completado): ?>
      <span class="badge" style="background:rgba(16,185,129,.2);color:#10b981;">🎓 Completado</span>
      <?php endif; ?>
    </div>
    <h6 class="fw-bold mb-1"><?=htmlspecialchars($m->curs_nombre)?></h6>
    <small class="text-cl-muted d-block mb-3"><i class="ti-user me-1"></i><?=htmlspecialchars($m->prof_nombre.' '.$m->prof_paterno)?></small>
    <div class="d-flex align-items-center gap-2 mb-1">
        <div class="progress flex-grow-1"><div class="progress-bar" style="width:<?=$pct?>%"></div></div>
        <small class="fw-semibold"><?=$pct?>%</small>
    </div>
    <small class="text-cl-muted"><?=$m->lecc_hechas?> / <?=$m->total_lecc?> lecciones completadas</small>
  </div>
  <div class="card-footer">
    <button class="btn btn-sm btn-outline-primary w-100" onclick="cargarFuncion('/mi-panel/ver/<?=$m->curs_ide?>','Mi Panel','Ver Curso','')">
        <?=$pct>0?'<i class="ti-arrow-right me-1"></i>Continuar':'<i class="ti-play me-1"></i>Comenzar'?>
    </button>
  </div>
</div>
</div>
<?php endforeach; ?>
</div>
