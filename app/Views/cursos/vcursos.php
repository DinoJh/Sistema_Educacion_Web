<?php $esPro=$session->perf_ide==2; $esAdmin=$session->perf_ide==3; ?>
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div><h4 class="mb-1 fw-bold">📚 <?=$esPro?'Mis Cursos':'Gestión de Cursos'?></h4>
    <small class="text-cl-muted"><?=count($cursos)?> curso(s)</small></div>
    <?php if($esPro): ?>
    <button class="btn btn-primary px-4" onclick="abrirModalCurso()"><i class="ti-plus me-1"></i>Nuevo Curso</button>
    <?php endif; ?>
</div>

<!-- Filtros -->
<div class="d-flex gap-2 flex-wrap mb-4">
    <button class="btn btn-sm btn-outline-secondary active filtro-cat" onclick="filtrarCat(this,'all')">Todos</button>
    <button class="btn btn-sm btn-outline-secondary filtro-cat" onclick="filtrarCat(this,'active')">Activos</button>
    <button class="btn btn-sm btn-outline-danger filtro-cat" onclick="filtrarCat(this,'deleted')">Eliminados</button>
    <?php foreach($categorias as $c): ?>
    <button class="btn btn-sm btn-outline-secondary filtro-cat" onclick="filtrarCat(this,'cate-<?=$c->cate_ide?>')"><?=$c->cate_nombre?></button>
    <?php endforeach; ?>
</div>

<div class="row g-3" id="gridCursos">
<?php if(empty($cursos)): ?>
<div class="col-12 text-center py-5"><i class="ti-book fs-1 text-cl-muted"></i><p class="mt-2 text-cl-muted">No hay cursos registrados.</p></div>
<?php endif; ?>
<?php foreach($cursos as $c):
    $eliminado = $c->curs_esta_ide == 2; ?>
<div class="col-xl-3 col-lg-4 col-md-6 curso-card-wrap" data-cate="<?=$c->curs_cate_ide?>" data-estado="<?=$eliminado?'deleted':'active'?>">
<div class="card curso-card h-100" style="<?=$eliminado?'border-color:rgba(239,68,68,.5)!important;opacity:.85;':''?>">
    <!-- Banda roja si eliminado -->
    <?php if($eliminado): ?>
    <div style="background:linear-gradient(90deg,#ef4444,#b91c1c);padding:6px 14px;border-radius:11px 11px 0 0;display:flex;align-items:center;justify-content:space-between;">
        <span style="color:#fff;font-size:.7rem;font-weight:700;letter-spacing:.05em;">⛔ ELIMINADO</span>
        <span style="color:rgba(255,255,255,.8);font-size:.65rem;">Por: <?=htmlspecialchars($c->elim_perfil??'').' - '.htmlspecialchars($c->elim_nombres??'')?></span>
    </div>
    <?php endif; ?>
    <div class="card-img-top d-flex align-items-center justify-content-center" style="height:120px;background:linear-gradient(135deg,<?=$eliminado?'#1a0505,#0d0f18':'#1e1b4b,#0d0f18'?>);">
        <i class="ti-code" style="font-size:2.8rem;color:<?=$eliminado?'#ef4444':'var(--cl-accent2)'?>;opacity:.45;"></i>
    </div>
    <div class="card-body d-flex flex-column" <?=!$eliminado ? 'onclick="verCurso('.$c->curs_ide.')" style="cursor:pointer;"' : "" ?>>
        <div class="d-flex justify-content-between mb-2">
            <span class="badge badge-nivel-<?=$c->curs_nivel?>"><?=$c->curs_nivel?></span>
            <small class="text-cl-muted"><?=$c->cate_nombre?></small>
        </div>
        <h6 class="fw-bold mb-1 <?=$eliminado?'text-danger':''?>"><?=htmlspecialchars($c->curs_nombre)?></h6>
        <p class="small text-cl-muted flex-grow-1 mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?=htmlspecialchars($c->curs_descripcion??'')?></p>
        <small class="text-cl-muted mb-2"><i class="ti-user me-1"></i><?=htmlspecialchars($c->usua_nombres.' '.$c->usua_paterno)?></small>
        <?php if($eliminado && $c->curs_motivo_baja): ?>
        <div class="mt-1 p-2 rounded" style="background:rgba(239,68,68,.08);border-left:3px solid #ef4444;">
            <small style="color:#f87171;font-style:italic;">"<?=htmlspecialchars($c->curs_motivo_baja)?>"</small>
        </div>
        <?php endif; ?>
        <?php if(!$eliminado): ?>
        <div class="d-flex justify-content-between mt-2" style="font-size:.72rem;color:var(--cl-muted);">
            <span><i class="ti-layers-alt me-1"></i><?=$c->total_secciones?> secc.</span>
            <span><i class="ti-video-camera me-1"></i><?=$c->total_lecciones?> lecc.</span>
            <span><i class="ti-user me-1"></i><?=$c->total_alumnos?></span>
        </div>
        <?php endif; ?>
    </div>
    <?php if(!$eliminado): ?>
    <div class="card-footer d-flex gap-2">
        <button class="btn btn-sm btn-outline-primary flex-fill" onclick="verCurso(<?=$c->curs_ide?>)"><i class="ti-pencil me-1"></i>Gestionar</button>
        <button class="btn btn-sm btn-outline-danger" onclick="abrirEliminar(<?=$c->curs_ide?>,'<?=addslashes($c->curs_nombre)?>')"><i class="ti-trash"></i></button>
    </div>
    <?php else: ?>
    <?php if($esAdmin): ?>
    <div class="card-footer">
        <button class="btn btn-sm btn-outline-success w-100" onclick="restaurarCurso(<?=$c->curs_ide?>)"><i class="ti-reload me-1"></i>Restaurar</button>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
</div>
<?php endforeach; ?>
</div>

<!-- Modal nuevo curso -->
<div class="modal fade" id="modalCurso" tabindex="-1">
<div class="modal-dialog modal-lg"><div class="modal-content" style="background:var(--cl-bg-card);">
  <div class="modal-header border-0"><h5 class="modal-title fw-bold"><i class="ti-plus me-2 text-accent"></i>Nuevo Curso</h5>
  <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <div class="row g-3">
      <div class="col-12"><label class="form-label small text-cl-muted">Nombre del Curso *</label>
        <input type="text" id="cNombre" class="form-control" placeholder="ej. Python desde cero"></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Categoría *</label>
        <select id="cCate" class="form-select"><option value="">Seleccionar…</option>
        <?php foreach($categorias as $cat): ?><option value="<?=$cat->cate_ide?>"><?=$cat->cate_nombre?></option><?php endforeach; ?>
        </select></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Nivel</label>
        <select id="cNivel" class="form-select"><option value="BASICO">Básico</option><option value="INTERMEDIO">Intermedio</option><option value="AVANZADO">Avanzado</option></select></div>
      <div class="col-12"><label class="form-label small text-cl-muted">Descripción</label>
        <textarea id="cDesc" class="form-control" rows="3" placeholder="Describe el contenido del curso…"></textarea></div>
    </div>
  </div>
  <div class="modal-footer border-0">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary px-4" onclick="guardarCurso()"><i class="ti-save me-1"></i>Guardar</button>
  </div>
</div></div></div>

<!-- Modal eliminar con motivo -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
<div class="modal-dialog"><div class="modal-content" style="background:var(--cl-bg-card);">
  <div class="modal-header border-0" style="border-bottom:1px solid rgba(239,68,68,.3)!important;">
    <h5 class="modal-title fw-bold text-danger"><i class="ti-trash me-2"></i>Eliminar Curso</h5>
    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
  </div>
  <div class="modal-body">
    <input type="hidden" id="elimIde">
    <p class="text-cl-muted mb-3">Estás eliminando: <strong id="elimNombre" class="text-danger"></strong></p>
    <label class="form-label small text-cl-muted">Motivo de eliminación <span class="text-cl-muted">(opcional)</span></label>
    <textarea id="elimMotivo" class="form-control" rows="3" placeholder='ej. "Los links no eran de clases" o "Me confundí al publicar"'></textarea>
    <div class="mt-2 p-2 rounded" style="background:rgba(239,68,68,.08);border-left:3px solid #ef4444;">
        <small style="color:#f87171;">El curso seguirá visible para el dueño y admins, marcado como eliminado con tu motivo.</small>
    </div>
  </div>
  <div class="modal-footer border-0">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-danger px-4" onclick="confirmarEliminar()"><i class="ti-trash me-1"></i>Eliminar</button>
  </div>
</div></div></div>

<script>
var catFiltro = 'all';
function filtrarCat(btn, filtro) {
    document.querySelectorAll('.filtro-cat').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active'); catFiltro = filtro;
    document.querySelectorAll('.curso-card-wrap').forEach(el => {
        var matchEstado = filtro==='all' || filtro===el.dataset.estado;
        var matchCate   = !filtro.startsWith('cate-') || el.dataset.cate == filtro.replace('cate-','');
        el.style.display = ((filtro==='all'||filtro==='active'||filtro==='deleted')?matchEstado:matchCate) ? '' : 'none';
    });
}
function abrirModalCurso(){new bootstrap.Modal(document.getElementById('modalCurso')).show();}
function verCurso(ide){cargarFuncion('/cursos/ver/'+ide,'Cursos','Gestionar Curso','Secciones y lecciones');}
function guardarCurso(){
    var n=document.getElementById('cNombre').value.trim(), c=document.getElementById('cCate').value;
    if(!n||!c){alertar('Completa nombre y categoría.','alert alert-warning');return;}
    openCargar(); bootstrap.Modal.getInstance(document.getElementById('modalCurso')).hide();
    $.post("<?=base_url('/cursos/guardar')?>",{nombre:n,cate_ide:c,nivel:document.getElementById('cNivel').value,descripcion:document.getElementById('cDesc').value},function(r){
        r=JSON.parse(r); closeCargar();
        if(r.ok){alertar(r.msg,'alert alert-success','ti-check');setTimeout(()=>cargarFuncion('/cursos','Cursos','Mis Cursos',''),1200);}
        else alertar(r.msg,'alert alert-danger','ti-close');
    });
}
function abrirEliminar(ide, nombre){
    document.getElementById('elimIde').value=ide;
    document.getElementById('elimNombre').innerHTML=nombre;
    document.getElementById('elimMotivo').value='';
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
function confirmarEliminar(){
    openCargar(); bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
    $.post("<?=base_url('/cursos/eliminar')?>",{ide:document.getElementById('elimIde').value,motivo:document.getElementById('elimMotivo').value},function(r){
        r=JSON.parse(r); closeCargar();
        if(r.ok){alertar(r.msg,'alert alert-success','ti-check');setTimeout(()=>cargarFuncion('/cursos','Cursos','Mis Cursos',''),1200);}
    });
}
function restaurarCurso(ide){
    if(!confirm('¿Restaurar este curso?')) return;
    $.post("<?=base_url('/cursos/restaurar')?>",{ide:ide},function(r){
        r=JSON.parse(r);if(r.ok){alertar(r.msg,'alert alert-success');setTimeout(()=>cargarFuncion('/cursos','Cursos','Gestión de Cursos',''),1000);}
    });
}
</script>
