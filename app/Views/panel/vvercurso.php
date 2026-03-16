<?php
$lecBySecc = [];
foreach($lecciones as $l) $lecBySecc[$l->lecc_secc_ide ?? 0][] = $l;
$totalLecc = count($lecciones);
$hechas = 0;
foreach($progreso as $v) { if($v==1) $hechas++; }
$pct = $totalLecc > 0 ? round(($hechas/$totalLecc)*100) : 0;
$primeraLecc = !empty($lecciones) ? $lecciones[0] : null;
?>
<div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
    <button class="btn btn-sm btn-outline-secondary" onclick="cargarFuncion('/mi-panel/cursos','Mi Panel','Explorar Cursos','')">
        <i class="ti-arrow-left me-1"></i>Volver
    </button>
    <div class="flex-grow-1">
        <h4 class="mb-0 fw-bold"><?=htmlspecialchars($curso->curs_nombre??'')?></h4>
        <small class="text-cl-muted"><?=$curso->cate_nombre?> &bull; <span class="badge badge-nivel-<?=$curso->curs_nivel?>"><?=$curso->curs_nivel?></span></small>
    </div>
    <?php if(!$matricula): ?>
    <button class="btn btn-primary" onclick="matricularme(<?=$curso->curs_ide?>)"><i class="ti-plus me-1"></i>Inscribirme</button>
    <?php endif; ?>
</div>

<?php if($matricula): ?>
<div class="card mb-3"><div class="card-body py-2">
    <div class="d-flex align-items-center gap-3">
        <small class="text-cl-muted">Tu progreso:</small>
        <div class="progress flex-grow-1"><div class="progress-bar" style="width:<?=$pct?>%"></div></div>
        <small class="fw-semibold"><?=$pct?>%</small>
        <small class="text-cl-muted"><?=$hechas?>/<?=$totalLecc?> lecciones</small>
        <?php if($matricula->matr_completado): ?>
        <span class="badge" style="background:rgba(16,185,129,.2);color:#10b981;">🎓 COMPLETADO</span>
        <?php endif; ?>
    </div>
</div></div>
<?php endif; ?>

<div class="row g-3">
<!-- Player principal -->
<div class="col-lg-8">
    <div class="card mb-3">
        <div class="card-body p-2">
            <div id="playerArea">
                <div class="video-wrapper d-flex align-items-center justify-content-center" style="background:#0d0f18;border-radius:12px;">
                    <div class="text-center py-5">
                        <i class="ti-play-circle fs-1 text-accent"></i>
                        <p class="mt-2 text-cl-muted small">Selecciona una lección para comenzar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card" id="infoLeccion" style="display:none;">
        <div class="card-body">
            <h6 class="fw-bold" id="leccTitulo"></h6>
            <p class="text-cl-muted small mb-0" id="leccDesc"></p>
            <?php if($matricula): ?>
            <div class="mt-2 d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" id="btnCompletado" onclick="marcarCompletado()">☐ Marcar como vista</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if($curso->prof_biografia): ?>
    <div class="card mt-3"><div class="card-body">
        <h6 class="fw-bold mb-2">👤 Instructor</h6>
        <div class="d-flex align-items-start gap-3">
            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--cl-accent),var(--cl-accent3));display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;flex-shrink:0;"><?=strtoupper(substr($curso->usua_nombres??'',0,1))?></div>
            <div>
                <div class="fw-semibold"><?=htmlspecialchars($curso->usua_nombres.' '.$curso->usua_paterno)?></div>
                <div class="small text-accent mb-1"><?=htmlspecialchars($curso->prof_especialidad??'')?></div>
                <p class="small text-cl-muted mb-0"><?=htmlspecialchars($curso->prof_biografia??'')?></p>
            </div>
        </div>
    </div></div>
    <?php endif; ?>
</div>

<!-- Lista de lecciones -->
<div class="col-lg-4">
<div class="card">
<div class="card-header"><span class="fw-semibold">Contenido del curso</span>
<small class="text-cl-muted ms-2"><?=$totalLecc?> lecciones</small></div>
<div class="card-body p-0" style="max-height:70vh;overflow-y:auto;">
<?php foreach($secciones as $s): ?>
<div class="px-3 py-2" style="background:var(--cl-bg-card2);border-bottom:1px solid var(--cl-border);">
    <small class="fw-semibold text-cl-muted text-uppercase" style="font-size:.67rem;letter-spacing:.06em;"><?=htmlspecialchars($s->secc_nombre)?></small>
</div>
<?php foreach($lecBySecc[$s->secc_ide]??[] as $l):
    $done = isset($progreso[$l->lecc_ide]) && $progreso[$l->lecc_ide]; ?>
<div class="leccion-item px-3 py-2 d-flex align-items-center gap-2 <?=$done?'completada':''?>" id="li-<?=$l->lecc_ide?>"
    onclick="<?=(!$matricula&&!$l->lecc_es_preview)?'alertar(\'Inscríbete para ver esta lección.\',\'alert alert-warning\')':("reproducir(".$l->lecc_ide.",'".$l->lecc_tipo."',embedUrl('".addslashes($l->lecc_url??'')."'),'".addslashes($l->lecc_titulo)."','".addslashes($l->lecc_descripcion??'')."')")?>">
    <div style="width:20px;text-align:center;flex-shrink:0;">
        <?php if($done): ?>
        <span style="color:var(--cl-success);">✓</span>
        <?php else: ?>
        <span class="tipo-badge tipo-<?=$l->lecc_tipo?>" style="font-size:.55rem;"><?=substr($l->lecc_tipo,0,1)?></span>
        <?php endif; ?>
    </div>
    <div class="flex-grow-1" style="min-width:0;">
        <div class="small fw-medium" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?=htmlspecialchars($l->lecc_titulo)?></div>
        <div style="font-size:.65rem;color:var(--cl-muted);"><?=$l->lecc_duracion?$l->lecc_duracion.' min':''?><?=$l->lecc_es_preview?'<span style="color:var(--cl-accent2);"> &bull; Gratis</span>':''?></div>
    </div>
    <?php if(!$matricula&&!$l->lecc_es_preview): ?>
    <i class="ti-lock" style="font-size:.75rem;color:var(--cl-muted);flex-shrink:0;"></i>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php endforeach; ?>
<?php if(empty($secciones)): ?><div class="text-center py-4 text-cl-muted small">Aún no hay lecciones.</div><?php endif; ?>
</div>
</div>
</div>
</div>

<script>
var leccActual = null;
function reproducir(ide, tipo, url, titulo, desc) {
    leccActual = ide;
    var area = document.getElementById('playerArea');
    if(tipo=='VIDEO' && url) {
        area.innerHTML = '<div class="video-wrapper"><iframe src="'+url+'" allowfullscreen></iframe></div>';
    } else if(tipo=='TEXTO') {
        area.innerHTML = '<div class="p-4" style="min-height:300px;"><p class="text-cl-muted">Leccion de tipo texto. Contenido cargado desde base de datos.</p></div>';
    } else if(tipo=='ARCHIVO' && url) {
        area.innerHTML = '<div class="p-4 text-center"><i class="ti-file fs-1 text-accent"></i><p class="mt-2">Archivo disponible</p><a href="'+url+'" target="_blank" class="btn btn-primary">Descargar / Ver</a></div>';
    } else {
        area.innerHTML = '<div class="p-4 text-center"><i class="ti-file fs-1 text-accent"></i><p class="mt-2 text-cl-muted">Recurso no disponible para preview.</p></div>';
    }
    document.getElementById('leccTitulo').innerHTML = titulo;
    document.getElementById('leccDesc').innerHTML = desc;
    document.getElementById('infoLeccion').style.display = '';
    // Marcar activa
    document.querySelectorAll('.leccion-item').forEach(el=>el.classList.remove('activa'));
    var li = document.getElementById('li-'+ide);
    if(li) li.classList.add('activa');
    // Actualizar botón completado
    var btn = document.getElementById('btnCompletado');
    if(btn) {
        var li2 = document.getElementById('li-'+ide);
        var done = li2 && li2.classList.contains('completada');
        btn.innerHTML = done ? '✓ Vista' : '☐ Marcar como vista';
        btn.className = done ? 'btn btn-sm btn-success' : 'btn btn-sm btn-outline-secondary';
    }
}
function marcarCompletado() {
    if(!leccActual) return;
    var li = document.getElementById('li-'+leccActual);
    var done = li && li.classList.contains('completada');
    $.post("<?=base_url('/mi-panel/marcar')?>",{lecc_ide:leccActual, completado:done?0:1},function(r){
        r=JSON.parse(r);
        if(r.ok) {
            if(li) { if(!done) li.classList.add('completada'); else li.classList.remove('completada'); }
            var btn=document.getElementById('btnCompletado');
            if(btn){btn.innerHTML=!done?'✓ Vista':'☐ Marcar como vista';btn.className=!done?'btn btn-sm btn-success':'btn btn-sm btn-outline-secondary';}
        }
    });
}
function matricularme(ide){
    $.post("<?=base_url('/mi-panel/matricular')?>",{curs_ide:ide},function(r){
        r=JSON.parse(r);
        if(r.ok){alertar(r.msg,'alert alert-success','ti-check');setTimeout(()=>cargarFuncion('/mi-panel/ver/'+ide,'Mi Panel','Ver Curso',''),1200);}
    });
}
// Auto-reproducir primera lección si hay matricula
<?php if($matricula && $primeraLecc && $primeraLecc->lecc_url): ?>
setTimeout(function(){reproducir(<?=$primeraLecc->lecc_ide?>,'<?=$primeraLecc->lecc_tipo?>',embedUrl('<?=addslashes($primeraLecc->lecc_url??'')?>'), '<?=addslashes($primeraLecc->lecc_titulo??'')?>', '<?=addslashes($primeraLecc->lecc_descripcion??'')?>')},300);
<?php endif; ?>
</script>
