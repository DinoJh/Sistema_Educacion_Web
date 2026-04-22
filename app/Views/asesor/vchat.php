<!-- Breadcrumb -->
<div class="d-flex align-items-center gap-2 mb-3" style="font-size:.85rem;">
    <?php if($session->perf_ide == 3): ?>
    <a href="#" onclick="cargarFuncion('/asesor/admin-grupos','Asesoría','Todos los Grupos','')" class="text-cl-muted">
        <i class="ti-arrow-left me-1"></i>Volver a Grupos
    </a>
    <?php else: ?>
    <a href="#" onclick="cargarFuncion('/asesor/grupos','Asesoría','Mis Grupos','')" class="text-cl-muted">
        <i class="ti-arrow-left me-1"></i>Volver a Mis Grupos
    </a>
    <?php endif; ?>
</div>

<!-- Ficha del grupo (info completa y ordenada) -->
<div class="card mb-3" style="border-left:4px solid var(--cl-accent2);">
    <div class="card-body py-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold mb-1">
                    <i class="ti-layers me-2" style="color:var(--cl-accent2);"></i>
                    <?=htmlspecialchars($grupo->grup_nombre)?>
                </h5>
                <div class="d-flex flex-wrap gap-3" style="font-size:.82rem;">
                    <span>
                        <span class="text-cl-muted">Curso:</span>
                        <strong class="ms-1"><?=htmlspecialchars($grupo->curs_nombre)?></strong>
                        <span class="badge badge-nivel-<?=$grupo->curs_nivel?> ms-1"><?=$grupo->curs_nivel?></span>
                    </span>
                    <span>
                        <span class="text-cl-muted">Profesor:</span>
                        <span class="ms-1"><?=htmlspecialchars($grupo->prof_paterno.' '.$grupo->prof_nombres)?></span>
                    </span>
                    <span>
                        <span class="text-cl-muted">Asesor:</span>
                        <strong class="ms-1"><?=htmlspecialchars($grupo->ases_paterno.' '.$grupo->ases_nombres)?></strong>
                    </span>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <small class="text-cl-muted">
                    <i class="ti-user me-1"></i><?=$grupo->total_alumnos?> alumno(s) en el grupo<br>
                    Creado: <?=date('d/m/Y H:i', strtotime($grupo->grup_create_at))?>
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Panel izquierdo: alumnos del grupo con progreso -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="px-3 pt-3 pb-2 border-bottom" style="border-color:rgba(255,255,255,.08)!important;">
                    <h6 class="fw-bold mb-0">
                        <i class="ti-user me-1" style="color:var(--cl-accent2);"></i>
                        Alumnos del Grupo
                    </h6>
                </div>
                <?php if(empty($alumnos)): ?>
                <div class="text-center py-4 text-cl-muted"><small>Sin alumnos en este grupo.</small></div>
                <?php endif; ?>
                <?php foreach($alumnos as $a):
                    $pct  = $a->total_lecc > 0 ? round(($a->lecc_hechas / $a->total_lecc) * 100) : 0;
                    $col  = $pct >= 80 ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#ef4444');
                ?>
                <div class="px-3 py-2" style="border-bottom:1px solid rgba(255,255,255,.05);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div style="width:28px;height:28px;border-radius:50%;
                                    background:linear-gradient(135deg,var(--cl-accent),#06b6d4);
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:.65rem;font-weight:700;flex-shrink:0;">
                            <?=strtoupper(substr($a->usua_nombres,0,1))?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="fw-medium" style="font-size:.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?=htmlspecialchars($a->usua_paterno.', '.$a->usua_nombres)?>
                            </div>
                            <small class="text-cl-muted" style="font-size:.68rem;"><?=htmlspecialchars($a->usua_email??'')?></small>
                        </div>
                    </div>
                    <!-- Barra de progreso -->
                    <div class="d-flex align-items-center gap-2">
                        <div style="flex:1;height:4px;background:rgba(255,255,255,.08);border-radius:99px;overflow:hidden;">
                            <div style="width:<?=$pct?>%;height:100%;background:<?=$col?>;"></div>
                        </div>
                        <small style="font-size:.68rem;color:<?=$col?>;width:28px;text-align:right;">
                            <?=$pct?>%
                        </small>
                    </div>
                    <small class="text-cl-muted" style="font-size:.68rem;">
                        <?=$a->lecc_hechas?>/<?=$a->total_lecc?> lecciones completadas
                    </small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Panel derecho: chat -->
    <div class="col-lg-8">
        <div class="card d-flex flex-column" style="height:520px;">
            <div class="card-body p-0 d-flex flex-column" style="overflow:hidden;">

                <!-- Cabecera del chat -->
                <div class="px-3 py-2 d-flex align-items-center gap-2"
                     style="border-bottom:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.03);">
                    <i class="ti-comment-alt" style="color:var(--cl-accent2);"></i>
                    <span class="fw-medium" style="font-size:.9rem;">Chat del Grupo</span>
                    <span class="badge ms-auto" style="background:rgba(124,58,237,.2);color:var(--cl-accent2);">
                        <?=count($mensajes)?> mensaje(s)
                    </span>
                </div>

                <!-- Área de mensajes -->
                <div id="areaMensajes" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;">
                    <?php if(empty($mensajes)): ?>
                    <div class="text-center text-cl-muted" style="margin:auto;font-size:.85rem;">
                        <i class="ti-comment-alt" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i>
                        Ningún mensaje todavía.<br>
                        <small>Sé el primero en escribir al grupo.</small>
                    </div>
                    <?php endif; ?>

                    <?php foreach($mensajes as $m):
                        $esPropio = ($m->usua_perf_ide == 4 || $m->usua_perf_ide == 3);
                        $esAdmin  = ($m->usua_perf_ide == 3);
                        $esMio    = ($m->usua_perf_ide == $session->perf_ide); // simplificado
                        $align    = $esPropio ? 'flex-end' : 'flex-start';
                        $bgMsg    = $esAdmin
                            ? 'rgba(16,185,129,.15)'
                            : ($esPropio ? 'rgba(124,58,237,.2)' : 'rgba(255,255,255,.06)');
                        $roleTag  = $esAdmin ? '<span style="font-size:.6rem;background:rgba(16,185,129,.25);color:#10b981;padding:1px 6px;border-radius:99px;margin-left:4px;">ADMIN</span>'
                                  : ($esPropio ? '<span style="font-size:.6rem;background:rgba(124,58,237,.25);color:var(--cl-accent2);padding:1px 6px;border-radius:99px;margin-left:4px;">ASESOR</span>' : '');
                    ?>
                    <div style="display:flex;flex-direction:column;align-items:<?=$align?>;max-width:75%;<?=$esPropio?'align-self:flex-end':'align-self:flex-start'?>">
                        <small class="text-cl-muted mb-1" style="font-size:.68rem;">
                            <?=htmlspecialchars($m->usua_paterno.' '.$m->usua_nombres)?><?=$roleTag?>
                        </small>
                        <div style="background:<?=$bgMsg?>;padding:8px 12px;border-radius:.5rem;
                                    font-size:.83rem;line-height:1.4;word-break:break-word;">
                            <?=nl2br(htmlspecialchars($m->grum_mensaje))?>
                        </div>
                        <small class="text-cl-muted mt-1" style="font-size:.65rem;">
                            <?=date('d/m/Y H:i', strtotime($m->grum_create_at))?>
                        </small>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Caja de envío (solo ASESOR dueño o ADMIN) -->
                <?php if($session->perf_ide == 3 || $session->perf_ide == 4): ?>
                <div class="p-3" style="border-top:1px solid rgba(255,255,255,.08);">
                    <div class="d-flex gap-2">
                        <textarea id="txtMensaje" class="form-control" rows="2"
                                  placeholder="Escribe un mensaje para el grupo..."
                                  style="resize:none;font-size:.85rem;"
                                  onkeydown="if(event.ctrlKey&&event.key==='Enter')enviarMensaje()"></textarea>
                        <button class="btn btn-primary" style="padding:0 18px;" onclick="enviarMensaje()"
                                title="Enviar (Ctrl+Enter)">
                            <i class="ti-arrow-right"></i>
                        </button>
                    </div>
                    <small class="text-cl-muted" style="font-size:.68rem;">
                        <kbd>Ctrl</kbd>+<kbd>Enter</kbd> para enviar rápido
                    </small>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script>
// Scroll al fondo del chat al cargar
(function(){
    var area = document.getElementById('areaMensajes');
    if(area) area.scrollTop = area.scrollHeight;
})();

function enviarMensaje() {
    var txt = document.getElementById('txtMensaje');
    var msg = txt.value.trim();
    if (!msg) { alertar('Escribe un mensaje primero.','alert alert-warning','ti-alert'); return; }

    openCargar();
    $.post('<?=base_url('/asesor/enviar-mensaje')?>', {
        grup_ide: <?=$grupo->grup_ide?>,
        mensaje:  msg
    }, function(r) {
        try { r = JSON.parse(r); } catch(e){}
        closeCargar();
        if (r.ok) {
            txt.value = '';
            // Recargar la vista del chat
            cargarFuncion(
                '/asesor/chat/<?=$grupo->grup_ide?>',
                'Asesoría',
                '<?=addslashes(htmlspecialchars($grupo->grup_nombre))?>',
                ''
            );
        } else {
            alertar(r.msg,'alert alert-danger','ti-close');
        }
    });
}
</script>
