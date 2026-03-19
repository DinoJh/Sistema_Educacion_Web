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
        area.innerHTML =
            '<div class="video-wrapper">' +
            '<iframe id="yt-frame" src="'+url+'" allowfullscreen ' +
            'style="background:#000;" ' +
            'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">' +
            '</iframe></div>';
    } else if(tipo=='TEXTO') {
        area.innerHTML = '<div class="p-4" style="min-height:260px;"><div class="alert alert-info"><i class="ti-text me-2"></i>Lección de tipo texto — contenido en base de datos.</div></div>';
    } else if(tipo=='ARCHIVO' && url) {
        area.innerHTML =
            '<div class="p-5 text-center" style="background:#0d0f18;border-radius:12px;">' +
            '<i class="ti-file fs-1 text-accent d-block mb-3"></i>' +
            '<p class="text-cl-muted mb-3">Archivo adjunto a esta lección</p>' +
            '<a href="'+url+'" target="_blank" class="btn btn-primary"><i class="ti-download me-2"></i>Descargar / Ver archivo</a>' +
            '</div>';
    } else {
        area.innerHTML =
            '<div class="p-5 text-center" style="background:#0d0f18;border-radius:12px;">' +
            '<i class="ti-alert fs-1 text-cl-muted d-block mb-3"></i>' +
            '<p class="text-cl-muted">Sin recurso disponible para esta lección.</p>' +
            '</div>';
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

// ── Reseñas ──────────────────────────────────────────────
function guardarResena() {
    var cal = parseInt(document.getElementById('rCalificacion').value);
    var com = document.getElementById('rComentario').value.trim();
    if(isNaN(cal)||cal<0||cal>20){alertar('La calificación debe ser entre 0 y 20.','alert alert-warning');return;}
    if(!com){alertar('Escribe un comentario.','alert alert-warning');return;}
    openCargar('Guardando reseña…');
    $.post("<?=base_url('/mi-panel/resena')?>",{curs_ide:<?=$curso->curs_ide??0?>,calificacion:cal,comentario:com},function(r){
        r=JSON.parse(r); closeCargar();
        if(r.ok){alertar(r.msg,'alert alert-success','ti-check');setTimeout(()=>cargarFuncion('/mi-panel/ver/<?=$curso->curs_ide??0?>','Mi Panel','Ver Curso',''),1300);}
        else alertar(r.msg,'alert alert-danger','ti-close');
    });
}
function actualizarEstrellas(val) {
    document.getElementById('rValLabel').innerHTML = val + '/20';
    document.getElementById('rCalificacion').value = val;
}
</script>

<!-- ═══════════════════ SECCIÓN DE RESEÑAS ═══════════════════ -->
<div class="mt-4">
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0">⭐ Reseñas del Curso</h5>
    <?php
    $totalResenas = count($resenas ?? []);
    $promedioResenas = $totalResenas > 0 ? round(array_sum(array_column((array)$resenas,'rese_calificacion')) / $totalResenas, 1) : null;
    ?>
    <?php if($totalResenas > 0): ?>
    <span class="badge" style="background:rgba(245,158,11,.15);color:#f59e0b;font-size:.85rem;padding:6px 14px;">
        Promedio: <?=$promedioResenas?>/20 &nbsp;·&nbsp; <?=$totalResenas?> reseña(s)
    </span>
    <?php endif; ?>
</div>

<?php if($matricula && $matricula->matr_completado): ?>
<!-- Formulario para dejar reseña (solo si completó el curso) -->
<div class="card mb-4" style="border:1px solid rgba(124,58,237,.3);">
<div class="card-body">
    <h6 class="fw-bold mb-3">
        <?=isset($miResena) && $miResena ? '✏️ Editar mi reseña' : '📝 Dejar mi reseña'?>
        <small class="text-cl-muted fw-normal ms-2" style="font-size:.75rem;">Solo disponible al completar el curso</small>
    </h6>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label small text-cl-muted">Calificación (0 – 20)</label>
            <div class="d-flex align-items-center gap-3">
                <input type="range" id="rCalificacion" class="form-range flex-grow-1" min="0" max="20" step="1"
                    value="<?=isset($miResena) && $miResena ? $miResena->rese_calificacion : 10?>"
                    oninput="actualizarEstrellas(this.value)">
                <span id="rValLabel" class="fw-bold" style="min-width:40px;color:var(--cl-accent2);font-size:1.1rem;">
                    <?=isset($miResena) && $miResena ? $miResena->rese_calificacion : 10?>/20
                </span>
            </div>
        </div>
        <div class="col-md-8">
            <label class="form-label small text-cl-muted">Comentario *</label>
            <textarea id="rComentario" class="form-control" rows="2"
                placeholder="¿Qué te pareció el curso? Sé honesto, tu opinión ayuda a todos."><?=isset($miResena) && $miResena ? htmlspecialchars($miResena->rese_comentario) : ''?></textarea>
        </div>
    </div>
    <div class="mt-3 text-end">
        <button class="btn btn-primary px-4" onclick="guardarResena()">
            <i class="ti-star me-1"></i><?=isset($miResena) && $miResena ? 'Actualizar reseña' : 'Publicar reseña'?>
        </button>
    </div>
</div>
</div>
<?php elseif($matricula && !$matricula->matr_completado): ?>
<div class="card mb-3" style="border:1px solid rgba(245,158,11,.2);">
<div class="card-body py-2 d-flex align-items-center gap-2">
    <i class="ti-info-alt" style="color:#f59e0b;"></i>
    <small class="text-cl-muted">Completa el curso para dejar tu reseña y calificación.</small>
</div>
</div>
<?php elseif(!$matricula): ?>
<div class="card mb-3" style="border:1px solid rgba(124,58,237,.2);">
<div class="card-body py-2 d-flex align-items-center gap-2">
    <i class="ti-info-alt" style="color:var(--cl-accent2);"></i>
    <small class="text-cl-muted">Inscríbete en el curso para poder dejar una reseña.</small>
</div>
</div>
<?php endif; ?>

<!-- Lista de reseñas públicas -->
<?php if(!empty($resenas)): ?>
<div class="d-flex flex-column gap-3">
<?php foreach($resenas as $r):
    $nota = $r->rese_calificacion;
    $notaColor = $nota >= 14 ? '#10b981' : ($nota >= 11 ? '#f59e0b' : '#ef4444');
    $esMia = ($r->rese_usua_ide == $session->usua_ide);
?>
<div class="card" style="<?=$esMia?'border:1px solid rgba(124,58,237,.35);':''?>">
<div class="card-body">
    <div class="d-flex align-items-start gap-3">
        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--cl-accent),#06b6d4);display:flex;align-items:center;justify-content:center;font-size:.9rem;font-weight:700;flex-shrink:0;color:#fff;">
            <?=strtoupper(substr($r->usua_nombres??'?',0,1))?>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <span class="fw-semibold small"><?=htmlspecialchars($r->usua_paterno.' '.$r->usua_nombres)?></span>
                <?php if($esMia): ?><span style="font-size:.65rem;background:rgba(124,58,237,.15);color:var(--cl-accent2);padding:2px 8px;border-radius:99px;">Tú</span><?php endif; ?>
                <span class="fw-bold ms-auto" style="color:<?=$notaColor?>;font-size:1rem;"><?=$nota?>/20</span>
                <small class="text-cl-muted"><?=date('d/m/Y', strtotime($r->rese_fecha))?></small>
            </div>
            <div class="progress mb-2" style="height:5px;background:rgba(255,255,255,.08);">
                <div class="progress-bar" style="width:<?=($nota/20)*100?>%;background:<?=$notaColor?>;"></div>
            </div>
            <p class="small text-cl-muted mb-0"><?=htmlspecialchars($r->rese_comentario??'')?></p>
        </div>
    </div>
</div>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<div class="card"><div class="card-body text-center py-4 text-cl-muted">
    <i class="ti-comment fs-2 mb-2 d-block"></i>
    <p class="mb-0 small">Aún no hay reseñas para este curso. ¡Sé el primero en opinar!</p>
</div></div>
<?php endif; ?>
</div>
