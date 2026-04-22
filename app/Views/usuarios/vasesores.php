<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">🎧 Asesores</h4>
        <small class="text-cl-muted"><?=count($usuarios)?> asesor(es) registrado(s)</small>
    </div>
    <button class="btn btn-primary"
            onclick="clAbrir('ov-nuevousuario');document.getElementById('nuevoPerf').value=4">
        <i class="ti-plus me-1"></i>Nuevo Asesor
    </button>
</div>

<?php if(empty($usuarios)): ?>
<div class="card text-center py-5">
    <div class="card-body">
        <div style="font-size:2.5rem;margin-bottom:.75rem;">🎧</div>
        <h5 class="fw-bold mb-1">No hay asesores registrados</h5>
        <p class="text-cl-muted mb-3">Crea el primer asesor con el botón de arriba.</p>
    </div>
</div>
<?php else: ?>

<div class="card"><div class="card-body p-0">
<table class="table mb-0">
<thead><tr>
    <th>Asesor</th>
    <th>DNI</th>
    <th>Usuario</th>
    <th>Email / Celular</th>
    <th style="text-align:center;">Grupos</th>
    <th style="text-align:center;">Alumnos Asesorados</th>
    <th>Estado</th>
    <th>Acciones</th>
</tr></thead>
<tbody>
<?php foreach($usuarios as $u): ?>
<tr>
    <td>
        <div class="d-flex align-items-center gap-2">
            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                        background:linear-gradient(135deg,#ef4444,#f97316);
                        display:flex;align-items:center;justify-content:center;
                        font-size:.75rem;font-weight:700;">
                <?=strtoupper(substr($u->usua_nombres,0,1))?>
            </div>
            <div>
                <div class="fw-medium">
                    <?=htmlspecialchars($u->usua_paterno.' '.$u->usua_materno.', '.$u->usua_nombres)?>
                </div>
                <small class="text-cl-muted" style="font-size:.7rem;">Asesor</small>
            </div>
        </div>
    </td>
    <td><small class="text-cl-muted"><?=$u->usua_dni?></small></td>
    <td><code style="font-size:.78rem;color:var(--cl-accent2);"><?=$u->usua_user?></code></td>
    <td>
        <small class="d-block"><?=$u->usua_email?></small>
        <small class="text-cl-muted"><?=$u->usua_celular?></small>
    </td>
    <td style="text-align:center;">
        <span class="badge" style="background:rgba(239,68,68,.15);color:#ef4444;">
            <?=$u->total_grupos?>
        </span>
    </td>
    <td style="text-align:center;">
        <span class="badge" style="background:rgba(124,58,237,.15);color:var(--cl-accent2);">
            <?=$u->total_alumnos_asesorados?>
        </span>
    </td>
    <td>
        <span class="badge bg-<?=$u->esta_clase?>"><?=$u->esta_nombre?></span>
    </td>
    <td>
        <button class="btn btn-xs btn-outline-warning me-1"
                style="padding:2px 8px;font-size:.7rem;"
                onclick="cambiarEstadoAsesor(<?=$u->usua_ide?>,<?=$u->esta_nombre=='ACTIVO'?2:1?>)">
            <?=$u->esta_nombre=='ACTIVO'?'Suspender':'Activar'?>
        </button>
        <button class="btn btn-xs btn-outline-danger"
                style="padding:2px 8px;font-size:.7rem;"
                onclick="eliminarAsesor(<?=$u->usua_ide?>)">
            Eliminar
        </button>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div>
<?php endif; ?>

<?php include __DIR__.'/../_modal_nuevo_usuario.php'; ?>

<script>
function cambiarEstadoAsesor(ide, esta) {
    openCargar();
    $.post('<?=base_url('/usuarios/cambiarestado')?>', {ide:ide, esta:esta}, function(){
        closeCargar();
        alertar('Estado actualizado.','alert alert-success','ti-check');
        setTimeout(()=>cargarFuncion('/usuarios/asesores','Usuarios','Asesores',''), 900);
    });
}
function eliminarAsesor(ide) {
    if (!confirm('¿Eliminar este asesor? Sus grupos y mensajes se conservarán.')) return;
    openCargar();
    $.post('<?=base_url('/usuarios/eliminar')?>', {ide:ide}, function(r){
        r=JSON.parse(r); closeCargar();
        if(r.ok){
            alertar(r.msg,'alert alert-success','ti-check');
            setTimeout(()=>cargarFuncion('/usuarios/asesores','Usuarios','Asesores',''),1000);
        }
    });
}
function guardarUsuario() {
    var data = {
        perf_ide: document.getElementById('nuevoPerf').value,
        dni:      document.getElementById('nuevoD').value,
        nombres:  document.getElementById('nuevoN').value,
        paterno:  document.getElementById('nuevoPa').value,
        materno:  document.getElementById('nuevoMa').value,
        email:    document.getElementById('nuevoE').value,
        celular:  document.getElementById('nuevoCel').value,
        user:     document.getElementById('nuevoU').value,
        pass:     document.getElementById('nuevoPas').value
    };
    if (!data.nombres || !data.user || !data.pass) {
        alertar('Completa los campos requeridos.','alert alert-warning','ti-alert');
        return;
    }
    clCerrar('ov-nuevousuario');
    openCargar();
    $.post('<?=base_url('/usuarios/nuevo')?>', data, function(r){
        r=JSON.parse(r); closeCargar();
        if(r.ok){
            alertar(r.msg,'alert alert-success','ti-check');
            setTimeout(()=>cargarFuncion('/usuarios/asesores','Usuarios','Asesores',''),1200);
        } else {
            alertar(r.msg,'alert alert-danger','ti-close');
        }
    });
}
</script>
