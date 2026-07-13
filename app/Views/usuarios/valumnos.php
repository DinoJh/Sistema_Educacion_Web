<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div><h4 class="fw-bold mb-1">👥 Alumnos</h4>
    <small class="text-cl-muted"><?=count($usuarios)?> registrado(s)</small></div>
    <button class="btn btn-primary" onclick="abrirNuevoAlumno()">
        <i class="ti-plus me-1"></i>Nuevo Alumno
    </button>
</div>

<div class="card"><div class="card-body p-0">
<table class="table mb-0">
<thead><tr>
    <th>Alumno</th>
    <th>DNI / Usuario</th>
    <th>Email</th>
    <th>UGEL / Institución</th>
    <th style="text-align:center;">Cursos</th>
    <th>Estado</th>
    <th>Acciones</th>
</tr></thead>
<tbody>
<?php foreach($usuarios as $u): ?>
<tr>
    <td>
        <div class="d-flex align-items-center gap-2">
            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--cl-accent),#06b6d4);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;">
                <?=strtoupper(substr($u->usua_nombres,0,1))?>
            </div>
            <div>
                <div class="fw-medium" style="cursor:pointer;color:var(--cl-accent2);"
                     onclick="verPerfil(<?=$u->usua_ide?>,'<?=addslashes(htmlspecialchars($u->usua_paterno.' '.$u->usua_nombres))?>')">
                    <?=htmlspecialchars($u->usua_paterno.' '.$u->usua_materno.', '.$u->usua_nombres)?>
                </div>
            </div>
        </div>
    </td>
    <td>
        <div style="font-size:.78rem;">DNI: <strong><?=$u->usua_dni?:'—'?></strong></div>
        <code style="font-size:.72rem;color:var(--cl-accent2);"><?=$u->usua_user?></code>
    </td>
    <td><small><?=$u->usua_email?:'—'?></small></td>
    <td>
        <?php if(!empty($u->alui_sin_colegio)): ?>
            <span class="badge" style="background:rgba(245,158,11,.15);color:#f59e0b;font-size:.7rem;">Independiente</span>
        <?php elseif(!empty($u->ugel_nombre)): ?>
            <div style="font-size:.78rem;font-weight:600;"><?=htmlspecialchars($u->ugel_nombre)?></div>
            <?php $cole = $u->cole_nombre ?? $u->alui_cole_texto ?? null; ?>
            <?php if($cole): ?>
            <small class="text-cl-muted"><?=htmlspecialchars($cole)?></small>
            <?php endif; ?>
        <?php else: ?>
            <span class="text-cl-muted" style="font-size:.75rem;">—</span>
        <?php endif; ?>
    </td>
    <td style="text-align:center;">
        <span class="badge" style="background:rgba(124,58,237,.15);color:var(--cl-accent2);"><?=$u->total_cursos?></span>
    </td>
    <td><span class="badge bg-<?=$u->esta_clase?>"><?=$u->esta_nombre?></span></td>
    <td>
        <div class="d-flex flex-wrap gap-1">
            <?php if($u->usua_email): ?>
            <button class="btn btn-xs btn-outline-success" style="padding:2px 7px;font-size:.68rem;"
                    title="Enviar correo de acceso"
                    onclick="reenviarBienvenida(<?=$u->usua_ide?>,'<?=addslashes(htmlspecialchars($u->usua_email))?>','<?=addslashes(htmlspecialchars($u->usua_paterno.' '.$u->usua_nombres))?>')">
                <i class="ti-email"></i>
            </button>
            <?php endif; ?>
            <button class="btn btn-xs btn-outline-warning" style="padding:2px 7px;font-size:.68rem;"
                    onclick="cambiarEstado(<?=$u->usua_ide?>,<?=$u->esta_nombre=='ACTIVO'?2:1?>)">
                <?=$u->esta_nombre=='ACTIVO'?'Suspender':'Activar'?>
            </button>
            <button class="btn btn-xs btn-outline-danger" style="padding:2px 7px;font-size:.68rem;"
                    onclick="eliminarUsuario(<?=$u->usua_ide?>)">
                Eliminar
            </button>
        </div>
    </td>
</tr>
<?php endforeach; ?>
<?php if(empty($usuarios)): ?>
<tr><td colspan="7" class="text-center py-4 text-cl-muted">No hay alumnos registrados.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div></div>

<?php include __DIR__.'/../_modal_nuevo_usuario.php'; ?>
<?php include __DIR__.'/../_modal_ver_perfil.php'; ?>

<!-- Modal reenviar bienvenida -->
<div class="cl-overlay" id="ov-reenviar">
<div class="cl-modal" style="max-width:420px;">
    <div class="cl-modal-hdr">
        <h5><i class="ti-email me-2" style="color:#10b981;"></i>Reenviar correo de acceso</h5>
        <button class="cl-modal-close" onclick="clCerrar('ov-reenviar')">✕</button>
    </div>
    <div class="cl-modal-body">
        <p style="font-size:.85rem;">Se reenviará el correo de bienvenida con usuario y contraseña a:</p>
        <div class="p-2 rounded mb-3" style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);font-size:.85rem;">
            <strong id="reenvNombre"></strong><br>
            <code id="reenvEmail" style="color:#10b981;"></code>
        </div>
        <div class="mb-2">
            <label class="form-label small text-cl-muted">Contraseña a enviar en el correo</label>
            <input type="text" id="reenvPass" class="form-control" placeholder="Contraseña actual o nueva">
            <small class="text-cl-muted" style="font-size:.7rem;">
                Escribe la contraseña que el alumno usa actualmente, o una nueva si la quieres cambiar.
            </small>
        </div>
        <input type="hidden" id="reenvUid">
    </div>
    <div class="cl-modal-ftr">
        <button class="btn btn-secondary" onclick="clCerrar('ov-reenviar')">Cancelar</button>
        <button class="btn btn-success" onclick="confirmarReenvio()">
            <i class="ti-email me-1"></i>Enviar correo
        </button>
    </div>
</div>
</div>

<script>
function abrirNuevoAlumno() {
    document.getElementById('nuevoPerf').value = 1;
    // Limpiar campos
    ['nuevoN','nuevoPa','nuevoMa','nuevoD','nuevoE','nuevoCel','nuevoU','nuevoPas'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) el.value = '';
    });
    if (typeof actualizarModalPerfil === 'function') actualizarModalPerfil();
    clAbrir('ov-nuevousuario');
}

function guardarUsuario() {
    var perf  = document.getElementById('nuevoPerf').value;
    var dni   = document.getElementById('nuevoD').value.trim();
    var user  = perf == 1 ? dni : document.getElementById('nuevoU').value.trim();
    var pass  = document.getElementById('nuevoPas').value.trim();
    var envEmail = document.getElementById('chkEnviarEmail') ? (document.getElementById('chkEnviarEmail').checked ? 1 : 0) : 0;

    if (!document.getElementById('nuevoN').value.trim()) {
        alertar('El nombre es obligatorio.','alert alert-warning','ti-alert'); return;
    }
    if (perf == 1 && !dni) {
        alertar('El DNI es obligatorio para alumnos.','alert alert-warning','ti-alert'); return;
    }
    if (!user) {
        alertar('El usuario es obligatorio.','alert alert-warning','ti-alert'); return;
    }
    if (!pass) {
        alertar('La contraseña es obligatoria.','alert alert-warning','ti-alert'); return;
    }

    var data = {
        perf_ide:     perf,
        dni:          dni,
        nombres:      document.getElementById('nuevoN').value.trim(),
        paterno:      document.getElementById('nuevoPa').value.trim(),
        materno:      document.getElementById('nuevoMa').value.trim(),
        email:        document.getElementById('nuevoE').value.trim(),
        celular:      document.getElementById('nuevoCel').value.trim(),
        user:         user,
        pass:         pass,
        enviar_email: envEmail,
    };

    clCerrar('ov-nuevousuario');
    openCargar('Creando usuario…');
    $.post("<?=base_url('/usuarios/nuevo')?>", data, function(r){
        r = JSON.parse(r); closeCargar();
        if (r.ok) {
            alertar(r.msg,'alert alert-success','ti-check');
            setTimeout(()=>cargarFuncion('/usuarios/alumnos','Usuarios','Alumnos',''), 1500);
        } else {
            alertar(r.msg,'alert alert-danger','ti-close');
        }
    });
}

function cambiarEstado(ide, esta) {
    openCargar();
    $.post("<?=base_url('/usuarios/cambiarestado')?>", {ide:ide, esta:esta}, function(){
        closeCargar();
        alertar('Estado actualizado.','alert alert-success','ti-check');
        setTimeout(()=>cargarFuncion('/usuarios/alumnos','Usuarios','Alumnos',''), 900);
    });
}

function eliminarUsuario(ide) {
    if (!confirm('¿Eliminar este alumno?')) return;
    openCargar();
    $.post("<?=base_url('/usuarios/eliminar')?>", {ide:ide}, function(r){
        r = JSON.parse(r); closeCargar();
        if (r.ok) {
            alertar(r.msg,'alert alert-success','ti-check');
            setTimeout(()=>cargarFuncion('/usuarios/alumnos','Usuarios','Alumnos',''), 1000);
        }
    });
}

function reenviarBienvenida(uid, email, nombre) {
    document.getElementById('reenvUid').value    = uid;
    document.getElementById('reenvEmail').textContent = email;
    document.getElementById('reenvNombre').textContent = nombre;
    document.getElementById('reenvPass').value   = '';
    clAbrir('ov-reenviar');
}

function confirmarReenvio() {
    var uid  = document.getElementById('reenvUid').value;
    var pass = document.getElementById('reenvPass').value.trim();
    if (!pass) { alertar('Escribe la contraseña a enviar.','alert alert-warning','ti-alert'); return; }
    clCerrar('ov-reenviar');
    openCargar('Enviando correo…');
    $.post("<?=base_url('/usuarios/reenviar-bienvenida')?>", {uid:uid, pass:pass}, function(r){
        r = JSON.parse(r); closeCargar();
        alertar(r.msg, r.ok?'alert alert-success':'alert alert-danger', r.ok?'ti-check':'ti-close');
    });
}
</script>
