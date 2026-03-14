<?php $esPro=$session->perf_ide==2; $esAdmin=$session->perf_ide==3; ?>
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div><h4 class="mb-1 fw-bold">📚 <?=$esPro?'Mis Cursos':'Gestión de Cursos'?></h4>
    <small class="text-cl-muted"><?=count($cursos)?> curso(s)</small></div>
    <?php if($esPro||$esAdmin): ?>
    <button class="btn btn-primary px-4" onclick="abrirModalCurso()"><i class="ti-plus me-1"></i>Nuevo Curso</button>
    <?php endif; ?>
</div>
<div class="d-flex gap-2 flex-wrap mb-4">
    <button class="btn btn-sm btn-outline-secondary active filtro-cat" onclick="filtrarCat(this,'')">Todos</button>
    <?php foreach($categorias as $c): ?>
    <button class="btn btn-sm btn-outline-secondary filtro-cat" onclick="filtrarCat(this,'<?=$c->cate_ide?>')"><?=$c->cate_nombre?></button>
    <?php endforeach; ?>
</div>
<div class="row g-3" id="gridCursos">
<?php if(empty($cursos)): ?>
<div class="col-12 text-center py-5"><i class="ti-book fs-1 text-cl-muted"></i><p class="mt-2 text-cl-muted">No hay cursos registrados.</p></div>
<?php endif; ?>
<?php foreach($cursos as $c): ?>
<div class="col-xl-3 col-lg-4 col-md-6 curso-card-wrap" data-cate="<?=$c->curs_cate_ide?>">
<div class="card curso-card h-100">
  <div class="card-img-top d-flex align-items-center justify-content-center" style="height:140px;background:linear-gradient(135deg,#1e1b4b,#0d0f18);">
    <i class="ti-code" style="font-size:3rem;color:var(--cl-accent2);opacity:.5;"></i>
  </div>
  <div class="card-body d-flex flex-column" onclick="verCurso(<?=$c->curs_ide?>)">
    <div class="d-flex justify-content-between mb-2">
      <span class="badge badge-nivel-<?=$c->curs_nivel?>"><?=$c->curs_nivel?></span>
      <small class="text-cl-muted"><?=$c->cate_nombre?></small>
    </div>
    <h6 class="fw-bold mb-1"><?=htmlspecialchars($c->curs_nombre)?></h6>
    <p class="small text-cl-muted flex-grow-1 mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?=htmlspecialchars($c->curs_descripcion??'')?></p>
    <small class="text-cl-muted mb-3"><i class="ti-user me-1"></i><?=htmlspecialchars($c->usua_nombres.' '.$c->usua_paterno)?></small>
    <div class="d-flex justify-content-between" style="font-size:.72rem;color:var(--cl-muted);">
      <span><i class="ti-layers-alt me-1"></i><?=$c->total_secciones?> secc.</span>
      <span><i class="ti-video-camera me-1"></i><?=$c->total_lecciones?> lecciones</span>
      <span><i class="ti-user me-1"></i><?=$c->total_alumnos?></span>
    </div>
  </div>
  <?php if($esPro||$esAdmin): ?>
  <div class="card-footer d-flex gap-2">
    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="verCurso(<?=$c->curs_ide?>)"><i class="ti-pencil"></i> Gestionar</button>
    <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Eliminar?'))$.post('<?=base_url('/cursos/eliminar')?>',{ide:<?=$c->curs_ide?>},function(r){r=JSON.parse(r);alertar(r.msg,'alert alert-success');setTimeout(()=>cargarFuncion('/cursos','Cursos','Mis Cursos',''),1000);})"><i class="ti-trash"></i></button>
  </div>
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

<script>
function filtrarCat(btn,cate){
  document.querySelectorAll('.filtro-cat').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.curso-card-wrap').forEach(el=>el.style.display=(!cate||el.dataset.cate==cate)?'':'none');
}
function abrirModalCurso(){new bootstrap.Modal(document.getElementById('modalCurso')).show();}
function verCurso(ide){cargarFuncion('/cursos/ver/'+ide,'Cursos','Gestionar Curso','Secciones y lecciones');}
function guardarCurso(){
  var n=document.getElementById('cNombre').value.trim(),c=document.getElementById('cCate').value;
  if(!n||!c){alertar('Completa nombre y categoría.','alert alert-warning');return;}
  openCargar();bootstrap.Modal.getInstance(document.getElementById('modalCurso')).hide();
  $.post("<?=base_url('/cursos/guardar')?>",{nombre:n,cate_ide:c,nivel:document.getElementById('cNivel').value,descripcion:document.getElementById('cDesc').value},function(r){
    r=JSON.parse(r);closeCargar();
    if(r.ok){alertar(r.msg,'alert alert-success','ti-check');setTimeout(()=>cargarFuncion('/cursos','Cursos','Mis Cursos',''),1200);}
    else alertar(r.msg,'alert alert-danger','ti-close');
  });
}
</script>
