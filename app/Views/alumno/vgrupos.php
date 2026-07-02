<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">🗂️ Mis Grupos de Asesoría</h4>
        <small class="text-cl-muted">Aquí aparecen los grupos donde un asesor te acompaña en tus cursos</small>
    </div>
    <span class="badge" style="background:rgba(124,58,237,.15);color:var(--cl-accent2);font-size:.85rem;padding:6px 14px;">
        <?=count($grupos)?> grupo(s)
    </span>
</div>

<?php if(empty($grupos)): ?>
<div class="card text-center py-5">
    <div class="card-body">
        <div style="font-size:3rem;margin-bottom:1rem;">🎧</div>
        <h5 class="fw-bold mb-2">Aún no tienes grupos de asesoría</h5>
        <p class="text-cl-muted mb-0" style="max-width:380px;margin:auto;">
            Cuando un asesor te incluya en un grupo de uno de tus cursos,
            aparecerá aquí y recibirás una notificación.
        </p>
    </div>
</div>
<?php else: ?>

<div class="row g-3">
<?php foreach($grupos as $g):
    $hasMsgsNuevos = ($g->msgs_nuevos > 0);
?>
<div class="col-xl-4 col-lg-6">
<div class="card h-100" style="<?=$hasMsgsNuevos?'border-color:rgba(124,58,237,.5)!important;':''?>">

    <?php if($hasMsgsNuevos): ?>
    <div class="px-3 pt-2" style="background:rgba(124,58,237,.08);border-radius:.375rem .375rem 0 0;">
        <small style="color:var(--cl-accent2);font-size:.75rem;font-weight:600;">
            <i class="ti-comment-alt me-1"></i><?=$g->msgs_nuevos?> mensaje(s) nuevo(s)
        </small>
    </div>
    <?php endif; ?>

    <div class="card-body">
        <!-- Icono + fecha -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div style="width:42px;height:42px;border-radius:.5rem;
                        background:linear-gradient(135deg,var(--cl-accent),#06b6d4);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti-layers" style="font-size:1.2rem;color:#fff;"></i>
            </div>
            <small class="text-cl-muted"><?=date('d/m/Y', strtotime($g->grup_create_at))?></small>
        </div>

        <!-- Nombre del grupo -->
        <h6 class="fw-bold mb-3"><?=htmlspecialchars($g->grup_nombre)?></h6>

        <!-- Ficha de información del grupo -->
        <div class="p-2 mb-3"
             style="background:rgba(255,255,255,.04);border-radius:.375rem;font-size:.78rem;
                    border-left:3px solid var(--cl-accent2);">
            <div class="mb-1">
                <span class="text-cl-muted">Curso:</span>
                <strong class="ms-1"><?=htmlspecialchars($g->curs_nombre)?></strong>
                <span class="badge badge-nivel-<?=$g->curs_nivel?> ms-1"><?=$g->curs_nivel?></span>
            </div>
            <div class="mb-1">
                <span class="text-cl-muted">Profesor:</span>
                <span class="ms-1"><?=htmlspecialchars($g->prof_paterno.' '.$g->prof_nombres)?></span>
            </div>
            <div>
                <span class="text-cl-muted">Asesor:</span>
                <strong class="ms-1" style="color:var(--cl-accent2);">
                    <?=htmlspecialchars($g->ases_paterno.' '.$g->ases_nombres)?>
                </strong>
            </div>
        </div>

        <!-- Métricas -->
        <div class="d-flex gap-3" style="font-size:.8rem;">
            <span>
                <i class="ti-user me-1" style="color:var(--cl-accent2);"></i>
                <strong><?=$g->total_miembros?></strong> miembro(s)
            </span>
            <span>
                <i class="ti-comment-alt me-1" style="color:#10b981;"></i>
                <strong><?=$g->total_mensajes?></strong> mensaje(s)
            </span>
        </div>
    </div>

    <div class="card-footer p-2">
        <button class="btn btn-sm w-100 <?=$hasMsgsNuevos?'btn-primary':'btn-outline-primary'?>"
                onclick="cargarFuncion('/asesor/chat/<?=$g->grup_ide?>','Asesoría','<?=addslashes(htmlspecialchars($g->grup_nombre))?>','')">
            <i class="ti-comment-alt me-1"></i>
            <?=$hasMsgsNuevos?'Ver mensajes nuevos':'Abrir Chat'?>
        </button>
    </div>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
