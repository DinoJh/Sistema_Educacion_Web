<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div><h4 class="fw-bold mb-1">👥 Alumnos</h4>
    <small class="text-cl-muted"><?=count($usuarios)?> registrado(s)</small></div>
    <button class="btn btn-primary" onclick="abrirModalNuevo(1)"><i class="ti-plus me-1"></i>Nuevo Alumno</button>
</div>
<div class="card"><div class="card-body p-0">
<table class="table mb-0">
<thead><tr>
    <th>Alumno</th><th>DNI</th><th>Usuario</th><th>Email</th><th>Cursos</th><th>Estado</th><th>Acciones</th>
</tr></thead>
<tbody>
<?php foreach($usuarios as $u): ?>
<tr>
    <td><div class="d-flex align-items-center gap-2">
        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--cl-accent),#06b6d4);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;"><?=strtoupper(substr($u->usua_nombres,0,1))?></div>
        <div><div class="fw-medium"><?=htmlspecialchars($u->usua_paterno.' '.$u->usua_materno.', '.$u->usua_nombres)?></div></div>
    </div></td>
    <td><small class="text-cl-muted"><?=$u->usua_dni?></small></td>
    <td><code style="font-size:.78rem;color:var(--cl-accent2);"><?=$u->usua_user?></code></td>
    <td><small><?=$u->usua_email?></small></td>
    <td><span class="badge" style="background:rgba(124,58,237,.15);color:var(--cl-accent2);"><?=$u->total_cursos?> cursos</span></td>
    <td><span class="badge bg-<?=$u->esta_clase?>"><?=$u->esta_nombre?></span></td>
    <td>
        <button class="btn btn-xs btn-outline-warning me-1" style="padding:2px 8px;font-size:.7rem;" onclick="cambiarEstado(<?=$u->usua_ide?>,<?=$u->esta_nombre=='ACTIVO'?2:1?>)"><?=$u->esta_nombre=='ACTIVO'?'Suspender':'Activar'?></button>
        <button class="btn btn-xs btn-outline-danger" style="padding:2px 8px;font-size:.7rem;" onclick="if(confirm('Eliminar usuario?'))$.post('<?=base_url('/usuarios/eliminar')?>',{ide:<?=$u->usua_ide?>},function(r){r=JSON.parse(r);if(r.ok){alertar(r.msg,'alert alert-success');setTimeout(()=>cargarFuncion('/usuarios/alumnos','Usuarios','Alumnos',''),1000);}})">Eliminar</button>
    </td>
</tr>
<?php endforeach; ?>
<?php if(empty($usuarios)): ?><tr><td colspan="7" class="text-center py-4 text-cl-muted">No hay alumnos registrados.</td></tr><?php endif; ?>
</tbody>
</table>
</div></div>
<?php include __DIR__.'/../_modal_nuevo_usuario.php'; ?>
<script>
function cambiarEstado(ide,esta){
    $.post("<?=base_url('/usuarios/cambiarestado')?>",{ide:ide,esta:esta},function(){
        alertar('Estado actualizado.','alert alert-success');setTimeout(()=>cargarFuncion('/usuarios/alumnos','Usuarios','Alumnos',''),900);
    });
}
function abrirModalNuevo(perf){
    document.getElementById('nuevoPerf').value=perf;
    new bootstrap.Modal(document.getElementById('modalNuevoUsuario')).show();
}
function guardarUsuario(){
    var data={perf_ide:document.getElementById('nuevoPerf').value,dni:document.getElementById('nuevoD').value,nombres:document.getElementById('nuevoN').value,paterno:document.getElementById('nuevoPa').value,materno:document.getElementById('nuevoMa').value,email:document.getElementById('nuevoE').value,celular:document.getElementById('nuevoCel').value,user:document.getElementById('nuevoU').value,pass:document.getElementById('nuevoPas').value};
    if(!data.nombres||!data.user||!data.pass){alertar('Completa los campos requeridos.','alert alert-warning');return;}
    openCargar();bootstrap.Modal.getInstance(document.getElementById('modalNuevoUsuario')).hide();
    $.post("<?=base_url('/usuarios/nuevo')?>",data,function(r){r=JSON.parse(r);closeCargar();if(r.ok){alertar(r.msg,'alert alert-success');setTimeout(()=>cargarFuncion('/usuarios/alumnos','Usuarios','Alumnos',''),1000);}else alertar(r.msg,'alert alert-danger');});
}
</script>
