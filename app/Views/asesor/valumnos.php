<!-- Navegación breadcrumb -->
<div class="d-flex align-items-center gap-2 mb-3" style="font-size:.85rem;">
    <a href="#" onclick="cargarFuncion('/asesor/cursos','Asesoría','Mis Cursos','')" class="text-cl-muted">
        <i class="ti-arrow-left me-1"></i>Volver a Cursos
    </a>
</div>

<!-- Cabecera del curso -->
<div class="card mb-4" style="border-left:4px solid var(--cl-accent2);">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-0"><?=htmlspecialchars($curso->curs_nombre)?></h5>
                <small class="text-cl-muted">
                    <span class="badge badge-nivel-<?=$curso->curs_nivel?> me-2"><?=$curso->curs_nivel?></span>
                    Profesor: <strong><?=htmlspecialchars($curso->prof_paterno.' '.$curso->prof_nombres)?></strong>
                </small>
            </div>
            <div class="text-end">
                <small class="text-cl-muted d-block"><?=count($alumnos)?> alumno(s) matriculado(s)</small>
                <?php
                    $sinAsesor = 0;
                    foreach($alumnos as $a) { if(!$a->grupo_ide) $sinAsesor++; }
                ?>
                <small style="color:#f59e0b;"><?=$sinAsesor?> sin asesor aún</small>
            </div>
        </div>
    </div>
</div>

<?php if(empty($alumnos)): ?>
<div class="card text-center py-5">
    <div class="card-body">
        <div style="font-size:2.5rem;margin-bottom:.75rem;">👤</div>
        <h5 class="fw-bold mb-1">No hay alumnos matriculados</h5>
        <p class="text-cl-muted mb-0">Este curso aún no tiene alumnos inscritos.</p>
    </div>
</div>
<?php else: ?>

<!-- Leyenda + botón Formar Grupo -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex gap-3" style="font-size:.8rem;">
        <span><span class="badge bg-success me-1">✓</span>Ya tiene asesor</span>
        <span><span class="badge bg-secondary me-1">·</span>Sin asesor</span>
    </div>
    <button class="btn btn-primary" onclick="abrirModalGrupo()" id="btnFormarGrupo" disabled>
        <i class="ti-layers me-1"></i>Formar Grupo
        <span id="contSelec" class="badge bg-light text-dark ms-1">0</span>
    </button>
</div>

<!-- Tabla de alumnos -->
<div class="card"><div class="card-body p-0">
<table class="table mb-0" id="tablaAlumnos">
<thead><tr>
    <th style="width:34px;">
        <input type="checkbox" id="chkTodos" title="Seleccionar todos sin asesor"
               onchange="toggleTodos(this)">
    </th>
    <th>Alumno</th>
    <th>Contacto</th>
    <th>Progreso</th>
    <th>Completado</th>
    <th>Estado Asesoría</th>
</tr></thead>
<tbody>
<?php foreach($alumnos as $a):
    $pct    = $a->total_lecc > 0 ? round(($a->lecc_hechas / $a->total_lecc) * 100) : 0;
    $tieneA = !empty($a->grupo_ide);
?>
<tr class="fila-alumno <?=$tieneA?'table-secondary':''?>">
    <td>
        <?php if(!$tieneA): ?>
        <input type="checkbox" class="chk-alumno" value="<?=$a->usua_ide?>"
               onchange="actualizarContador()">
        <?php else: ?>
        <span title="Ya tiene asesor" style="color:#aaa;">—</span>
        <?php endif; ?>
    </td>
    <td>
        <div class="d-flex align-items-center gap-2">
            <div style="width:32px;height:32px;border-radius:50%;
                        background:linear-gradient(135deg,var(--cl-accent),#06b6d4);
                        display:flex;align-items:center;justify-content:center;
                        font-size:.7rem;font-weight:700;flex-shrink:0;">
                <?=strtoupper(substr($a->usua_nombres,0,1))?>
            </div>
            <div>
                <div class="fw-medium" style="font-size:.9rem;">
                    <?=htmlspecialchars($a->usua_paterno.' '.$a->usua_materno.', '.$a->usua_nombres)?>
                </div>
            </div>
        </div>
    </td>
    <td>
        <small class="d-block text-cl-muted"><?=htmlspecialchars($a->usua_email??'')?></small>
        <small class="text-cl-muted"><?=htmlspecialchars($a->usua_celular??'')?></small>
    </td>
    <td style="min-width:140px;">
        <div class="d-flex align-items-center gap-2">
            <div style="flex:1;height:6px;background:rgba(255,255,255,.1);border-radius:99px;overflow:hidden;">
                <div style="width:<?=$pct?>%;height:100%;
                            background:<?=$pct>=80?'#10b981':($pct>=40?'#f59e0b':'#ef4444')?>;">
                </div>
            </div>
            <small style="font-size:.72rem;color:<?=$pct>=80?'#10b981':($pct>=40?'#f59e0b':'#ef4444')?>;">
                <?=$pct?>%
            </small>
        </div>
        <small class="text-cl-muted" style="font-size:.7rem;">
            <?=$a->lecc_hechas?>/<?=$a->total_lecc?> lecciones
        </small>
    </td>
    <td>
        <?php if($a->matr_completado): ?>
        <span class="badge bg-success">Completado</span>
        <?php else: ?>
        <span class="badge bg-secondary" style="font-size:.7rem;">En progreso</span>
        <?php endif; ?>
    </td>
    <td>
        <?php if($tieneA): ?>
        <div>
            <span class="badge bg-success mb-1">Asesorado</span>
            <small class="d-block text-cl-muted" style="font-size:.7rem;">
                <i class="ti-user me-1"></i><?=htmlspecialchars($a->asesor_nombre??'')?>
            </small>
        </div>
        <?php else: ?>
        <span class="badge bg-secondary">Sin asesor</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div>
<?php endif; ?>

<!-- ═══ MODAL: FORMAR GRUPO ═══ -->
<div class="cl-overlay" id="ov-formargrupo">
<div class="cl-modal cl-modal-lg">
    <div class="cl-modal-hdr">
        <h5><i class="ti-layers me-2"></i>Formar Grupo de Asesoría</h5>
        <button class="cl-modal-close" onclick="clCerrar('ov-formargrupo')">✕</button>
    </div>
    <div class="cl-modal-body">
        <input type="hidden" id="modalCursIde" value="<?=$curso->curs_ide?>">

        <!-- Información del grupo -->
        <div class="card mb-3" style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);">
            <div class="card-body py-2 px-3" style="font-size:.82rem;">
                <strong>Curso:</strong> <?=htmlspecialchars($curso->curs_nombre)?><br>
                <strong>Profesor:</strong> <?=htmlspecialchars($curso->prof_paterno.' '.$curso->prof_nombres)?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small text-cl-muted">Nombre del Grupo *</label>
            <input id="nombreGrupo" class="form-control"
                   placeholder="Ej: Grupo A – Refuerzo JS Semana 1"
                   maxlength="200">
            <small class="text-cl-muted">Los alumnos verán este nombre en el grupo.</small>
        </div>

        <!-- Lista de alumnos seleccionados -->
        <div>
            <label class="form-label small text-cl-muted">Alumnos seleccionados</label>
            <div id="listaSeleccionados" class="d-flex flex-wrap gap-2"
                 style="min-height:36px;padding:8px;background:rgba(255,255,255,.04);
                        border:1px solid rgba(255,255,255,.1);border-radius:.375rem;">
                <span class="text-cl-muted small" id="msgSinSelec">Ninguno seleccionado</span>
            </div>
        </div>
    </div>
    <div class="cl-modal-ftr">
        <button class="btn btn-secondary" onclick="clCerrar('ov-formargrupo')">Cancelar</button>
        <button class="btn btn-primary" onclick="guardarGrupo()">
            <i class="ti-layers me-1"></i>Crear Grupo
        </button>
    </div>
</div>
</div>

<!-- Dataset de nombres para el modal -->
<script>
var alumnosData = {
<?php foreach($alumnos as $a): if(!$a->grupo_ide): ?>
    <?=$a->usua_ide?>: "<?=addslashes(htmlspecialchars($a->usua_paterno.' '.$a->usua_nombres))?>",
<?php endif; endforeach; ?>
};

function actualizarContador() {
    var checks = document.querySelectorAll('.chk-alumno:checked');
    document.getElementById('contSelec').textContent = checks.length;
    document.getElementById('btnFormarGrupo').disabled = (checks.length === 0);
}

function toggleTodos(chkAll) {
    document.querySelectorAll('.chk-alumno').forEach(c => { c.checked = chkAll.checked; });
    actualizarContador();
}

function abrirModalGrupo() {
    var checks = document.querySelectorAll('.chk-alumno:checked');
    if (checks.length === 0) return;

    var lista = document.getElementById('listaSeleccionados');
    var msg   = document.getElementById('msgSinSelec');
    lista.innerHTML = '';
    msg.style.display = 'none';

    checks.forEach(c => {
        var uid  = c.value;
        var span = document.createElement('span');
        span.className = 'badge';
        span.style.cssText = 'background:rgba(124,58,237,.2);color:var(--cl-accent2);font-size:.78rem;padding:4px 10px;';
        span.textContent = alumnosData[uid] || ('Alumno #'+uid);
        lista.appendChild(span);
    });

    if (lista.children.length === 0) {
        msg.style.display = '';
    }

    clAbrir('ov-formargrupo');
}

function guardarGrupo() {
    var nombre = document.getElementById('nombreGrupo').value.trim();
    if (!nombre) { alertar('Escribe un nombre para el grupo.','alert alert-warning','ti-alert'); return; }

    var alumnos = [];
    document.querySelectorAll('.chk-alumno:checked').forEach(c => alumnos.push(c.value));
    if (alumnos.length === 0) { alertar('Selecciona al menos un alumno.','alert alert-warning','ti-alert'); return; }

    clCerrar('ov-formargrupo');
    openCargar();

    // Construir FormData con array de alumnos
    var fd = new FormData();
    fd.append('curs_ide',    document.getElementById('modalCursIde').value);
    fd.append('nombre_grupo', nombre);
    alumnos.forEach(uid => fd.append('alumnos[]', uid));

    $.ajax({
        url:  '<?=base_url('/asesor/formar-grupo')?>',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(r) {
            try { r = JSON.parse(r); } catch(e) {}
            closeCargar();
            if (r.ok) {
                alertar(r.msg, 'alert alert-success', 'ti-check');
                setTimeout(() => cargarFuncion(
                    '/asesor/alumnos/<?=$curso->curs_ide?>',
                    'Asesoría',
                    '<?=addslashes(htmlspecialchars($curso->curs_nombre))?>',
                    ''
                ), 1200);
            } else {
                alertar(r.msg, 'alert alert-danger', 'ti-close');
            }
        }
    });
}
</script>
