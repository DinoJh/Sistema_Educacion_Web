<!-- Breadcrumb -->
<div class="d-flex align-items-center gap-2 mb-3" style="font-size:.85rem;">
    <?php if($session->perf_ide == 3): ?>
    <a href="#" onclick="cargarFuncion('/asesor/admin-grupos','Asesoría','Todos los Grupos','')" class="text-cl-muted">
        <i class="ti-arrow-left me-1"></i>Volver a Grupos
    </a>
    <?php elseif($session->perf_ide == 4): ?>
    <a href="#" onclick="cargarFuncion('/asesor/grupos','Asesoría','Mis Grupos','')" class="text-cl-muted">
        <i class="ti-arrow-left me-1"></i>Volver a Mis Grupos
    </a>
    <?php else: ?>
    <a href="#" onclick="cargarFuncion('/alumno/grupos','Asesoría','Mis Grupos de Asesoría','')" class="text-cl-muted">
        <i class="ti-arrow-left me-1"></i>Volver a Mis Grupos
    </a>
    <?php endif; ?>
</div>

<!-- Ficha del grupo -->
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
            <div class="col-md-4 text-md-end d-flex flex-column align-items-md-end gap-2">
                <small class="text-cl-muted">
                    <i class="ti-user me-1"></i><?=$grupo->total_alumnos?> alumno(s) · Creado: <?=date('d/m/Y', strtotime($grupo->grup_create_at))?>
                </small>
                <!-- Botón Enviar Correo — solo ASESOR y ADMIN -->
                <?php if(in_array($session->perf_ide, [3, 4])): ?>
                <button class="btn btn-sm"
                        style="background:rgba(16,185,129,.15);color:#10b981;border:1px solid rgba(16,185,129,.3);"
                        onclick="clAbrir('ov-email-grupo')">
                    <i class="ti-email me-1"></i>Enviar correo al grupo
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Panel izquierdo: alumnos -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="px-3 pt-3 pb-2" style="border-bottom:1px solid rgba(255,255,255,.08);">
                    <h6 class="fw-bold mb-0">
                        <i class="ti-user me-1" style="color:var(--cl-accent2);"></i>
                        Alumnos del Grupo
                    </h6>
                </div>
                <?php foreach($alumnos as $a):
                    $pct = $a->total_lecc > 0 ? round(($a->lecc_hechas / $a->total_lecc) * 100) : 0;
                    $col = $pct >= 80 ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#ef4444');
                ?>
                <div class="px-3 py-2" style="border-bottom:1px solid rgba(255,255,255,.05);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div style="width:28px;height:28px;border-radius:50%;flex-shrink:0;
                                    background:linear-gradient(135deg,var(--cl-accent),#06b6d4);
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:.65rem;font-weight:700;">
                            <?=strtoupper(substr($a->usua_nombres,0,1))?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="fw-medium" style="font-size:.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?=htmlspecialchars($a->usua_paterno.', '.$a->usua_nombres)?>
                            </div>
                            <small class="text-cl-muted" style="font-size:.65rem;"><?=htmlspecialchars($a->usua_email??'')?></small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="flex:1;height:4px;background:rgba(255,255,255,.08);border-radius:99px;overflow:hidden;">
                            <div style="width:<?=$pct?>%;height:100%;background:<?=$col?>;"></div>
                        </div>
                        <small style="font-size:.68rem;color:<?=$col?>;width:28px;text-align:right;"><?=$pct?>%</small>
                    </div>
                    <small class="text-cl-muted" style="font-size:.67rem;"><?=$a->lecc_hechas?>/<?=$a->total_lecc?> lecciones</small>
                </div>
                <?php endforeach; ?>

                <!-- Historial de correos enviados (solo asesor/admin) -->
                <?php if(!empty($emailsLog) && in_array($session->perf_ide, [3,4])): ?>
                <div class="px-3 pt-3 pb-1" style="border-top:1px solid rgba(255,255,255,.08);">
                    <div class="fw-bold mb-2" style="font-size:.78rem;color:var(--cl-muted);">
                        <i class="ti-email me-1"></i>CORREOS ENVIADOS
                    </div>
                    <?php foreach($emailsLog as $e): ?>
                    <div class="mb-2 p-2 rounded" style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.12);font-size:.72rem;">
                        <div class="fw-medium text-cl-text mb-1" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?=htmlspecialchars($e->grem_asunto)?>
                        </div>
                        <div class="text-cl-muted">
                            <i class="ti-check" style="color:#10b981;"></i> <?=$e->grem_total?> enviado(s)
                            <?php if($e->grem_errores > 0): ?>
                            · <span style="color:#ef4444;"><i class="ti-close"></i> <?=$e->grem_errores?> error(es)</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-cl-muted"><?=date('d/m/Y H:i', strtotime($e->grem_create_at))?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Panel derecho: chat -->
    <div class="col-lg-8">
        <div class="card d-flex flex-column" style="height:520px;">
            <div class="card-body p-0 d-flex flex-column" style="overflow:hidden;">

                <div class="px-3 py-2 d-flex align-items-center gap-2"
                     style="border-bottom:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.03);">
                    <i class="ti-comment-alt" style="color:var(--cl-accent2);"></i>
                    <span class="fw-medium" style="font-size:.9rem;">Chat del Grupo</span>
                    <span class="badge ms-auto" style="background:rgba(124,58,237,.2);color:var(--cl-accent2);">
                        <?=count($mensajes)?> mensaje(s)
                    </span>
                </div>

                <div id="areaMensajes" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;">
                    <?php if(empty($mensajes)): ?>
                    <div class="text-center text-cl-muted" style="margin:auto;font-size:.85rem;">
                        <i class="ti-comment-alt" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i>
                        Ningún mensaje todavía.<br>
                        <small>Sé el primero en escribir al grupo.</small>
                    </div>
                    <?php endif; ?>

                    <?php foreach($mensajes as $m):
                        $esAsesorOAdmin = in_array($m->usua_perf_ide, [3, 4]);
                        $esMio = ($m->usua_ide == $session->usua_ide);
                        $align = $esMio ? 'flex-end' : 'flex-start';
                        $bgMsg = $m->usua_perf_ide == 3
                            ? 'rgba(16,185,129,.15)'
                            : ($esAsesorOAdmin ? 'rgba(124,58,237,.2)' : 'rgba(255,255,255,.06)');
                        $roleTag = $m->usua_perf_ide == 3
                            ? '<span style="font-size:.6rem;background:rgba(16,185,129,.25);color:#10b981;padding:1px 6px;border-radius:99px;margin-left:4px;">ADMIN</span>'
                            : ($m->usua_perf_ide == 4 ? '<span style="font-size:.6rem;background:rgba(124,58,237,.25);color:var(--cl-accent2);padding:1px 6px;border-radius:99px;margin-left:4px;">ASESOR</span>' : '');
                    ?>
                    <div style="display:flex;flex-direction:column;align-items:<?=$align?>;<?=$esMio?'align-self:flex-end':'align-self:flex-start'?>;max-width:75%;">
                        <small class="text-cl-muted mb-1" style="font-size:.68rem;">
                            <?=htmlspecialchars($m->usua_paterno.' '.$m->usua_nombres)?><?=$roleTag?>
                        </small>
                        <div style="background:<?=$bgMsg?>;padding:8px 12px;border-radius:.5rem;font-size:.83rem;line-height:1.4;word-break:break-word;">
                            <?=nl2br(htmlspecialchars($m->grum_mensaje))?>
                        </div>
                        <small class="text-cl-muted mt-1" style="font-size:.65rem;">
                            <?=date('d/m/Y H:i', strtotime($m->grum_create_at))?>
                        </small>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Caja de envío -->
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
                        <kbd>Ctrl</kbd>+<kbd>Enter</kbd> para enviar
                    </small>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- ═══ MODAL: ENVIAR CORREO AL GRUPO ═══ -->
<?php if(in_array($session->perf_ide, [3, 4])): ?>
<div class="cl-overlay" id="ov-email-grupo">
<div class="cl-modal cl-modal-lg">
    <div class="cl-modal-hdr">
        <h5><i class="ti-email me-2" style="color:#10b981;"></i>Enviar Correo a los Alumnos del Grupo</h5>
        <button class="cl-modal-close" onclick="clCerrar('ov-email-grupo')">✕</button>
    </div>
    <div class="cl-modal-body">

        <!-- Destinatarios -->
        <div class="p-3 mb-3 rounded" style="background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);">
            <div class="fw-medium mb-2" style="font-size:.82rem;color:#10b981;">
                <i class="ti-user me-1"></i>Destinatarios (<?=count($alumnos)?> alumno(s))
            </div>
            <div class="d-flex flex-wrap gap-1">
                <?php foreach($alumnos as $a): ?>
                <span class="badge" style="background:rgba(16,185,129,.15);color:#10b981;font-size:.72rem;font-weight:400;">
                    <?=htmlspecialchars($a->usua_paterno.', '.$a->usua_nombres)?>
                    <?php if($a->usua_email): ?>
                    <span style="opacity:.7;">·  <?=htmlspecialchars($a->usua_email)?></span>
                    <?php else: ?>
                    <span style="color:#f59e0b;"> · sin email</span>
                    <?php endif; ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Info del grupo (va en el email automáticamente) -->
        <div class="p-2 mb-3 rounded" style="background:rgba(124,58,237,.07);border:1px solid rgba(124,58,237,.15);font-size:.78rem;">
            <div class="text-cl-muted mb-1"><i class="ti-info-alt me-1"></i>El correo incluirá automáticamente:</div>
            <div class="d-flex flex-wrap gap-3">
                <span><strong>Grupo:</strong> <?=htmlspecialchars($grupo->grup_nombre)?></span>
                <span><strong>Curso:</strong> <?=htmlspecialchars($grupo->curs_nombre)?></span>
                <span><strong>Asesor:</strong> <?=htmlspecialchars($grupo->ases_paterno.' '.$grupo->ases_nombres)?></span>
            </div>
        </div>

        <!-- Asunto -->
        <div class="mb-3">
            <label class="form-label small text-cl-muted">Asunto del correo <span class="text-danger">*</span></label>
            <input id="emailAsunto" class="form-control"
                   placeholder="Ej: Sesión Zoom mañana a las 9:00 AM"
                   maxlength="200">
        </div>

        <!-- Cuerpo -->
        <div class="mb-2">
            <label class="form-label small text-cl-muted">Mensaje <span class="text-danger">*</span></label>
            <textarea id="emailCuerpo" class="form-control" rows="6"
                      placeholder="Escribe el contenido del correo...&#10;&#10;Ej:&#10;Hola a todos,&#10;&#10;Mañana tenemos una sesión de refuerzo por Zoom a las 9:00 AM.&#10;Link: https://zoom.us/j/ejemplo&#10;&#10;Por favor confirmen asistencia."></textarea>
            <small class="text-cl-muted">Los saltos de línea se respetan. Las URLs se convierten en links automáticamente.</small>
        </div>

    </div>
    <div class="cl-modal-ftr">
        <button class="btn btn-secondary" onclick="clCerrar('ov-email-grupo')">Cancelar</button>
        <button class="btn" id="btnEnviarEmail"
                style="background:rgba(16,185,129,.15);color:#10b981;border:1px solid rgba(16,185,129,.3);"
                onclick="confirmarEnvioEmail()">
            <i class="ti-email me-1"></i>Enviar correo a <?=count($alumnos)?> alumno(s)
        </button>
    </div>
</div>
</div>

<!-- ═══ MODAL: CONFIRMACIÓN DE ENVÍO ═══ -->
<div class="cl-overlay" id="ov-email-confirm">
<div class="cl-modal" style="max-width:400px;">
    <div class="cl-modal-hdr">
        <h5>⚠️ Confirmar envío</h5>
        <button class="cl-modal-close" onclick="clCerrar('ov-email-confirm')">✕</button>
    </div>
    <div class="cl-modal-body">
        <p style="font-size:.88rem;">
            Se enviará un correo electrónico a <strong><?=count($alumnos)?> alumno(s)</strong>
            del grupo <strong>"<?=htmlspecialchars($grupo->grup_nombre)?>"</strong>.
        </p>
        <p class="text-cl-muted" style="font-size:.8rem;">
            Esta acción no se puede deshacer. ¿Deseas continuar?
        </p>
    </div>
    <div class="cl-modal-ftr">
        <button class="btn btn-secondary" onclick="clCerrar('ov-email-confirm')">Cancelar</button>
        <button class="btn btn-success" onclick="ejecutarEnvioEmail()">
            <i class="ti-email me-1"></i>Sí, enviar ahora
        </button>
    </div>
</div>
</div>
<?php endif; ?>


<script>
// ── Chat ──────────────────────────────────────
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

// ── Email al grupo ────────────────────────────
function confirmarEnvioEmail() {
    var asunto = document.getElementById('emailAsunto').value.trim();
    var cuerpo = document.getElementById('emailCuerpo').value.trim();
    if (!asunto || !cuerpo) {
        alertar('Completa el asunto y el mensaje del correo.','alert alert-warning','ti-alert');
        return;
    }
    clCerrar('ov-email-grupo');
    clAbrir('ov-email-confirm');
}

function ejecutarEnvioEmail() {
    var asunto = document.getElementById('emailAsunto').value.trim();
    var cuerpo = document.getElementById('emailCuerpo').value.trim();
    clCerrar('ov-email-confirm');
    openCargar('Enviando correos, por favor espera…');

    $.ajax({
        url:  '<?=base_url('/asesor/enviar-email')?>',
        type: 'POST',
        data: {
            grup_ide: <?=$grupo->grup_ide?>,
            asunto:   asunto,
            cuerpo:   cuerpo
        },
        success: function(r) {
            try { r = JSON.parse(r); } catch(e){}
            closeCargar();

            if (r.ok) {
                alertar(r.msg,'alert alert-success','ti-check');
                // Limpiar campos
                document.getElementById('emailAsunto').value = '';
                document.getElementById('emailCuerpo').value = '';
                // Recargar para mostrar historial actualizado
                setTimeout(() => cargarFuncion(
                    '/asesor/chat/<?=$grupo->grup_ide?>',
                    'Asesoría',
                    '<?=addslashes(htmlspecialchars($grupo->grup_nombre))?>',
                    ''
                ), 1500);
            } else {
                // Mostrar detalle de errores si hay
                var detalle = r.msg;
                if (r.fallidos && r.fallidos.length > 0) {
                    detalle += '\n\nCorreos con error:\n• ' + r.fallidos.join('\n• ');
                }
                alertar(detalle,'alert alert-danger','ti-close');
                // Si hubo parciales, recargar igual
                if (r.enviados > 0) {
                    setTimeout(() => cargarFuncion(
                        '/asesor/chat/<?=$grupo->grup_ide?>',
                        'Asesoría',
                        '<?=addslashes(htmlspecialchars($grupo->grup_nombre))?>',
                        ''
                    ), 2500);
                }
            }
        },
        error: function() {
            closeCargar();
            alertar('Error de conexión al enviar los correos.','alert alert-danger','ti-close');
        }
    });
}
</script>
