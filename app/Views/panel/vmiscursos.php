<?php $uid = $session->usua_ide; ?>
<div class="mb-4">
    <h4 class="fw-bold mb-1">🎓 Explorar Cursos</h4>
    <p class="text-cl-muted small">Encuentra el curso que quieres aprender</p>
</div>

<!-- Filtros -->
<div class="d-flex gap-2 flex-wrap mb-4">
    <button class="btn btn-sm btn-outline-secondary active filtro-cat" onclick="filtrarCat(this,'')">Todos</button>
    <?php foreach($categorias as $c): ?>
    <button class="btn btn-sm btn-outline-secondary filtro-cat" onclick="filtrarCat(this,'<?=$c->cate_ide?>')"><?=$c->cate_nombre?></button>
    <?php endforeach; ?>
</div>

<div class="row g-3">
<?php if(empty($cursos)): ?>
<div class="col-12 text-center py-5"><i class="ti-book fs-1 text-cl-muted"></i><p class="mt-3 text-cl-muted">No hay cursos disponibles.</p></div>
<?php endif; ?>
<?php foreach($cursos as $c): ?>
<div class="col-xl-3 col-lg-4 col-md-6 curso-card-wrap" data-cate="<?=$c->curs_cate_ide?>">
<div class="card curso-card h-100" onclick="verCursoAlumno(<?=$c->curs_ide?>)">
    <div class="card-img-top d-flex align-items-center justify-content-center" style="height:140px;background:linear-gradient(135deg,#1e1b4b,#0d0f18);">
        <i class="ti-code" style="font-size:3rem;color:var(--cl-accent2);opacity:.45;"></i>
    </div>
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between mb-2">
            <span class="badge badge-nivel-<?=$c->curs_nivel?>"><?=$c->curs_nivel?></span>
            <?php if($c->ya_matriculado): ?>
            <span class="badge" style="background:rgba(16,185,129,.15);color:#10b981;font-size:.65rem;">✓ INSCRITO</span>
            <?php endif; ?>
        </div>
        <h6 class="fw-bold mb-1"><?=htmlspecialchars($c->curs_nombre)?></h6>
        <p class="small text-cl-muted flex-grow-1" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?=htmlspecialchars($c->curs_descripcion??'')?></p>
        <small class="text-cl-muted mb-2"><i class="ti-user me-1"></i><?=htmlspecialchars($c->usua_nombres.' '.$c->usua_paterno)?></small>
        <div class="d-flex justify-content-between align-items-center" style="font-size:.72rem;">
            <span class="text-cl-muted"><i class="ti-video-camera me-1"></i><?=$c->total_lecciones?> lecciones</span>
            <span class="text-cl-muted"><i class="ti-user me-1"></i><?=$c->total_alumnos?> alumnos</span>
            <?php if($c->promedio_nota): ?>
            <span style="color:#f59e0b;">★ <?=$c->promedio_nota?>/20</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-footer">
        <?php if($c->ya_matriculado): ?>
        <button class="btn btn-success btn-sm w-100" onclick="verCursoAlumno(<?=$c->curs_ide?>)"><i class="ti-arrow-right me-1"></i>Continuar</button>
        <?php else: ?>
        <button class="btn btn-primary btn-sm w-100" onclick="matricularme(<?=$c->curs_ide?>,this)"><i class="ti-plus me-1"></i>Inscribirme gratis</button>
        <?php endif; ?>
    </div>
</div>
</div>
<?php endforeach; ?>
</div>

<script>
function filtrarCat(btn,cate){
    document.querySelectorAll('.filtro-cat').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.curso-card-wrap').forEach(el=>el.style.display=(!cate||el.dataset.cate==cate)?'':'none');
}
function verCursoAlumno(ide){cargarFuncion('/mi-panel/ver/'+ide,'Mi Panel','Ver Curso','');}
function matricularme(ide,btn){
    btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm"></span>';
    $.post("<?=base_url('/mi-panel/matricular')?>",{curs_ide:ide},function(r){
        r=JSON.parse(r);
        if(r.ok){alertar(r.msg,'alert alert-success','ti-check');setTimeout(()=>cargarFuncion('/mi-panel/cursos','Mi Panel','Mis Cursos',''),1200);}
    });
}
</script>
