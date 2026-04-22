<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">🎓 Cursos Disponibles</h4>
        <small class="text-cl-muted">Selecciona un curso para ver y asesorar a sus alumnos</small>
    </div>
</div>

<!-- Buscador -->
<div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
    <input type="text" id="buscarCurso" class="form-control" style="max-width:320px;"
           placeholder="Buscar por nombre o categoría..."
           oninput="filtrarCursosAsesor(this.value)">
</div>

<div class="row g-3" id="gridCursosAsesor">
<?php if(empty($cursos)): ?>
<div class="col-12 text-center py-5 text-cl-muted">
    <i class="ti-book" style="font-size:2.5rem;"></i>
    <p class="mt-2">No hay cursos activos en la plataforma.</p>
</div>
<?php endif; ?>

<?php foreach($cursos as $c): ?>
<div class="col-xl-3 col-lg-4 col-md-6 curso-asesor-wrap"
     data-nombre="<?=strtolower(htmlspecialchars($c->curs_nombre.' '.$c->cate_nombre))?>">
<div class="card h-100" style="cursor:pointer;transition:transform .15s,box-shadow .15s;"
     onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.25)'"
     onmouseleave="this.style.transform='';this.style.boxShadow=''">
    <!-- Header color según nivel -->
    <?php
        $colores = ['BASICO'=>'#1e3a5f,#0d47a1','INTERMEDIO'=>'#1b4332,#1a6b47','AVANZADO'=>'#4a1942,#7b1fa2'];
        $grad = $colores[$c->curs_nivel] ?? '1e1b4b,#0d0f18';
    ?>
    <div class="d-flex align-items-center justify-content-center"
         style="height:110px;background:linear-gradient(135deg,<?=$grad?>);border-radius:.375rem .375rem 0 0;">
        <i class="<?=$c->cate_icono ?? 'ti-book'?>"
           style="font-size:2.5rem;color:rgba(255,255,255,.35);"></i>
    </div>

    <div class="card-body d-flex flex-column"
         onclick="cargarFuncion('/asesor/alumnos/<?=$c->curs_ide?>','Asesoría','<?=addslashes(htmlspecialchars($c->curs_nombre))?>','')">
        <div class="d-flex justify-content-between mb-2 align-items-start">
            <span class="badge badge-nivel-<?=$c->curs_nivel?>"><?=$c->curs_nivel?></span>
            <small class="text-cl-muted"><i class="ti-tag me-1"></i><?=htmlspecialchars($c->cate_nombre??'')?></small>
        </div>
        <h6 class="fw-bold mb-1"><?=htmlspecialchars($c->curs_nombre)?></h6>
        <p class="small text-cl-muted flex-grow-1 mb-2"
           style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
            <?=htmlspecialchars($c->curs_descripcion??'')?>
        </p>
        <small class="text-cl-muted mb-2">
            <i class="ti-user me-1"></i>Prof.
            <?=htmlspecialchars($c->usua_paterno.' '.$c->usua_nombres)?>
        </small>
        <div class="d-flex justify-content-between mt-1" style="font-size:.72rem;color:var(--cl-muted);">
            <span><i class="ti-user me-1"></i><?=$c->total_alumnos?> alumno(s)</span>
        </div>
    </div>

    <div class="card-footer p-2">
        <button class="btn btn-sm w-100"
                style="background:rgba(124,58,237,.15);color:var(--cl-accent2);font-size:.8rem;"
                onclick="cargarFuncion('/asesor/alumnos/<?=$c->curs_ide?>','Asesoría','<?=addslashes(htmlspecialchars($c->curs_nombre))?>','')">
            <i class="ti-eye me-1"></i>Ver Alumnos
        </button>
    </div>
</div>
</div>
<?php endforeach; ?>
</div>

<script>
function filtrarCursosAsesor(texto) {
    texto = texto.toLowerCase();
    document.querySelectorAll('.curso-asesor-wrap').forEach(el => {
        el.style.display = !texto || (el.dataset.nombre||'').includes(texto) ? '' : 'none';
    });
}
</script>
