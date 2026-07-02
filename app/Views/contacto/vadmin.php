<!-- Stats rapidos -->
<?php
$nuevos    = array_filter($mensajes, fn($m) => $m->cont_leida == 0);
$pendientes= array_filter($mensajes, fn($m) => $m->cont_leida == 1);
$resueltos = array_filter($mensajes, fn($m) => $m->cont_leida == 2);
?>
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">📬 Contacto y Soporte</h4>
        <small class="text-cl-muted">Mensajes, quejas y sugerencias de los usuarios</small>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-danger" style="font-size:.8rem;padding:6px 12px;">
            <?=count($nuevos)?> nuevo(s)
        </span>
        <span class="badge bg-warning text-dark" style="font-size:.8rem;padding:6px 12px;">
            <?=count($pendientes)?> pendiente(s)
        </span>
        <span class="badge bg-success" style="font-size:.8rem;padding:6px 12px;">
            <?=count($resueltos)?> resuelto(s)
        </span>
    </div>
</div>

<?php if(empty($mensajes)): ?>
<div class="card text-center py-5">
    <div class="card-body">
        <div style="font-size:2.5rem;margin-bottom:.75rem;">📭</div>
        <h5 class="fw-bold mb-1">No hay mensajes</h5>
        <p class="text-cl-muted mb-0">Cuando algún usuario envíe un mensaje aparecerá aquí.</p>
    </div>
</div>
<?php else: ?>

<!-- Filtro de tipo -->
<div class="d-flex gap-2 mb-3 flex-wrap">
    <button class="btn btn-sm btn-outline-secondary active" id="filtroTodos" onclick="filtrarContacto('todos',this)">Todos</button>
    <button class="btn btn-sm btn-outline-danger"           id="filtroQUEJA"       onclick="filtrarContacto('QUEJA',this)">Quejas</button>
    <button class="btn btn-sm btn-outline-warning"          id="filtroRECLAMO"     onclick="filtrarContacto('RECLAMO',this)">Reclamos</button>
    <button class="btn btn-sm btn-outline-info"             id="filtroSUGERENCIA"  onclick="filtrarContacto('SUGERENCIA',this)">Sugerencias</button>
    <button class="btn btn-sm btn-outline-secondary"        id="filtroCONSULTA"    onclick="filtrarContacto('CONSULTA',this)">Consultas</button>
</div>

<div id="listaContactos">
<?php foreach($mensajes as $m):
    $tipoCls = ['QUEJA'=>'danger','RECLAMO'=>'warning','SUGERENCIA'=>'info','CONSULTA'=>'secondary'];
    $estaCls = ['0'=>'danger','1'=>'warning','2'=>'success'];
    $estaLbl = ['0'=>'Nuevo','1'=>'Visto','2'=>'Respondido'];
?>
<div class="card mb-2 cont-item" data-tipo="<?=$m->cont_tipo?>"
     style="<?=$m->cont_leida==0?'border-color:rgba(239,68,68,.4)!important;':''?>">
<div class="card-body py-2 px-3">
<div class="d-flex align-items-start gap-3 flex-wrap">

    <!-- Avatar + nombre -->
    <div style="min-width:160px;">
        <div class="d-flex align-items-center gap-2 mb-1">
            <div style="width:28px;height:28px;border-radius:50%;flex-shrink:0;
                        background:linear-gradient(135deg,var(--cl-accent),#06b6d4);
                        display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;">
                <?=strtoupper(substr($m->usua_nombres??'?',0,1))?>
            </div>
            <div>
                <div class="fw-medium" style="font-size:.82rem;">
                    <?=htmlspecialchars($m->usua_paterno.' '.$m->usua_nombres)?>
                </div>
                <small class="text-cl-muted" style="font-size:.68rem;">
                    <?=htmlspecialchars($m->perf_nombre??'')?>
                </small>
            </div>
        </div>
        <div class="d-flex gap-1">
            <span class="badge bg-<?=$tipoCls[$m->cont_tipo]??'secondary'?>"><?=$m->cont_tipo?></span>
            <span class="badge bg-<?=$estaCls[$m->cont_leida]??'secondary'?>"><?=$estaLbl[$m->cont_leida]??'?'?></span>
        </div>
    </div>

    <!-- Contenido -->
    <div style="flex:1;min-width:200px;">
        <div class="fw-medium mb-1" style="font-size:.88rem;">
            <?=htmlspecialchars($m->cont_asunto)?>
        </div>
        <div class="text-cl-muted" style="font-size:.8rem;line-height:1.4;">
            <?=nl2br(htmlspecialchars($m->cont_mensaje))?>
        </div>
        <?php if($m->cont_respuesta): ?>
        <div class="mt-2 p-2 rounded" style="background:rgba(16,185,129,.08);border-left:3px solid #10b981;font-size:.78rem;">
            <strong style="color:#10b981;"><i class="ti-arrow-right me-1"></i>Respuesta del admin:</strong>
            <div class="mt-1"><?=nl2br(htmlspecialchars($m->cont_respuesta))?></div>
            <small class="text-cl-muted"><?=date('d/m/Y H:i', strtotime($m->cont_resp_at??$m->cont_create_at))?></small>
        </div>
        <?php endif; ?>
    </div>

    <!-- Acciones -->
    <div class="text-end" style="min-width:120px;">
        <small class="text-cl-muted d-block mb-2" style="font-size:.68rem;">
            <?=date('d/m/Y H:i', strtotime($m->cont_create_at))?>
        </small>
        <?php if($m->cont_leida != 2): ?>
        <button class="btn btn-xs btn-outline-success" style="font-size:.72rem;padding:3px 10px;"
                onclick="abrirRespuesta(<?=$m->cont_ide?>,'<?=addslashes(htmlspecialchars($m->cont_asunto))?>','<?=addslashes(htmlspecialchars($m->usua_paterno.' '.$m->usua_nombres))?>')">
            <i class="ti-arrow-right me-1"></i>Responder
        </button>
        <?php else: ?>
        <span class="badge bg-success" style="font-size:.7rem;">Respondido</span>
        <?php endif; ?>
    </div>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ═══ MODAL RESPONDER ═══ -->
<div class="cl-overlay" id="ov-responder">
<div class="cl-modal">
    <div class="cl-modal-hdr">
        <h5><i class="ti-arrow-right me-2"></i>Responder mensaje</h5>
        <button class="cl-modal-close" onclick="clCerrar('ov-responder')">✕</button>
    </div>
    <div class="cl-modal-body">
        <input type="hidden" id="respContIde">
        <div class="mb-2 p-2 rounded" style="background:rgba(255,255,255,.04);font-size:.82rem;">
            Para: <strong id="respDestinatario"></strong><br>
            Asunto: <span id="respAsunto" style="color:var(--cl-accent2);"></span>
        </div>
        <div class="mb-3">
            <label class="form-label small text-cl-muted">Tu respuesta *</label>
            <textarea id="respMensaje" class="form-control" rows="4"
                      placeholder="Escribe la respuesta al usuario..."></textarea>
        </div>
    </div>
    <div class="cl-modal-ftr">
        <button class="btn btn-secondary" onclick="clCerrar('ov-responder')">Cancelar</button>
        <button class="btn btn-success" onclick="enviarRespuesta()">
            <i class="ti-arrow-right me-1"></i>Enviar respuesta
        </button>
    </div>
</div>
</div>

<script>
function filtrarContacto(tipo, btn) {
    document.querySelectorAll('#listaContactos .cont-item').forEach(el => {
        el.style.display = (tipo === 'todos' || el.dataset.tipo === tipo) ? '' : 'none';
    });
    document.querySelectorAll('[id^="filtro"]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function abrirRespuesta(ide, asunto, dest) {
    document.getElementById('respContIde').value    = ide;
    document.getElementById('respDestinatario').textContent = dest;
    document.getElementById('respAsunto').textContent       = asunto;
    document.getElementById('respMensaje').value    = '';
    clAbrir('ov-responder');
}

function enviarRespuesta() {
    var ide  = document.getElementById('respContIde').value;
    var resp = document.getElementById('respMensaje').value.trim();
    if (!resp) { alertar('Escribe la respuesta primero.','alert alert-warning','ti-alert'); return; }

    clCerrar('ov-responder');
    openCargar();
    $.post('<?=base_url('/contacto/responder')?>', {
        cont_ide:  ide,
        respuesta: resp
    }, function(r) {
        r = JSON.parse(r); closeCargar();
        if (r.ok) {
            alertar(r.msg,'alert alert-success','ti-check');
            setTimeout(() => cargarFuncion('/contacto/admin','Soporte','Contacto / Soporte',''), 1100);
        } else {
            alertar(r.msg,'alert alert-danger','ti-close');
        }
    });
}
</script>
