<!-- ══════════════════════════════════════════════
     Modal: Ver Perfil (solo lectura)
     Incluir con: include __DIR__.'/../_modal_ver_perfil.php';
     Requiere la función verPerfil(ide, nombre) en la página
══════════════════════════════════════════════ -->
<div class="cl-overlay" id="ov-ver-perfil">
<div class="cl-modal cl-modal-lg">
    <div class="cl-modal-hdr">
        <h5 id="tituloModalPerfil">
            <i class="ti-user me-2" style="color:var(--cl-accent2);"></i>
            <span id="nombreModalPerfil">Perfil</span>
        </h5>
        <button class="cl-modal-close" onclick="clCerrar('ov-ver-perfil')">✕</button>
    </div>
    <div class="cl-modal-body" id="cuerpoModalPerfil" style="min-height:200px;">
        <div class="text-center py-5 text-cl-muted">
            <span class="spinner-border spinner-border-sm me-2"></span>Cargando perfil...
        </div>
    </div>
    <div class="cl-modal-ftr">
        <button class="btn btn-secondary" onclick="clCerrar('ov-ver-perfil')">Cerrar</button>
    </div>
</div>
</div>

<script>
function verPerfil(ide, nombre) {
    document.getElementById('nombreModalPerfil').textContent = nombre || 'Perfil';
    document.getElementById('cuerpoModalPerfil').innerHTML =
        '<div class="text-center py-5 text-cl-muted"><span class="spinner-border spinner-border-sm me-2"></span>Cargando...</div>';
    clAbrir('ov-ver-perfil');

    $.getJSON('<?=base_url('/perfil/ver')?>/' + ide, function(r) {
        if (r.ok) {
            document.getElementById('cuerpoModalPerfil').innerHTML = r.html;
            if (r.nombre) document.getElementById('nombreModalPerfil').textContent = r.nombre;
        } else {
            document.getElementById('cuerpoModalPerfil').innerHTML =
                '<div class="text-center py-4 text-cl-muted">No se pudo cargar el perfil.</div>';
        }
    }).fail(function() {
        document.getElementById('cuerpoModalPerfil').innerHTML =
            '<div class="text-center py-4 text-cl-muted">Error de conexión.</div>';
    });
}
</script>
