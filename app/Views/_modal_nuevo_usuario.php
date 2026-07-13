<!-- ═══ OVERLAY: NUEVO USUARIO ═══ -->
<div class="cl-overlay" id="ov-nuevousuario">
<div class="cl-modal cl-modal-lg">
  <div class="cl-modal-hdr">
    <h5><i class="ti-user me-2"></i>Nuevo Usuario</h5>
    <button class="cl-modal-close" onclick="clCerrar('ov-nuevousuario')">✕</button>
  </div>
  <div class="cl-modal-body">
    <input type="hidden" id="nuevoPerf" value="1">

    <!-- Aviso para alumnos: el usuario es el DNI -->
    <div id="avisoAlumno" class="mb-3 p-2 rounded" style="background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);font-size:.78rem;color:var(--cl-accent2);">
        <i class="ti-info-alt me-1"></i>
        Para <strong>alumnos</strong>: el nombre de usuario se genera automáticamente a partir del DNI.
        Si no tiene DNI, escribe un usuario manualmente.
    </div>

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label small text-cl-muted">Nombres *</label>
        <input id="nuevoN" class="form-control" placeholder="Nombres">
      </div>
      <div class="col-md-4">
        <label class="form-label small text-cl-muted">Ap. Paterno</label>
        <input id="nuevoPa" class="form-control" placeholder="Paterno">
      </div>
      <div class="col-md-4">
        <label class="form-label small text-cl-muted">Ap. Materno</label>
        <input id="nuevoMa" class="form-control" placeholder="Materno">
      </div>

      <!-- DNI: al escribirlo auto-rellena el usuario -->
      <div class="col-md-4">
        <label class="form-label small text-cl-muted">DNI <span class="text-danger">*</span></label>
        <input id="nuevoD" class="form-control" maxlength="8" placeholder="12345678"
               oninput="autoDniUser()">
      </div>
      <div class="col-md-4">
        <label class="form-label small text-cl-muted">Email</label>
        <input type="email" id="nuevoE" class="form-control" placeholder="correo@ejemplo.com">
      </div>
      <div class="col-md-4">
        <label class="form-label small text-cl-muted">Celular</label>
        <input id="nuevoCel" class="form-control" placeholder="9XXXXXXXX">
      </div>

      <div class="col-md-6">
        <label class="form-label small text-cl-muted">Usuario *</label>
        <input id="nuevoU" class="form-control" placeholder="Se genera del DNI automáticamente">
      </div>
      <div class="col-md-6">
        <label class="form-label small text-cl-muted">Contraseña *</label>
        <input type="text" id="nuevoPas" class="form-control" placeholder="Contraseña inicial">
        <small class="text-cl-muted" style="font-size:.7rem;">El alumno la puede cambiar después.</small>
      </div>
    </div>

    <!-- Opción de enviar correo de bienvenida (solo para alumnos) -->
    <div id="bloqueEmail" class="mt-3 p-3 rounded" style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);">
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" id="chkEnviarEmail" checked>
            <label class="form-check-label fw-semibold" for="chkEnviarEmail" style="font-size:.85rem;color:#10b981;">
                <i class="ti-email me-1"></i>Enviar correo de bienvenida con instrucciones de acceso
            </label>
        </div>
        <small class="text-cl-muted d-block mt-1" style="font-size:.72rem;">
            Se enviará al email ingresado arriba con su usuario, contraseña y enlace directo al sistema.
        </small>
    </div>
  </div>
  <div class="cl-modal-ftr">
    <button class="btn btn-secondary" onclick="clCerrar('ov-nuevousuario')">Cancelar</button>
    <button class="btn btn-primary" onclick="guardarUsuario()">
        <i class="ti-save me-1"></i>Guardar
    </button>
  </div>
</div>
</div>

<script>
// Auto-rellenar usuario con DNI cuando se trata de un alumno (perf=1)
function autoDniUser() {
    var perf = document.getElementById('nuevoPerf').value;
    var dni  = document.getElementById('nuevoD').value.trim();
    if (perf == 1) {
        document.getElementById('nuevoU').value = dni;
    }
}

// Mostrar/ocultar bloque de email y aviso según perfil
function actualizarModalPerfil() {
    var perf     = document.getElementById('nuevoPerf').value;
    var aviso    = document.getElementById('avisoAlumno');
    var blqEmail = document.getElementById('bloqueEmail');
    aviso.style.display    = (perf == 1) ? '' : 'none';
    blqEmail.style.display = (perf == 1) ? '' : 'none';
    // Si cambia el perfil, limpia el usuario para que no quede el DNI de antes
    document.getElementById('nuevoU').placeholder =
        (perf == 1) ? 'Se genera del DNI automáticamente' : 'usuario123';
}

// Llamar al inicio y cuando cambie el perfil (desde las vistas que lo llaman)
document.addEventListener('DOMContentLoaded', actualizarModalPerfil);
var _nuevoPerf = document.getElementById('nuevoPerf');
if(_nuevoPerf) { _nuevoPerf.addEventListener('change', actualizarModalPerfil); }

// MutationObserver para detectar cambios al campo hidden nuevoPerf
(function() {
    var observer = new MutationObserver(actualizarModalPerfil);
    var el = document.getElementById('nuevoPerf');
    if(el) observer.observe(el, { attributes:true, attributeFilter:['value'] });
})();
</script>
