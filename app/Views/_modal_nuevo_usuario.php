<!-- ═══ OVERLAY: NUEVO USUARIO (CSS puro) ═══ -->
<div class="cl-overlay" id="ov-nuevousuario">
<div class="cl-modal cl-modal-lg">
  <div class="cl-modal-hdr"><h5>Nuevo Usuario</h5><button class="cl-modal-close" onclick="clCerrar('ov-nuevousuario')">✕</button></div>
  <div class="cl-modal-body">
    <input type="hidden" id="nuevoPerf" value="1">
    <div class="row g-3">
      <div class="col-md-4"><label class="form-label small text-cl-muted">Nombres *</label><input id="nuevoN" class="form-control" placeholder="Nombres"></div>
      <div class="col-md-4"><label class="form-label small text-cl-muted">Ap. Paterno</label><input id="nuevoPa" class="form-control" placeholder="Paterno"></div>
      <div class="col-md-4"><label class="form-label small text-cl-muted">Ap. Materno</label><input id="nuevoMa" class="form-control" placeholder="Materno"></div>
      <div class="col-md-4"><label class="form-label small text-cl-muted">DNI</label><input id="nuevoD" class="form-control" maxlength="8" placeholder="12345678"></div>
      <div class="col-md-4"><label class="form-label small text-cl-muted">Email *</label><input type="email" id="nuevoE" class="form-control" placeholder="correo@ejemplo.com"></div>
      <div class="col-md-4"><label class="form-label small text-cl-muted">Celular</label><input id="nuevoCel" class="form-control" placeholder="9XXXXXXXX"></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Usuario *</label><input id="nuevoU" class="form-control" placeholder="usuario123"></div>
      <div class="col-md-6"><label class="form-label small text-cl-muted">Contraseña *</label><input type="password" id="nuevoPas" class="form-control" placeholder="contraseña"></div>
    </div>
  </div>
  <div class="cl-modal-ftr">
    <button class="btn btn-secondary" onclick="clCerrar('ov-nuevousuario')">Cancelar</button>
    <button class="btn btn-primary" onclick="guardarUsuario()"><i class="ti-save me-1"></i>Guardar</button>
  </div>
</div></div>
