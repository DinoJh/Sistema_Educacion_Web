<div class="d-flex align-items-center justify-content-between mb-4">
    <div><h4 class="fw-bold mb-1">🏷️ Categorías</h4><small class="text-cl-muted"><?=count($categorias)?> categoría(s)</small></div>
    <button class="btn btn-primary" onclick="abrirModal()"><i class="ti-plus me-1"></i>Nueva Categoría</button>
</div>
<div class="card"><div class="card-body p-0">
<table class="table mb-0">
<thead><tr><th>Nombre</th><th>Icono</th><th>Cursos</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>
<?php foreach($categorias as $c): ?>
<tr>
    <td class="fw-medium"><?=$c->cate_nombre?></td>
    <td><i class="<?=$c->cate_icono?> me-1"></i><small class="text-cl-muted"><?=$c->cate_icono?></small></td>
    <td><span class="badge" style="background:rgba(124,58,237,.15);color:var(--cl-accent2);"><?=$c->total_cursos?></span></td>
    <td><span class="badge bg-<?=$c->esta_clase?>"><?=$c->esta_nombre?></span></td>
    <td>
        <button class="btn btn-xs btn-outline-primary me-1" style="padding:2px 8px;font-size:.7rem;" onclick="editarCat(<?=$c->cate_ide?>,'<?=addslashes($c->cate_nombre)?>','<?=$c->cate_icono?>')">Editar</button>
        <button class="btn btn-xs btn-outline-danger" style="padding:2px 8px;font-size:.7rem;" onclick="if(confirm('Eliminar?'))$.post('<?=base_url('/categorias/eliminar')?>',{ide:<?=$c->cate_ide?>},function(){alertar('Eliminado.','alert alert-success');setTimeout(()=>cargarFuncion('/categorias','Categorías','Categorías',''),900);})">Eliminar</button>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div>

<div class="modal fade" id="modalCat" tabindex="-1">
<div class="modal-dialog"><div class="modal-content" style="background:var(--cl-bg-card);">
  <div class="modal-header border-0"><h5 class="modal-title fw-bold" id="catModalTitle">Nueva Categoría</h5>
  <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" id="catIde" value="">
    <div class="mb-3"><label class="form-label small text-cl-muted">Nombre *</label><input id="catNombre" class="form-control" placeholder="ej. Programación"></div>
    <div class="mb-3"><label class="form-label small text-cl-muted">Icono (Themify)</label><input id="catIcono" class="form-control" placeholder="ti-code"><small class="text-cl-muted">Ver iconos en themify.me/themify-icons</small></div>
  </div>
  <div class="modal-footer border-0">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary" onclick="guardarCat()">Guardar</button>
  </div>
</div></div></div>

<script>
function abrirModal(){document.getElementById('catIde').value='';document.getElementById('catNombre').value='';document.getElementById('catIcono').value='';document.getElementById('catModalTitle').innerHTML='Nueva Categoría';new bootstrap.Modal(document.getElementById('modalCat')).show();}
function editarCat(ide,nombre,icono){document.getElementById('catIde').value=ide;document.getElementById('catNombre').value=nombre;document.getElementById('catIcono').value=icono;document.getElementById('catModalTitle').innerHTML='Editar Categoría';new bootstrap.Modal(document.getElementById('modalCat')).show();}
function guardarCat(){
    var n=document.getElementById('catNombre').value.trim();
    if(!n){alertar('Escribe el nombre.','alert alert-warning');return;}
    openCargar();bootstrap.Modal.getInstance(document.getElementById('modalCat')).hide();
    $.post("<?=base_url('/categorias/guardar')?>",{ide:document.getElementById('catIde').value,nombre:n,icono:document.getElementById('catIcono').value},function(){closeCargar();alertar('Guardado.','alert alert-success');setTimeout(()=>cargarFuncion('/categorias','Categorías','Categorías',''),900);});
}
</script>
