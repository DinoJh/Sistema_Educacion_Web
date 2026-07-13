<?php
// Datos de institución educativa (de alumno_info)
$sinColegio = (int)($aluInfo->alui_sin_colegio ?? 0);
$ugelNombre = $aluInfo->ugel_nombre      ?? null;
$ugelCiudad = $aluInfo->ugel_ciudad      ?? null;
$coleNombre = $aluInfo->cole_nombre      ?? $aluInfo->alui_cole_texto ?? null;
$tieneUgel  = ($ugelNombre !== null);
?>

<!-- Cabecera -->
<div class="mb-4 d-flex align-items-center gap-3">
    <div style="width:60px;height:60px;border-radius:50%;
                background:linear-gradient(135deg,var(--cl-accent),#06b6d4);
                display:flex;align-items:center;justify-content:center;
                font-size:1.6rem;font-weight:700;color:#fff;flex-shrink:0;">
        <?=strtoupper(substr($usuario->usua_nombres??'?',0,1))?>
    </div>
    <div>
        <h4 class="fw-bold mb-0">
            <?=htmlspecialchars($usuario->usua_nombres.' '.$usuario->usua_paterno.' '.$usuario->usua_materno)?>
        </h4>
        <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
            <small class="text-cl-muted">
                <i class="ti-user me-1"></i>Alumno &nbsp;·&nbsp;
                Usuario: <code style="color:var(--cl-accent2);"><?=$usuario->usua_user?></code>
            </small>
        </div>
    </div>
</div>

<!-- Banner institución educativa -->
<div class="card mb-4" style="border-left:4px solid <?=$sinColegio?'#f59e0b':($tieneUgel?'var(--cl-accent2)':'rgba(255,255,255,.1)')?>;cursor:default;">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span style="font-size:1.3rem;"><?=$sinColegio?'🎓':'🏫'?></span>
            <div style="flex:1;">
                <?php if($sinColegio): ?>
                    <div class="fw-semibold" style="color:#f59e0b;">Alumno independiente</div>
                    <small class="text-cl-muted">No asociado a ninguna institución educativa</small>
                <?php elseif($tieneUgel): ?>
                    <div class="fw-semibold"><?=htmlspecialchars($coleNombre ?? 'Colegio no especificado')?></div>
                    <small class="text-cl-muted">
                        <i class="ti-location-pin me-1"></i><?=htmlspecialchars($ugelNombre)?><?=$ugelCiudad?' — '.htmlspecialchars($ugelCiudad):''?>
                    </small>
                <?php else: ?>
                    <div class="text-cl-muted" style="font-size:.85rem;">Sin institución educativa registrada</div>
                    <small class="text-cl-muted" style="font-size:.72rem;">Usa el botón de abajo si necesitas corregir tus datos</small>
                <?php endif; ?>
            </div>
            <!-- Botón Contactar Admin al costado del banner -->
            <button class="btn btn-sm" style="background:rgba(245,158,11,.12);color:#f59e0b;border:1px solid rgba(245,158,11,.3);white-space:nowrap;"
                    onclick="clAbrir('ov-contacto-alumno')">
                <i class="ti-comment-alt me-1"></i>Contactar administrador
            </button>
        </div>
    </div>
</div>

<div class="row g-4">
<!-- Datos personales -->
<div class="col-lg-6">
<div class="card mb-4"><div class="card-body">
    <h6 class="fw-bold mb-3">👤 Datos personales</h6>
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label small text-cl-muted">Nombres *</label>
            <input id="pNombres" class="form-control" value="<?=htmlspecialchars($usuario->usua_nombres?:'')?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-cl-muted">Ap. Paterno</label>
            <input id="pPaterno" class="form-control" value="<?=htmlspecialchars($usuario->usua_paterno?:'')?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-cl-muted">Ap. Materno</label>
            <input id="pMaterno" class="form-control" value="<?=htmlspecialchars($usuario->usua_materno?:'')?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-cl-muted">DNI</label>
            <input id="pDni" class="form-control" value="<?=htmlspecialchars($usuario->usua_dni?:'')?>" maxlength="8" placeholder="12345678">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-cl-muted">Celular</label>
            <input id="pCelular" class="form-control" value="<?=htmlspecialchars($usuario->usua_celular?:'')?>">
        </div>
        <div class="col-12">
            <label class="form-label small text-cl-muted">Email</label>
            <input type="email" id="pEmail" class="form-control" value="<?=htmlspecialchars($usuario->usua_email?:'')?>">
        </div>
    </div>
    <div class="text-end mt-3">
        <button class="btn btn-primary" onclick="guardarPerfil()">
            <i class="ti-save me-1"></i>Guardar cambios
        </button>
    </div>
</div></div>
</div>

<!-- Cambiar contraseña + info institución -->
<div class="col-lg-6">

<!-- Info institución (solo lectura con valores exactos) -->
<div class="card mb-4"><div class="card-body">
    <h6 class="fw-bold mb-2">🏫 Institución educativa</h6>
    <p class="text-cl-muted" style="font-size:.76rem;margin-bottom:10px;">
        Si hay algún error en tu colegio o UGEL, usa el botón <strong>"Contactar administrador"</strong> para solicitar la corrección.
    </p>
    <?php if($sinColegio): ?>
    <div class="p-2 rounded" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);font-size:.83rem;">
        <strong style="color:#f59e0b;">Alumno independiente</strong> — sin colegio asociado
    </div>
    <?php elseif($tieneUgel): ?>
    <div class="row g-2" style="font-size:.83rem;">
        <div class="col-12">
            <label class="form-label small text-cl-muted mb-1">UGEL</label>
            <div class="p-2 rounded" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);color:var(--cl-accent2);font-weight:600;">
                <?=htmlspecialchars($ugelNombre)?><?=$ugelCiudad?' — '.htmlspecialchars($ugelCiudad):''?>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label small text-cl-muted mb-1">Institución</label>
            <div class="p-2 rounded" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);">
                <?=htmlspecialchars($coleNombre ?? 'No especificado')?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="p-2 rounded text-cl-muted" style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);font-size:.83rem;">
        No registraste una institución educativa al crear tu cuenta.
    </div>
    <?php endif; ?>
</div></div>

<!-- Cambiar contraseña -->
<div class="card"><div class="card-body">
    <h6 class="fw-bold mb-3">🔒 Cambiar contraseña</h6>
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label small text-cl-muted">Contraseña actual</label>
            <input type="password" id="passActual" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-cl-muted">Nueva contraseña</label>
            <input type="password" id="passNueva" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-cl-muted">Confirmar</label>
            <input type="password" id="passRepite" class="form-control">
        </div>
    </div>
    <div class="text-end mt-3">
        <button class="btn btn-outline-warning" onclick="cambiarPass()">
            <i class="ti-key me-1"></i>Cambiar contraseña
        </button>
    </div>
</div></div>

</div><!-- /col-lg-6 -->
</div><!-- /row -->


<!-- ═══ OVERLAY: Contactar administrador (desde Mi Perfil) ═══ -->
<div class="cl-overlay" id="ov-contacto-alumno">
<div class="cl-modal">
    <div class="cl-modal-hdr">
        <h5><i class="ti-comment-alt me-2" style="color:#f59e0b;"></i>Contactar al Administrador</h5>
        <button class="cl-modal-close" onclick="clCerrar('ov-contacto-alumno')">✕</button>
    </div>
    <div class="cl-modal-body">
        <p class="text-cl-muted" style="font-size:.83rem;margin-bottom:14px;">
            Puedes escribirnos si necesitas corregir tu colegio, UGEL u otro dato de tu cuenta.
        </p>

        <!-- Tipo de mensaje -->
        <div class="mb-3">
            <label class="form-label small text-cl-muted">Tipo</label>
            <div class="d-flex gap-2 flex-wrap" id="tipoBtns">
                <button type="button" class="btn btn-sm tipo-c-btn activo" data-tipo="CONSULTA"   onclick="selTipoC(this)">💬 Consulta</button>
                <button type="button" class="btn btn-sm tipo-c-btn"        data-tipo="RECLAMO"    onclick="selTipoC(this)">🔔 Reclamo</button>
                <button type="button" class="btn btn-sm tipo-c-btn"        data-tipo="SUGERENCIA" onclick="selTipoC(this)">💡 Sugerencia</button>
                <button type="button" class="btn btn-sm tipo-c-btn"        data-tipo="QUEJA"      onclick="selTipoC(this)">⚠️ Queja</button>
            </div>
            <input type="hidden" id="caTipo" value="CONSULTA">
        </div>

        <div class="mb-3">
            <label class="form-label small text-cl-muted">Asunto *</label>
            <input type="text" id="caAsunto" class="form-control"
                   placeholder="Ej: Solicito corrección de mi colegio" maxlength="200">
        </div>
        <div class="mb-1">
            <label class="form-label small text-cl-muted">Mensaje *</label>
            <textarea id="caMensaje" class="form-control" rows="4"
                      placeholder="Explica tu caso con detalle..."></textarea>
        </div>
    </div>
    <div class="cl-modal-ftr">
        <button class="btn btn-secondary" onclick="clCerrar('ov-contacto-alumno')">Cancelar</button>
        <button class="btn btn-primary" onclick="enviarContactoAlumno()">
            <i class="ti-arrow-right me-1"></i>Enviar mensaje
        </button>
    </div>
</div>
</div>

<script>
/* ── Perfil ── */
function guardarPerfil() {
    openCargar('Guardando perfil…');
    $.post("<?=base_url('/mi-perfil/guardar')?>", {
        nombres: document.getElementById('pNombres').value,
        paterno: document.getElementById('pPaterno').value,
        materno: document.getElementById('pMaterno').value,
        dni:     document.getElementById('pDni').value,
        celular: document.getElementById('pCelular').value,
        email:   document.getElementById('pEmail').value,
    }, function(r) {
        r = JSON.parse(r); closeCargar();
        alertar(r.msg, r.ok ? 'alert alert-success' : 'alert alert-danger', r.ok ? 'ti-check' : 'ti-close');
    });
}

function cambiarPass() {
    openCargar('Cambiando contraseña…');
    $.post("<?=base_url('/mi-perfil/password')?>", {
        actual: document.getElementById('passActual').value,
        nueva:  document.getElementById('passNueva').value,
        repite: document.getElementById('passRepite').value,
    }, function(r) {
        r = JSON.parse(r); closeCargar();
        alertar(r.msg, r.ok ? 'alert alert-success' : 'alert alert-danger', r.ok ? 'ti-check' : 'ti-close');
        if (r.ok) {
            document.getElementById('passActual').value = '';
            document.getElementById('passNueva').value  = '';
            document.getElementById('passRepite').value = '';
        }
    });
}

/* ── Contacto admin ── */
function selTipoC(btn) {
    document.querySelectorAll('.tipo-c-btn').forEach(b => {
        b.classList.remove('activo','btn-warning','btn-outline-secondary');
        b.style.background = '';
        b.style.color = '';
    });
    btn.classList.add('activo');
    btn.style.background = 'rgba(245,158,11,.2)';
    btn.style.color = '#f59e0b';
    document.getElementById('caTipo').value = btn.dataset.tipo;
}
// Estilo inicial
document.querySelectorAll('.tipo-c-btn').forEach(b => {
    b.style.background = 'rgba(255,255,255,.05)';
    b.style.color = 'var(--cl-muted)';
    b.style.border = '1px solid rgba(255,255,255,.1)';
});
document.querySelector('.tipo-c-btn.activo').style.background = 'rgba(245,158,11,.2)';
document.querySelector('.tipo-c-btn.activo').style.color = '#f59e0b';

function enviarContactoAlumno() {
    var asunto  = document.getElementById('caAsunto').value.trim();
    var mensaje = document.getElementById('caMensaje').value.trim();
    var tipo    = document.getElementById('caTipo').value;
    if (!asunto || !mensaje) {
        alertar('Completa el asunto y el mensaje.','alert alert-warning','ti-alert'); return;
    }
    clCerrar('ov-contacto-alumno');
    openCargar('Enviando tu mensaje…');
    $.post("<?=base_url('/contacto/enviar')?>", {
        tipo: tipo, asunto: asunto, mensaje: mensaje
    }, function(r) {
        r = JSON.parse(r); closeCargar();
        alertar(r.msg, r.ok ? 'alert alert-success' : 'alert alert-danger', r.ok ? 'ti-check' : 'ti-close');
        if (r.ok) {
            document.getElementById('caAsunto').value  = '';
            document.getElementById('caMensaje').value = '';
        }
    });
}
</script>
