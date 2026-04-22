<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">🗂️ Grupos de Asesoría — Todos los Asesores</h4>
        <small class="text-cl-muted"><?=count($grupos)?> grupo(s) en total</small>
    </div>
</div>

<?php if(empty($grupos)): ?>
<div class="card text-center py-5">
    <div class="card-body">
        <div style="font-size:2.5rem;margin-bottom:.75rem;">🗂️</div>
        <h5 class="fw-bold mb-1">No hay grupos formados aún</h5>
        <p class="text-cl-muted mb-0">Cuando un asesor forme un grupo aparecerá aquí.</p>
    </div>
</div>
<?php else: ?>

<!-- Buscador -->
<div class="mb-3">
    <input type="text" class="form-control" style="max-width:350px;"
           placeholder="Buscar por asesor, curso o grupo..."
           oninput="filtrarGruposAdmin(this.value)">
</div>

<div class="card"><div class="card-body p-0">
<table class="table mb-0" id="tablaGruposAdmin">
<thead><tr>
    <th>Grupo</th>
    <th>Curso</th>
    <th>Profesor</th>
    <th>Asesor</th>
    <th style="text-align:center;">Alumnos</th>
    <th style="text-align:center;">Mensajes</th>
    <th>Creado</th>
    <th>Acciones</th>
</tr></thead>
<tbody>
<?php foreach($grupos as $g): ?>
<tr class="fila-admin-grupo"
    data-buscar="<?=strtolower(htmlspecialchars($g->grup_nombre.' '.$g->curs_nombre.' '.$g->ases_paterno.' '.$g->ases_nombres))?>">
    <td>
        <div class="d-flex align-items-center gap-2">
            <div style="width:32px;height:32px;border-radius:.375rem;flex-shrink:0;
                        background:linear-gradient(135deg,var(--cl-accent),#06b6d4);
                        display:flex;align-items:center;justify-content:center;">
                <i class="ti-layers" style="font-size:.85rem;color:#fff;"></i>
            </div>
            <div>
                <div class="fw-medium" style="font-size:.88rem;">
                    <?=htmlspecialchars($g->grup_nombre)?>
                </div>
            </div>
        </div>
    </td>
    <td>
        <div style="font-size:.82rem;"><?=htmlspecialchars($g->curs_nombre)?></div>
        <span class="badge badge-nivel-<?=$g->curs_nivel?>"><?=$g->curs_nivel?></span>
    </td>
    <td>
        <small><?=htmlspecialchars($g->prof_paterno.' '.$g->prof_nombres)?></small>
    </td>
    <td>
        <div class="d-flex align-items-center gap-1">
            <div style="width:24px;height:24px;border-radius:50%;flex-shrink:0;
                        background:rgba(124,58,237,.2);
                        display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;">
                <?=strtoupper(substr($g->ases_nombres,0,1))?>
            </div>
            <small><?=htmlspecialchars($g->ases_paterno.' '.$g->ases_nombres)?></small>
        </div>
    </td>
    <td style="text-align:center;">
        <span class="badge" style="background:rgba(124,58,237,.15);color:var(--cl-accent2);">
            <?=$g->total_alumnos?>
        </span>
    </td>
    <td style="text-align:center;">
        <span class="badge <?=$g->total_mensajes>0?'bg-success':'bg-secondary'?>">
            <?=$g->total_mensajes?>
        </span>
    </td>
    <td>
        <small class="text-cl-muted"><?=date('d/m/Y', strtotime($g->grup_create_at))?></small>
    </td>
    <td>
        <button class="btn btn-xs btn-outline-primary"
                style="padding:2px 10px;font-size:.72rem;"
                onclick="cargarFuncion('/asesor/chat/<?=$g->grup_ide?>','Asesoría','<?=addslashes(htmlspecialchars($g->grup_nombre))?>','')">
            <i class="ti-eye me-1"></i>Ver Chat
        </button>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div>
<?php endif; ?>

<script>
function filtrarGruposAdmin(txt) {
    txt = txt.toLowerCase();
    document.querySelectorAll('.fila-admin-grupo').forEach(tr => {
        tr.style.display = !txt || (tr.dataset.buscar||'').includes(txt) ? '' : 'none';
    });
}
</script>
