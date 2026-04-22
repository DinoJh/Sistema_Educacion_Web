<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">🗂️ Mis Grupos de Asesoría</h4>
        <small class="text-cl-muted"><?=count($grupos)?> grupo(s) formado(s)</small>
    </div>
    <button class="btn btn-outline-primary btn-sm"
            onclick="cargarFuncion('/asesor/cursos','Asesoría','Mis Cursos','')">
        <i class="ti-plus me-1"></i>Formar nuevo grupo
    </button>
</div>

<?php if(empty($grupos)): ?>
<div class="card text-center py-5">
    <div class="card-body">
        <div style="font-size:2.5rem;margin-bottom:.75rem;">🗂️</div>
        <h5 class="fw-bold mb-1">Aún no tienes grupos</h5>
        <p class="text-cl-muted mb-3">Ve a <strong>Mis Cursos</strong>, selecciona un curso y forma tu primer grupo.</p>
        <button class="btn btn-primary"
                onclick="cargarFuncion('/asesor/cursos','Asesoría','Mis Cursos','')">
            <i class="ti-book me-1"></i>Ver Cursos
        </button>
    </div>
</div>
<?php else: ?>

<div class="row g-3">
<?php foreach($grupos as $g): ?>
<div class="col-xl-4 col-lg-6">
<div class="card h-100">
    <!-- Encabezado del grupo -->
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div style="width:42px;height:42px;border-radius:.5rem;
                        background:linear-gradient(135deg,var(--cl-accent),#06b6d4);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti-layers" style="font-size:1.2rem;color:#fff;"></i>
            </div>
            <small class="text-cl-muted"><?=date('d/m/Y', strtotime($g->grup_create_at))?></small>
        </div>

        <h6 class="fw-bold mb-1"><?=htmlspecialchars($g->grup_nombre)?></h6>

        <!-- Info del grupo -->
        <div class="p-2 mb-3" style="background:rgba(255,255,255,.04);border-radius:.375rem;font-size:.78rem;">
            <div class="mb-1">
                <span class="text-cl-muted">Curso:</span>
                <strong class="ms-1"><?=htmlspecialchars($g->curs_nombre)?></strong>
                <span class="badge badge-nivel-<?=$g->curs_nivel?> ms-1"><?=$g->curs_nivel?></span>
            </div>
            <div>
                <span class="text-cl-muted">Profesor:</span>
                <span class="ms-1"><?=htmlspecialchars($g->prof_paterno.' '.$g->prof_nombres)?></span>
            </div>
        </div>

        <!-- Métricas -->
        <div class="d-flex gap-3" style="font-size:.8rem;">
            <span><i class="ti-user me-1" style="color:var(--cl-accent2);"></i>
                  <strong><?=$g->total_alumnos?></strong> alumno(s)</span>
            <span><i class="ti-comment-alt me-1" style="color:#10b981;"></i>
                  <strong><?=$g->total_mensajes?></strong> mensaje(s)</span>
        </div>
    </div>

    <div class="card-footer p-2">
        <button class="btn btn-primary btn-sm w-100"
                onclick="cargarFuncion('/asesor/chat/<?=$g->grup_ide?>','Asesoría','<?=addslashes(htmlspecialchars($g->grup_nombre))?>','')">
            <i class="ti-comment-alt me-1"></i>Abrir Chat del Grupo
        </button>
    </div>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
