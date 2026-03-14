<!-- MIS CURSOS: solo los matriculados -->
<div class="mb-4">
    <h4 class="fw-bold mb-1">📚 Mis Cursos</h4>
    <p class="text-cl-muted small">Cursos en los que estás inscrito</p>
</div>

<?php if(empty($matriculas)): ?>
<div class="card text-center py-5"><div class="card-body">
    <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
    <h5 class="fw-bold mb-2">Aún no estás inscrito en ningún curso</h5>
    <p class="text-cl-muted mb-3">Explora el catálogo y encuentra el curso perfecto para ti.</p>
    <button class="btn btn-primary px-4" onclick="cargarFuncion('/mi-panel/catalogo','Mi Panel','Buscar Cursos','')">
        <i class="ti-search me-1"></i>Buscar Cursos
    </button>
</div></div>
<?php else: ?>
<div class="row g-3">
<?php foreach($matriculas as $m):
    $pct = $m->total_lecc > 0 ? round(($m->lecc_hechas/$m->total_lecc)*100) : 0; ?>
<div class="col-xl-3 col-lg-4 col-md-6">
<div class="card curso-card h-100" onclick="cargarFuncion('/mi-panel/ver/<?=$m->curs_ide?>','Mi Panel','<?=addslashes($m->curs_nombre)?>','')">
    <div class="card-img-top d-flex align-items-center justify-content-center" style="height:120px;background:linear-gradient(135deg,#1e1b4b,#0d0f18);position:relative;">
        <i class="ti-code" style="font-size:2.5rem;color:var(--cl-accent2);opacity:.45;"></i>
        <?php if($m->matr_completado): ?>
        <div style="position:absolute;top:8px;right:8px;background:rgba(16,185,129,.9);color:#fff;font-size:.65rem;padding:2px 8px;border-radius:99px;font-weight:600;">🎓 COMPLETADO</div>
        <?php endif; ?>
    </div>
    <div class="card-body d-flex flex-column">
        <div class="mb-2"><span class="badge badge-nivel-<?=$m->curs_nivel?>"><?=$m->curs_nivel?></span></div>
        <h6 class="fw-bold mb-1"><?=htmlspecialchars($m->curs_nombre)?></h6>
        <small class="text-cl-muted mb-3"><?=$m->cate_nombre?></small>
        <div class="mt-auto">
            <div class="d-flex justify-content-between mb-1">
                <small class="text-cl-muted">Progreso</small>
                <small class="fw-semibold"><?=$pct?>%</small>
            </div>
            <div class="progress mb-2"><div class="progress-bar" style="width:<?=$pct?>%"></div></div>
            <small class="text-cl-muted"><?=$m->lecc_hechas?>/<?=$m->total_lecc?> lecciones</small>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-sm <?=$pct>0?'btn-success':'btn-primary'?> w-100">
            <?=$pct>0?'<i class="ti-arrow-right me-1"></i>Continuar':'<i class="ti-play me-1"></i>Comenzar'?>
        </button>
    </div>
</div>
</div>
<?php endforeach; ?>
</div>
<div class="mt-4 text-center">
    <button class="btn btn-outline-primary" onclick="cargarFuncion('/mi-panel/catalogo','Mi Panel','Buscar Cursos','')">
        <i class="ti-search me-2"></i>Explorar más cursos
    </button>
</div>
<?php endif; ?>
