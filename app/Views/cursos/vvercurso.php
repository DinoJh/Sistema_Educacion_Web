<?php
$esPro   = $session->perf_ide == 2;
$esAdmin = $session->perf_ide == 3;
$puedeEditar = $esPro || $esAdmin;
$lecBySecc = [];
foreach($lecciones as $l) $lecBySecc[$l->lecc_secc_ide ?? 0][] = $l;

// Check if profesor owns the course
$esDueno = false;
if ($esPro) {
    $db2 = \Config\Database::connect();
    $profCheck = $db2->table('profesores')->where('prof_usua_ide',$session->usua_ide)->get()->getRow();
    $esDueno = $profCheck && ($profCheck->prof_ide == $curso->curs_prof_ide);
    $puedeEditar = $esAdmin || $esDueno;
}
?>
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <button class="btn btn-sm btn-outline-secondary" onclick="cargarFuncion('/cursos','Cursos','Mis Cursos','')">
        <i class="ti-arrow-left me-1"></i>Volver
    </button>
    <div class="flex-grow-1">
        <h4 class="mb-0 fw-bold"><?=htmlspecialchars($curso->curs_nombre??'')?></h4>
        <small class="text-cl-muted"><?=$curso->cate_nombre?> &bull; <span class="badge badge-nivel-<?=$curso->curs_nivel?>"><?=$curso->curs_nivel?></span>
        &bull; Prof: <strong><?=htmlspecialchars($curso->usua_nombres.' '.$curso->usua_paterno)?></strong></small>
    </div>
    <?php if($puedeEditar): ?>
    <button class="btn btn-primary" onclick="abrirModalSeccion()"><i class="ti-plus me-1"></i>Nueva Sección</button>
    <?php endif; ?>
</div>

<?php if($curso->curs_descripcion): ?>
<div class="card mb-4"><div class="card-body"><p class="mb-0 text-cl-muted"><?=htmlspecialchars($curso->curs_descripcion)?></p></div></div>
<?php endif; ?>

<div class="row g-4">
<div class="col-lg-8">
<?php if(empty($secciones)): ?>
<div class="text-center py-5 card"><div class="card-body">
    <i class="ti-layout-accordion-list fs-1 text-cl-muted"></i>
    <p class="mt-2 text-cl-muted">No hay secciones aún.<?=$puedeEditar?' Crea la primera sección para empezar.':''?></p>
</div></div>
<?php endif; ?>

<?php foreach($secciones as $s): ?>
<div class="mb-3" id="secc-blk-<?=$s->secc_ide?>">
  <div class="seccion-header d-flex align-items-center justify-content-between" onclick="toggleSecc(<?=$s->secc_ide?>)">
    <div class="d-flex align-items-center gap-2">
        <i class="ti-angle-down" id="icon-secc-<?=$s->secc_ide?>"></i>
        <span class="fw-semibold"><?=htmlspecialchars($s->secc_nombre)?></span>
        <small class="text-cl-muted"><?=count($lecBySecc[$s->secc_ide]??[])?> lecciones</small>
    </div>
    <?php if($puedeEditar): ?>
    <div class="d-flex gap-1" onclick="event.stopPropagation()">
        <button class="btn btn-xs" style="padding:2px 8px;font-size:.7rem;background:rgba(124,58,237,.15);color:var(--cl-accent2);" onclick="abrirModalLeccion(<?=$s->secc_ide?>,<?=$curso->curs_ide?>)">
            <i class="ti-plus"></i> Lección
        </button>
        <button class="btn btn-xs btn-outline-danger" style="padding:2px 6px;font-size:.7rem;" onclick="if(confirm('¿Eliminar sección y sus lecciones?'))eliminarSecc(<?=$s->secc_ide?>)"><i class="ti-trash"></i></button>
    </div>
    <?php endif; ?>
  </div>
  <div id="body-secc-<?=$s->secc_ide?>" class="mt-1">
    <?php foreach($lecBySecc[$s->secc_ide]??[] as $l): ?>
    <div class="leccion-item p-3 d-flex align-items-center gap-3 rounded mb-1">
        <span class="tipo-badge tipo-<?=$l->lecc_tipo?>"><?=$l->lecc_tipo?></span>
        <div class="flex-grow-1">
            <div class="fw-medium"><?=htmlspecialchars($l->lecc_titulo)?></div>
            <?php if($l->lecc_descripcion): ?>
            <small class="text-cl-muted"><?=htmlspecialchars($l->lecc_descripcion)?></small>
            <?php endif; ?>
            <?php if($l->lecc_url): ?>
            <div class="mt-1"><small class="text-accent" style="font-size:.7rem;word-break:break-all;"><?=htmlspecialchars($l->lecc_url)?></small></div>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <?php if($l->lecc_es_preview): ?><span style="font-size:.62rem;background:rgba(6,182,212,.15);color:#06b6d4;padding:2px 6px;border-radius:99px;">PREVIEW</span><?php endif; ?>
            <?php if($l->lecc_duracion): ?><small class="text-cl-muted"><?=$l->lecc_duracion?>m</small><?php endif; ?>
            <?php if(($l->lecc_tipo=='VIDEO'||$l->lecc_tipo=='ARCHIVO')&&$l->lecc_url): ?>
            <button class="btn btn-xs" style="padding:3px 8px;background:rgba(124,58,237,.15);color:var(--cl-accent2);font-size:.7rem;" onclick="previewVideo('<?=htmlspecialchars(addslashes($l->lecc_url))?>','<?=htmlspecialchars(addslashes($l->lecc_titulo??''))?>','<?=$l->lecc_tipo?>')">▶ Ver</button>
            <?php endif; ?>
            <?php if($puedeEditar): ?>
            <button class="btn btn-xs btn-outline-danger" style="padding:2px 6px;font-size:.7rem;" onclick="if(confirm('¿Eliminar lección?'))$.post('<?=base_url('/lecciones/eliminar')?>',{ide:<?=$l->lecc_ide?>},function(){recargarCurso(<?=$curso->curs_ide?>);})"><i class="ti-trash"></i></button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(empty($lecBySecc[$s->secc_ide]??[])): ?>
    <div class="text-center py-3 text-cl-muted small">Sin lecciones en esta sección.
        <?php if($puedeEditar): ?><a href="#" class="text-accent" onclick="abrirModalLeccion(<?=$s->secc_ide?>,<?=$curso->curs_ide?>)">+ Agregar</a><?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>
</div>

<!-- Panel lateral derecho -->
<div class="col-lg-4">
    <div class="card mb-3"><div class="card-body">
        <h6 class="fw-bold mb-3">📊 Estadísticas</h6>
        <div class="d-flex justify-content-between mb-2"><span class="text-cl-muted small">Secciones</span><span class="fw-semibold"><?=count($secciones)?></span></div>
        <div class="d-flex justify-content-between mb-2"><span class="text-cl-muted small">Lecciones</span><span class="fw-semibold"><?=count($lecciones)?></span></div>
        <div class="d-flex justify-content-between"><span class="text-cl-muted small">Instructor</span><span class="fw-semibold small"><?=htmlspecialchars($curso->usua_nombres??'')?></span></div>
    </div></div>
    <?php if($puedeEditar): ?>
    <div class="card mb-3"><div class="card-body">
        <h6 class="fw-bold mb-3">⚡ Acciones rápidas</h6>
        <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="abrirModalSeccion()"><i class="ti-plus me-1"></i>Nueva Sección</button>
        <?php if(!empty($secciones)): ?>
        <button class="btn btn-outline-secondary btn-sm w-100 mb-2" onclick="abrirModalLeccion(<?=$secciones[0]->secc_ide?>,<?=$curso->curs_ide?>)"><i class="ti-video-camera me-1"></i>Nueva Lección</button>
        <?php endif; ?>
        <button class="btn btn-outline-warning btn-sm w-100" onclick="abrirEditarCurso()"><i class="ti-pencil me-1"></i>Editar Info Curso</button>
    </div></div>
    <?php endif; ?>
</div>
</div>

<!-- Modal preview video/archivo -->
<div class="modal fade" id="modalVideo" tabindex="-1">
<div class="modal-dialog modal-xl"><div class="modal-content" style="background:var(--cl-bg-card);">
  <div class="modal-header border-0"><h6 class="modal-title fw-bold" id="videoTitulo"></h6>
  <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <div class="modal-body"><div class="video-wrapper"><iframe id="videoFrame" src="" allowfullscreen></iframe></div></div>
</div></div></div>

<!-- Modal nueva sección -->
<div class="modal fade" id="modalSeccion" tabindex="-1">
<div class="modal-dialog"><div class="modal-content" style="background:var(--cl-bg-card);">
  <div class="modal-header border-0"><h5 class="modal-title fw-bold">Nueva Sección</h5>
  <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <label class="form-label small text-cl-muted">Nombre de la sección *</label>
    <input type="text" id="seccNombre" class="form-control" placeholder="ej. Introducción al lenguaje">
  </div>
  <div class="modal-footer border-0">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary" onclick="guardarSeccion()">Guardar</button>
  </div>
</div></div></div>

<!-- Modal editar curso -->
<div class="modal fade" id="modalEditarCurso" tabindex="-1">
<div class="modal-dialog modal-lg"><div class="modal-content" style="background:var(--cl-bg-card);">
  <div class="modal-header border-0"><h5 class="modal-title fw-bold"><i class="ti-pencil me-2"></i>Editar Curso</h5>
  <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <div class="row g-3">
      <div class="col-12"><label class="form-label small text-cl-muted">Nombre *</label>
        <input type="text" id="eCursNombre" class="form-control" value="<?=htmlspecialchars($curso->curs_nombre??'')?>"></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Nivel</label>
        <select id="eCursNivel" class="form-select">
          <option value="BASICO" <?=$curso->curs_nivel=='BASICO'?'selected':''?>>Básico</option>
          <option value="INTERMEDIO" <?=$curso->curs_nivel=='INTERMEDIO'?'selected':''?>>Intermedio</option>
          <option value="AVANZADO" <?=$curso->curs_nivel=='AVANZADO'?'selected':''?>>Avanzado</option>
        </select></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Categoría</label>
        <select id="eCursCate" class="form-select">
        <?php foreach($categorias??[] as $cat): ?>
        <option value="<?=$cat->cate_ide?>" <?=$cat->cate_ide==$curso->curs_cate_ide?'selected':''?>><?=$cat->cate_nombre?></option>
        <?php endforeach; ?>
        </select></div>
      <div class="col-12"><label class="form-label small text-cl-muted">Descripción</label>
        <textarea id="eCursDesc" class="form-control" rows="3"><?=htmlspecialchars($curso->curs_descripcion??'')?></textarea></div>
    </div>
  </div>
  <div class="modal-footer border-0">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary" onclick="guardarEditCurso()"><i class="ti-save me-1"></i>Guardar</button>
  </div>
</div></div></div>

<!-- Modal nueva lección -->
<div class="modal fade" id="modalLeccion" tabindex="-1">
<div class="modal-dialog modal-lg"><div class="modal-content" style="background:var(--cl-bg-card);">
  <div class="modal-header border-0"><h5 class="modal-title fw-bold"><i class="ti-video-camera me-2 text-accent"></i>Nueva Lección</h5>
  <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" id="lCursIde"><input type="hidden" id="lSeccIde">
    <div class="row g-3">
      <div class="col-12"><label class="form-label small text-cl-muted">Título *</label>
        <input type="text" id="lTitulo" class="form-control" placeholder="ej. ¿Qué es Python?"></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Tipo</label>
        <select id="lTipo" class="form-select" onchange="toggleTipo()">
          <option value="VIDEO">📹 Video (YouTube / Drive)</option>
          <option value="TEXTO">📄 Texto / Nota</option>
          <option value="ARCHIVO">📎 Archivo / Link externo</option>
        </select></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Duración (min)</label>
        <input type="number" id="lDuracion" class="form-control" placeholder="ej. 15"></div>
      <div id="campoUrl" class="col-12">
        <label class="form-label small text-cl-muted">URL del video <span class="text-cl-muted">(YouTube o Google Drive)</span></label>
        <div class="d-flex gap-2">
            <input type="text" id="lUrl" class="form-control" placeholder="https://youtube.com/watch?v=…  o  https://drive.google.com/…">
            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="previsualizarEmbed()" style="white-space:nowrap;"><i class="ti-eye"></i> Ver</button>
        </div>
        <div id="previewEmbed" class="mt-2" style="display:none;"><div class="video-wrapper"><iframe id="embedPreview" src="" allowfullscreen></iframe></div></div>
      </div>
      <div id="campoArchivo" class="col-12" style="display:none;">
        <label class="form-label small text-cl-muted">URL / Enlace del archivo</label>
        <input type="text" id="lArchivoUrl" class="form-control" placeholder="https://… o /archivos/guia.pdf"></div>
      <div class="col-12"><label class="form-label small text-cl-muted">Descripción breve</label>
        <textarea id="lDesc" class="form-control" rows="2" placeholder="Descripción corta de la lección"></textarea></div>
      <div class="col-md-6"><div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" id="lPreview">
        <label class="form-check-label small text-cl-muted" for="lPreview">Vista previa gratuita (sin inscripción)</label>
      </div></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Orden</label>
        <input type="number" id="lOrden" class="form-control" value="1" min="1"></div>
    </div>
  </div>
  <div class="modal-footer border-0">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary px-4" onclick="guardarLeccion()"><i class="ti-save me-1"></i>Guardar Lección</button>
  </div>
</div></div></div>

<script>
var cursIdeActual = <?=$curso->curs_ide??0?>;
var catsData = <?=json_encode(array_map(fn($c)=>['ide'=>$c->cate_ide,'nombre'=>$c->cate_nombre],$categorias??[]))?>;

function toggleSecc(ide){
    var b=document.getElementById('body-secc-'+ide);
    var ic=document.getElementById('icon-secc-'+ide);
    if(b){b.style.display=b.style.display==='none'?'':'none'; ic.className=b.style.display==='none'?'ti-angle-right':'ti-angle-down';}
}
function previewVideo(url,titulo,tipo){
    var e = tipo==='ARCHIVO' ? url : embedUrl(url);
    document.getElementById('videoTitulo').innerHTML=titulo;
    document.getElementById('videoFrame').src=e||url;
    new bootstrap.Modal(document.getElementById('modalVideo')).show();
}
document.getElementById('modalVideo').addEventListener('hidden.bs.modal',function(){document.getElementById('videoFrame').src='';});

function abrirModalSeccion(){new bootstrap.Modal(document.getElementById('modalSeccion')).show();}
function abrirEditarCurso(){new bootstrap.Modal(document.getElementById('modalEditarCurso')).show();}
function abrirModalLeccion(seccIde,cursIde){
    document.getElementById('lSeccIde').value=seccIde;
    document.getElementById('lCursIde').value=cursIde;
    document.getElementById('lUrl').value='';
    document.getElementById('previewEmbed').style.display='none';
    document.getElementById('lTitulo').value='';
    document.getElementById('lDesc').value='';
    toggleTipo();
    new bootstrap.Modal(document.getElementById('modalLeccion')).show();
}
function toggleTipo(){
    var t=document.getElementById('lTipo').value;
    document.getElementById('campoUrl').style.display=(t==='VIDEO')?'':'none';
    document.getElementById('campoArchivo').style.display=(t==='ARCHIVO')?'':'none';
}
function previsualizarEmbed(){
    var url=document.getElementById('lUrl').value.trim();
    var e=embedUrl(url);
    if(!e){alertar('URL no reconocida. Prueba con un link de YouTube o Google Drive.','alert alert-warning');return;}
    document.getElementById('embedPreview').src=e;
    document.getElementById('previewEmbed').style.display='';
}
function guardarSeccion(){
    var n=document.getElementById('seccNombre').value.trim();
    if(!n){alertar('Escribe el nombre de la sección.','alert alert-warning');return;}
    bootstrap.Modal.getInstance(document.getElementById('modalSeccion')).hide();
    setTimeout(function(){
        openCargar();
        $.post("<?=base_url('/secciones/guardar')?>",{curs_ide:cursIdeActual,nombre:n},function(){recargarCurso(cursIdeActual);});
    }, 400);
}
function guardarEditCurso(){
    bootstrap.Modal.getInstance(document.getElementById('modalEditarCurso')).hide();
    setTimeout(function(){
        openCargar();
        $.post("<?=base_url('/cursos/actualizar')?>",{
            ide:cursIdeActual,
            nombre:document.getElementById('eCursNombre').value,
            nivel:document.getElementById('eCursNivel').value,
            cate_ide:document.getElementById('eCursCate').value,
            descripcion:document.getElementById('eCursDesc').value
        },function(r){r=JSON.parse(r);closeCargar();alertar(r.msg,'alert alert-success');setTimeout(()=>recargarCurso(cursIdeActual),1000);});
    }, 400);
}
function guardarLeccion(){
    var t=document.getElementById('lTitulo').value.trim();
    if(!t){alertar('Escribe el título de la lección.','alert alert-warning');return;}
    bootstrap.Modal.getInstance(document.getElementById('modalLeccion')).hide();
    setTimeout(function(){
        openCargar();
        $.post("<?=base_url('/lecciones/guardar')?>",{
            curs_ide:document.getElementById('lCursIde').value,
            secc_ide:document.getElementById('lSeccIde').value,
            titulo:t, tipo:document.getElementById('lTipo').value,
            url:document.getElementById('lUrl').value,
            archivo_url:document.getElementById('lArchivoUrl').value,
            descripcion:document.getElementById('lDesc').value,
            duracion:document.getElementById('lDuracion').value,
            es_preview:document.getElementById('lPreview').checked?1:0,
            orden:document.getElementById('lOrden').value
        },function(r){r=JSON.parse(r);recargarCurso(cursIdeActual);});
    }, 400);
}
function eliminarSecc(ide){
    openCargar();
    $.post("<?=base_url('/secciones/eliminar')?>",{ide:ide},function(){recargarCurso(cursIdeActual);});
}
function recargarCurso(ide){cargarFuncion('/cursos/ver/'+ide,'Cursos','Gestionar Curso','');}

// Pass categorias to editar modal
(function(){
    var sel=document.getElementById('eCursCate');
    if(sel && catsData.length > 0 && sel.options.length === 0){
        catsData.forEach(function(c){var o=new Option(c.nombre,c.ide);sel.add(o);});
    }
})();
</script>
