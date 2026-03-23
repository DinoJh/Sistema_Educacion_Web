<?php
$tipo        = $extra->pale_tipo        ?? 'OTRO';
$institucion = $extra->pale_institucion ?? '';
$carrera     = $extra->pale_carrera     ?? '';
$desc        = $extra->pale_descripcion ?? '';
?>
<div class="mb-4 d-flex align-items-center gap-3">
    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--cl-accent),#06b6d4);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:#fff;flex-shrink:0;">
        <?=strtoupper(substr($usuario->usua_nombres??'?',0,1))?>
    </div>
    <div>
        <h4 class="fw-bold mb-0"><?=htmlspecialchars($usuario->usua_nombres.' '.$usuario->usua_paterno.' '.$usuario->usua_materno)?></h4>
        <small class="text-cl-muted"><i class="ti-user me-1"></i>Alumno &nbsp;·&nbsp; Usuario: <code style="color:var(--cl-accent2);"><?=$usuario->usua_user?></code></small>
    </div>
</div>

<div class="row g-4">
<!-- Col izquierda: datos básicos -->
<div class="col-lg-6">
<div class="card mb-4"><div class="card-body">
    <h6 class="fw-bold mb-3">👤 Datos personales</h6>
    <div class="row g-3">
        <div class="col-12"><label class="form-label small text-cl-muted">Nombres *</label>
            <input id="pNombres" class="form-control" value="<?=htmlspecialchars($usuario->usua_nombres??'')?>"></div>
        <div class="col-md-6"><label class="form-label small text-cl-muted">Ap. Paterno</label>
            <input id="pPaterno" class="form-control" value="<?=htmlspecialchars($usuario->usua_paterno??'')?>"></div>
        <div class="col-md-6"><label class="form-label small text-cl-muted">Ap. Materno</label>
            <input id="pMaterno" class="form-control" value="<?=htmlspecialchars($usuario->usua_materno??'')?>"></div>
        <div class="col-md-6"><label class="form-label small text-cl-muted">Celular</label>
            <input id="pCelular" class="form-control" value="<?=htmlspecialchars($usuario->usua_celular??'')?>"></div>
        <div class="col-md-6"><label class="form-label small text-cl-muted">Email</label>
            <input type="email" id="pEmail" class="form-control" value="<?=htmlspecialchars($usuario->usua_email??'')?>"></div>
    </div>
    <div class="text-end mt-3">
        <button class="btn btn-primary" onclick="guardarPerfil()"><i class="ti-save me-1"></i>Guardar cambios</button>
    </div>
</div></div>

<div class="card"><div class="card-body">
    <h6 class="fw-bold mb-3">🔒 Cambiar contraseña</h6>
    <div class="row g-3">
        <div class="col-12"><label class="form-label small text-cl-muted">Contraseña actual</label>
            <input type="password" id="passActual" class="form-control"></div>
        <div class="col-md-6"><label class="form-label small text-cl-muted">Nueva contraseña</label>
            <input type="password" id="passNueva" class="form-control"></div>
        <div class="col-md-6"><label class="form-label small text-cl-muted">Confirmar</label>
            <input type="password" id="passRepite" class="form-control"></div>
    </div>
    <div class="text-end mt-3">
        <button class="btn btn-outline-warning" onclick="cambiarPass()"><i class="ti-key me-1"></i>Cambiar contraseña</button>
    </div>
</div></div>
</div>

<!-- Col derecha: perfil académico -->
<div class="col-lg-6">
<div class="card"><div class="card-body">
    <h6 class="fw-bold mb-3">🎓 Perfil académico</h6>
    <div class="row g-3">
        <div class="col-12"><label class="form-label small text-cl-muted">Soy…</label>
            <select id="pTipo" class="form-select" onchange="toggleCampos()">
                <option value="ESTUDIANTE_COLEGIO" <?=$tipo=='ESTUDIANTE_COLEGIO'?'selected':''?>>Estudiante de colegio / secundaria</option>
                <option value="ESTUDIANTE_SUPERIOR" <?=$tipo=='ESTUDIANTE_SUPERIOR'?'selected':''?>>Estudiante universitario / instituto</option>
                <option value="PROFESIONAL" <?=$tipo=='PROFESIONAL'?'selected':''?>>Profesional / trabajador</option>
                <option value="OTRO" <?=$tipo=='OTRO'?'selected':''?>>Otro</option>
            </select>
        </div>
        <div class="col-12" id="campoInstitucion">
            <label class="form-label small text-cl-muted" id="labelInstitucion">Institución educativa</label>
            <input id="pInstitucion" class="form-control" placeholder="ej. Universidad Nacional del Altiplano" value="<?=htmlspecialchars($institucion)?>">
        </div>
        <div class="col-12" id="campoCarrera">
            <label class="form-label small text-cl-muted">Carrera / Especialidad</label>
            <input id="pCarrera" class="form-control" placeholder="ej. Ingeniería de Sistemas" value="<?=htmlspecialchars($carrera)?>">
        </div>
        <div class="col-12"><label class="form-label small text-cl-muted">¿Qué te motivó a unirte a CodePuno?</label>
            <textarea id="pDesc" class="form-control" rows="3" placeholder="Cuéntanos un poco sobre ti…"><?=htmlspecialchars($desc)?></textarea>
        </div>
    </div>
    <div class="text-end mt-3">
        <button class="btn btn-primary" onclick="guardarPerfil()"><i class="ti-save me-1"></i>Guardar cambios</button>
    </div>
</div></div>
</div>
</div>

<script>
function toggleCampos() {
    var t = document.getElementById('pTipo').value;
    var ci = document.getElementById('campoInstitucion');
    var cc = document.getElementById('campoCarrera');
    var li = document.getElementById('labelInstitucion');
    if (t === 'OTRO') {
        ci.style.display = 'none'; cc.style.display = 'none';
    } else {
        ci.style.display = ''; cc.style.display = '';
        if (t === 'ESTUDIANTE_COLEGIO') {
            li.textContent = 'Nombre del colegio / institución';
            document.getElementById('pCarrera').closest('div').querySelector('label').textContent = 'Año / Grado';
        } else if (t === 'ESTUDIANTE_SUPERIOR') {
            li.textContent = 'Universidad / Instituto';
            document.getElementById('pCarrera').closest('div').querySelector('label').textContent = 'Carrera';
        } else {
            li.textContent = 'Empresa / Organización';
            document.getElementById('pCarrera').closest('div').querySelector('label').textContent = 'Cargo / Rol';
        }
    }
}
toggleCampos();

function guardarPerfil() {
    openCargar('Guardando perfil…');
    $.post("<?=base_url('/mi-perfil/guardar')?>", {
        nombres:     document.getElementById('pNombres').value,
        paterno:     document.getElementById('pPaterno').value,
        materno:     document.getElementById('pMaterno').value,
        celular:     document.getElementById('pCelular').value,
        email:       document.getElementById('pEmail').value,
        tipo:        document.getElementById('pTipo').value,
        institucion: document.getElementById('pInstitucion') ? document.getElementById('pInstitucion').value : '',
        carrera:     document.getElementById('pCarrera') ? document.getElementById('pCarrera').value : '',
        descripcion: document.getElementById('pDesc').value,
    }, function(r) {
        r = JSON.parse(r); closeCargar();
        alertar(r.msg, r.ok ? 'alert alert-success' : 'alert alert-danger', r.ok ? 'ti-check' : 'ti-close');
    });
}
function cambiarPass() {
    openCargar('Cambiando contraseña…');
    $.post("<?=base_url('/mi-perfil/password')?>", {
        actual:  document.getElementById('passActual').value,
        nueva:   document.getElementById('passNueva').value,
        repite:  document.getElementById('passRepite').value,
    }, function(r) {
        r = JSON.parse(r); closeCargar();
        alertar(r.msg, r.ok ? 'alert alert-success' : 'alert alert-danger', r.ok ? 'ti-check' : 'ti-close');
        if (r.ok) { document.getElementById('passActual').value=''; document.getElementById('passNueva').value=''; document.getElementById('passRepite').value=''; }
    });
}
</script>
