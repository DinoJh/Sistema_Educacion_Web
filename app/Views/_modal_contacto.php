<!-- ══════════════════════════════════════════════════
     Modal #contacto — Contactar al Administrador
     Incluir en vheader.php justo antes de </body>
     Llamado desde vmenu.php con: $('#contacto').modal('show')
══════════════════════════════════════════════════ -->
<div class="modal fade" id="contacto" tabindex="-1" aria-labelledby="contactoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:var(--cl-bg-card);border:1px solid var(--cl-border);border-radius:12px;">

      <!-- Header -->
      <div class="modal-header" style="border-bottom:1px solid var(--cl-border);">
        <h5 class="modal-title fw-bold" id="contactoLabel">
          <i class="ti-comment-alt me-2" style="color:var(--cl-accent2);"></i>
          Contactar al Administrador
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <p class="text-cl-muted small mb-3">
          Puedes enviarnos tus dudas, quejas, reclamos o sugerencias.
          El equipo administrativo las revisará a la brevedad.
        </p>

        <!-- Tipo -->
        <div class="mb-3">
          <label class="form-label small text-cl-muted">Tipo de mensaje</label>
          <div class="d-flex gap-2 flex-wrap" id="tipoContacto">
            <button type="button" class="btn btn-sm tipo-btn active"
                    data-tipo="CONSULTA"
                    style="background:rgba(100,116,139,.2);color:#94a3b8;border:1px solid rgba(255,255,255,.1);"
                    onclick="selTipo(this,'CONSULTA')">💬 Consulta</button>
            <button type="button" class="btn btn-sm tipo-btn"
                    data-tipo="QUEJA"
                    style="background:rgba(239,68,68,.08);color:#ef4444;border:1px solid rgba(239,68,68,.2);"
                    onclick="selTipo(this,'QUEJA')">⚠️ Queja</button>
            <button type="button" class="btn btn-sm tipo-btn"
                    data-tipo="RECLAMO"
                    style="background:rgba(245,158,11,.08);color:#f59e0b;border:1px solid rgba(245,158,11,.2);"
                    onclick="selTipo(this,'RECLAMO')">🔔 Reclamo</button>
            <button type="button" class="btn btn-sm tipo-btn"
                    data-tipo="SUGERENCIA"
                    style="background:rgba(6,182,212,.08);color:#06b6d4;border:1px solid rgba(6,182,212,.2);"
                    onclick="selTipo(this,'SUGERENCIA')">💡 Sugerencia</button>
          </div>
          <input type="hidden" id="cont_tipo_val" value="CONSULTA">
        </div>

        <!-- Asunto -->
        <div class="mb-3">
          <label class="form-label small text-cl-muted">Asunto <span class="text-danger">*</span></label>
          <input type="text" id="cont_asunto" class="form-control"
                 placeholder="Describe brevemente el motivo" maxlength="200">
        </div>

        <!-- Mensaje -->
        <div class="mb-1">
          <label class="form-label small text-cl-muted">Mensaje <span class="text-danger">*</span></label>
          <textarea id="cont_mensaje" class="form-control" rows="4"
                    placeholder="Explica tu caso con el mayor detalle posible..."></textarea>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer" style="border-top:1px solid var(--cl-border);">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="enviarContacto()">
          <i class="ti-arrow-right me-1"></i>Enviar mensaje
        </button>
      </div>

    </div>
  </div>
</div>

<script>
function selTipo(btn, tipo) {
    document.querySelectorAll('.tipo-btn').forEach(b => b.style.fontWeight = '');
    btn.style.fontWeight = '700';
    document.getElementById('cont_tipo_val').value = tipo;
}

function enviarContacto() {
    var asunto  = document.getElementById('cont_asunto').value.trim();
    var mensaje = document.getElementById('cont_mensaje').value.trim();
    var tipo    = document.getElementById('cont_tipo_val').value;

    if (!asunto || !mensaje) {
        alert('Completa el asunto y el mensaje antes de enviar.');
        return;
    }

    var btn = document.querySelector('#contacto .modal-footer .btn-primary');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando...';

    $.post(window.baseUrl + '/contacto/enviar', {
        tipo:    tipo,
        asunto:  asunto,
        mensaje: mensaje
    }, function(r) {
        try { r = JSON.parse(r); } catch(e) {}
        btn.disabled = false;
        btn.innerHTML = '<i class="ti-arrow-right me-1"></i>Enviar mensaje';

        if (r.ok) {
            $('#contacto').modal('hide');
            // Limpiar formulario
            document.getElementById('cont_asunto').value  = '';
            document.getElementById('cont_mensaje').value = '';
            document.getElementById('cont_tipo_val').value = 'CONSULTA';
            // Mostrar alerta del sistema
            if (typeof alertar !== 'undefined') {
                alertar(r.msg, 'alert alert-success', 'ti-check');
            } else {
                alert(r.msg);
            }
        } else {
            alert(r.msg || 'Error al enviar. Intenta de nuevo.');
        }
    }).fail(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti-arrow-right me-1"></i>Enviar mensaje';
        alert('Error de conexión. Intenta de nuevo.');
    });
}
</script>
