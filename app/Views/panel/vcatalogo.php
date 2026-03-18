<div class="mb-4">
    <h4 class="fw-bold mb-1">🔍 Buscar Cursos</h4>
    <p class="text-cl-muted small">Explora todos los cursos disponibles y únete gratis</p>
</div>

<!-- Buscador -->
<div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
    <input type="text" id="buscadorTexto" class="form-control" style="max-width:300px;" placeholder="Buscar por nombre..." oninput="filtrarCursos()">
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-sm btn-outline-secondary active filtro-cat" onclick="filtrarCat(this,'')">Todos</button>
        <?php foreach($categorias as $c): ?>
        <button class="btn btn-sm btn-outline-secondary filtro-cat" onclick="filtrarCat(this,'<?=$c->cate_ide?>')"><?=$c->cate_nombre?></button>
        <?php endforeach; ?>
    </div>
</div>

<div class="row g-3" id="gridCatalogo">
<?php if(empty($cursos)): ?>
<div class="col-12 text-center py-5 text-cl-muted"><i class="ti-book fs-1"></i><p class="mt-2">No hay cursos disponibles.</p></div>
<?php endif; ?>
<?php foreach($cursos as $c): ?>
<div class="col-xl-3 col-lg-4 col-md-6 curso-card-wrap" data-cate="<?=$c->curs_cate_ide?>" data-nombre="<?=strtolower($c->curs_nombre)?>">
<div class="card curso-card h-100">
    <div class="card-img-top d-flex align-items-center justify-content-center" style="height:130px;background:linear-gradient(135deg,#1e1b4b,#0d0f18);">
        <i class="ti-code" style="font-size:2.8rem;color:var(--cl-accent2);opacity:.45;"></i>
    </div>
    <div class="card-body d-flex flex-column" onclick="cargarFuncion('/mi-panel/ver/<?=$c->curs_ide?>','Mi Panel','<?=addslashes($c->curs_nombre)?>','')">
        <div class="d-flex justify-content-between mb-2">
            <span class="badge badge-nivel-<?=$c->curs_nivel?>"><?=$c->curs_nivel?></span>
            <?php if($c->ya_matriculado): ?>
            <span style="font-size:.65rem;background:rgba(16,185,129,.15);color:#10b981;padding:2px 8px;border-radius:99px;font-weight:600;">✓ Inscrito</span>
            <?php endif; ?>
        </div>
        <h6 class="fw-bold mb-1"><?=htmlspecialchars($c->curs_nombre)?></h6>
        <p class="small text-cl-muted flex-grow-1 mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?=htmlspecialchars($c->curs_descripcion??'')?></p>
        <small class="text-cl-muted mb-2"><i class="ti-user me-1"></i><?=htmlspecialchars($c->usua_nombres.' '.$c->usua_paterno)?></small>
        <div class="d-flex justify-content-between" style="font-size:.72rem;color:var(--cl-muted);">
            <span><i class="ti-video-camera me-1"></i><?=$c->total_lecciones?> lecciones</span>
            <span><i class="ti-user me-1"></i><?=$c->total_alumnos?> alumnos</span>
            <?php if($c->promedio_nota): ?>
            <span style="color:#f59e0b;cursor:pointer;" onclick="event.stopPropagation();verResenas(<?=$c->curs_ide?>,<?=htmlspecialchars(json_encode($c->curs_nombre))?>)">★ <?=$c->promedio_nota?>/20</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-footer">
        <?php if($c->ya_matriculado): ?>
        <button class="btn btn-success btn-sm w-100" onclick="cargarFuncion('/mi-panel/ver/<?=$c->curs_ide?>','Mi Panel','<?=addslashes($c->curs_nombre)?>','')"><i class="ti-arrow-right me-1"></i>Continuar</button>
        <?php else: ?>
        <button class="btn btn-primary btn-sm w-100" onclick="inscribirme(<?=$c->curs_ide?>,this)"><i class="ti-plus me-1"></i>Inscribirme gratis</button>
        <?php endif; ?>
    </div>
</div>
</div>
<?php endforeach; ?>
</div>

<script>
var catActiva = '';
function filtrarCat(btn, cate) {
    document.querySelectorAll('.filtro-cat').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    catActiva = cate;
    filtrarCursos();
}
function filtrarCursos() {
    var texto = document.getElementById('buscadorTexto').value.toLowerCase();
    document.querySelectorAll('.curso-card-wrap').forEach(el => {
        var matchCat  = !catActiva || el.dataset.cate == catActiva;
        var matchText = !texto || (el.dataset.nombre||'').includes(texto);
        el.style.display = (matchCat && matchText) ? '' : 'none';
    });
}
function inscribirme(ide, btn) {
    btn.disabled=true;
    btn.innerHTML='<span class="spinner-border spinner-border-sm"></span>';
    $.post("<?=base_url('/mi-panel/matricular')?>", {curs_ide:ide}, function(r){
        r=JSON.parse(r);
        if(r.ok){alertar(r.msg,'alert alert-success','ti-check');setTimeout(()=>cargarFuncion('/mi-panel/catalogo','Mi Panel','Buscar Cursos',''),1200);}
    });
}
function verResenas(curs_ide, nombre) {
    document.getElementById('modalResenasTitle').innerHTML = '⭐ Reseñas: ' + nombre;
    document.getElementById('modalResenasBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border"></div></div>';
    new bootstrap.Modal(document.getElementById('modalResenas')).show();
    $.get("<?=base_url('/mi-panel/resenas/')?>"+curs_ide, function(r){
        r = JSON.parse(r);
        if(!r.length){
            document.getElementById('modalResenasBody').innerHTML='<p class="text-cl-muted text-center py-3">Aún no hay reseñas para este curso.</p>';
            return;
        }
        var html='';
        r.forEach(function(x){
            var nota = x.rese_calificacion;
            var color = nota>=14?'#10b981':(nota>=11?'#f59e0b':'#ef4444');
            html += '<div class="mb-3 p-3 rounded" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);">';
            html += '<div class="d-flex align-items-center gap-2 mb-1">';
            html += '<span class="fw-semibold small">'+x.nombre+'</span>';
            html += '<span class="ms-auto fw-bold" style="color:'+color+'">'+nota+'/20</span>';
            html += '<small class="text-cl-muted">'+x.fecha+'</small>';
            html += '</div>';
            html += '<div class="progress mb-2" style="height:4px;background:rgba(255,255,255,.06);"><div class="progress-bar" style="width:'+((nota/20)*100)+'%;background:'+color+';"></div></div>';
            html += '<p class="small text-cl-muted mb-0">'+x.comentario+'</p>';
            html += '</div>';
        });
        document.getElementById('modalResenasBody').innerHTML = html;
    });
}
</script>

<!-- Modal Reseñas -->
<div class="modal fade" id="modalResenas" tabindex="-1">
<div class="modal-dialog modal-lg"><div class="modal-content" style="background:var(--cl-bg-card);">
  <div class="modal-header border-0">
    <h5 class="modal-title fw-bold" id="modalResenasTitle"></h5>
    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
  </div>
  <div class="modal-body" id="modalResenasBody" style="max-height:70vh;overflow-y:auto;"></div>
</div></div></div>
