<?php
// Renderizado como fragmento HTML para inyectar en el modal del admin
$perf   = $usuario->usua_perf_ide;
$perfNombre = $usuario->perf_nombre ?? 'Usuario';

$colores = [1=>'#06b6d4',2=>'#10b981',3=>'#f59e0b',4=>'#a78bfa'];
$color   = $colores[$perf] ?? '#94a3b8';

// Datos institución educativa del alumno
$sinColegio = $aluInfo->alui_sin_colegio ?? 0;
$ugelNombre = $aluInfo->ugel_nombre      ?? null;
$ugelCiudad = $aluInfo->ugel_ciudad      ?? null;
$coleNombre = $aluInfo->cole_nombre      ?? $aluInfo->alui_cole_texto ?? null;
?>

<!-- Cabecera del perfil -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div style="width:56px;height:56px;border-radius:50%;flex-shrink:0;
                background:linear-gradient(135deg,<?=$color?>,<?=$color?>88);
                display:flex;align-items:center;justify-content:center;
                font-size:1.5rem;font-weight:700;color:#fff;">
        <?=strtoupper(substr($usuario->usua_nombres??'?',0,1))?>
    </div>
    <div>
        <h5 class="fw-bold mb-0">
            <?=htmlspecialchars($usuario->usua_paterno.' '.$usuario->usua_materno.', '.$usuario->usua_nombres)?>
        </h5>
        <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
            <span class="badge" style="background:<?=$color?>22;color:<?=$color?>;font-size:.72rem;">
                <?=htmlspecialchars($perfNombre)?>
            </span>
            <code style="font-size:.75rem;color:var(--cl-accent2);"><?=$usuario->usua_user?></code>
            <span class="badge bg-<?=$usuario->usua_esta_ide==1?'success':'danger'?>" style="font-size:.68rem;">
                <?=$usuario->usua_esta_ide==1?'ACTIVO':'INACTIVO'?>
            </span>
        </div>
    </div>
</div>

<!-- Estadísticas rápidas (solo alumno y profesor) -->
<?php if(!empty($stats)): ?>
<div class="row g-2 mb-4">
    <?php if($perf == 1): ?>
    <div class="col-4">
        <div class="p-2 rounded text-center" style="background:rgba(6,182,212,.08);border:1px solid rgba(6,182,212,.15);">
            <div class="fw-bold" style="font-size:1.2rem;color:#06b6d4;"><?=$stats['cursos']??0?></div>
            <small class="text-cl-muted" style="font-size:.68rem;">Cursos</small>
        </div>
    </div>
    <div class="col-4">
        <div class="p-2 rounded text-center" style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.15);">
            <div class="fw-bold" style="font-size:1.2rem;color:#10b981;"><?=$stats['completados']??0?></div>
            <small class="text-cl-muted" style="font-size:.68rem;">Completados</small>
        </div>
    </div>
    <div class="col-4">
        <div class="p-2 rounded text-center" style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.15);">
            <div class="fw-bold" style="font-size:1.2rem;color:var(--cl-accent2);"><?=$stats['lecciones']??0?></div>
            <small class="text-cl-muted" style="font-size:.68rem;">Lecciones</small>
        </div>
    </div>
    <?php elseif($perf == 2): ?>
    <div class="col-6">
        <div class="p-2 rounded text-center" style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.15);">
            <div class="fw-bold" style="font-size:1.2rem;color:#10b981;"><?=$stats['cursos']??0?></div>
            <small class="text-cl-muted" style="font-size:.68rem;">Cursos activos</small>
        </div>
    </div>
    <div class="col-6">
        <div class="p-2 rounded text-center" style="background:rgba(6,182,212,.08);border:1px solid rgba(6,182,212,.15);">
            <div class="fw-bold" style="font-size:1.2rem;color:#06b6d4;"><?=$stats['alumnos']??0?></div>
            <small class="text-cl-muted" style="font-size:.68rem;">Alumnos totales</small>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Datos personales -->
<div class="card mb-3"><div class="card-body py-2 px-3">
    <div class="fw-bold mb-2" style="font-size:.8rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.05em;">
        <i class="ti-user me-1"></i>Datos personales
    </div>
    <div class="row g-2" style="font-size:.83rem;">
        <?php if($usuario->usua_email): ?>
        <div class="col-sm-6">
            <span class="text-cl-muted">Email:</span>
            <span class="ms-1"><?=htmlspecialchars($usuario->usua_email)?></span>
        </div>
        <?php endif; ?>
        <?php if($usuario->usua_celular): ?>
        <div class="col-sm-6">
            <span class="text-cl-muted">Celular:</span>
            <span class="ms-1"><?=htmlspecialchars($usuario->usua_celular)?></span>
        </div>
        <?php endif; ?>
        <?php if($usuario->usua_dni): ?>
        <div class="col-sm-6">
            <span class="text-cl-muted">DNI:</span>
            <span class="ms-1"><?=htmlspecialchars($usuario->usua_dni)?></span>
        </div>
        <?php endif; ?>
        <?php if($usuario->usua_create_at): ?>
        <div class="col-sm-6">
            <span class="text-cl-muted">Registro:</span>
            <span class="ms-1"><?=date('d/m/Y', strtotime($usuario->usua_create_at))?></span>
        </div>
        <?php endif; ?>
    </div>
</div></div>

<!-- Institución educativa (solo alumno) -->
<?php if($perf == 1): ?>
<div class="card mb-3" style="border-left:3px solid <?=$sinColegio?'#f59e0b':'var(--cl-accent2)'?>;"><div class="card-body py-2 px-3">
    <div class="fw-bold mb-2" style="font-size:.8rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.05em;">
        <i class="ti-location-pin me-1"></i>Institución educativa
    </div>
    <?php if($sinColegio): ?>
        <div style="font-size:.85rem;color:#f59e0b;font-weight:600;">🎓 Alumno independiente</div>
        <small class="text-cl-muted">No asociado a ninguna institución educativa</small>
    <?php elseif($ugelNombre): ?>
        <div style="font-size:.85rem;font-weight:600;">🏫 <?=htmlspecialchars($coleNombre ?? 'Colegio no especificado')?></div>
        <small class="text-cl-muted">
            <i class="ti-location-pin me-1"></i><?=htmlspecialchars($ugelNombre)?><?=$ugelCiudad?' — '.$ugelCiudad:''?>
        </small>
    <?php else: ?>
        <span class="text-cl-muted" style="font-size:.82rem;">No registró institución educativa</span>
    <?php endif; ?>
</div></div>
<?php endif; ?>

<!-- Perfil académico del alumno -->
<?php if($perf == 1 && $extra): ?>
<?php
$tipos = ['ESTUDIANTE_COLEGIO'=>'Estudiante de colegio','ESTUDIANTE_SUPERIOR'=>'Estudiante universitario','PROFESIONAL'=>'Profesional','OTRO'=>'Otro'];
$tipo  = $extra->pale_tipo ?? 'OTRO';
?>
<?php if($extra->pale_institucion || $extra->pale_carrera || $extra->pale_descripcion): ?>
<div class="card mb-3"><div class="card-body py-2 px-3">
    <div class="fw-bold mb-2" style="font-size:.8rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.05em;">
        <i class="ti-book me-1"></i>Perfil académico
    </div>
    <div style="font-size:.83rem;">
        <div class="mb-1"><span class="text-cl-muted">Tipo:</span> <span class="ms-1"><?=$tipos[$tipo]??$tipo?></span></div>
        <?php if($extra->pale_institucion): ?>
        <div class="mb-1"><span class="text-cl-muted">Institución:</span> <span class="ms-1"><?=htmlspecialchars($extra->pale_institucion)?></span></div>
        <?php endif; ?>
        <?php if($extra->pale_carrera): ?>
        <div class="mb-1"><span class="text-cl-muted">Carrera/Grado:</span> <span class="ms-1"><?=htmlspecialchars($extra->pale_carrera)?></span></div>
        <?php endif; ?>
        <?php if($extra->pale_descripcion): ?>
        <div class="mt-2 p-2 rounded" style="background:rgba(255,255,255,.04);font-size:.8rem;color:var(--cl-muted);font-style:italic;">
            "<?=htmlspecialchars($extra->pale_descripcion)?>"
        </div>
        <?php endif; ?>
    </div>
</div></div>
<?php endif; ?>
<?php endif; ?>

<!-- Datos del profesor -->
<?php if($perf == 2 && $extra): ?>
<?php if($extra->prof_especialidad || $extra->prof_grado || $extra->prof_biografia): ?>
<div class="card mb-3"><div class="card-body py-2 px-3">
    <div class="fw-bold mb-2" style="font-size:.8rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.05em;">
        <i class="ti-graduation me-1"></i>Datos del profesor
    </div>
    <div style="font-size:.83rem;">
        <?php if($extra->prof_especialidad): ?><div class="mb-1"><span class="text-cl-muted">Especialidad:</span> <span class="ms-1"><?=htmlspecialchars($extra->prof_especialidad)?></span></div><?php endif; ?>
        <?php if($extra->prof_grado): ?>      <div class="mb-1"><span class="text-cl-muted">Grado:</span>        <span class="ms-1"><?=htmlspecialchars($extra->prof_grado)?></span></div><?php endif; ?>
        <?php if($extra->prof_area): ?>       <div class="mb-1"><span class="text-cl-muted">Área:</span>         <span class="ms-1"><?=htmlspecialchars($extra->prof_area)?></span></div><?php endif; ?>
        <?php if($extra->prof_web): ?>        <div class="mb-1"><span class="text-cl-muted">Web:</span>          <a href="<?=htmlspecialchars($extra->prof_web)?>" target="_blank" class="ms-1" style="color:var(--cl-accent2);"><?=htmlspecialchars($extra->prof_web)?></a></div><?php endif; ?>
        <?php if($extra->prof_biografia): ?>
        <div class="mt-2 p-2 rounded" style="background:rgba(255,255,255,.04);font-size:.8rem;color:var(--cl-muted);">
            <?=nl2br(htmlspecialchars($extra->prof_biografia))?>
        </div>
        <?php endif; ?>
    </div>
</div></div>
<?php endif; ?>
<?php endif; ?>
