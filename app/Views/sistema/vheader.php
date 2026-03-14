<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CodePuno - <?php echo $system_name ?? 'Plataforma'; ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/nifty.min.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/demo-purpose/demo-icons.min.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/vendors/themify-icons/themify-icons.min.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/vendors/gridjs/gridjs.min.css">
    <script src="<?php echo $base; ?>/assets/vendors/gridjs/gridjs.umd.min.js" defer></script>
    <script src="<?php echo $base; ?>/assets/vendors/chart.js/chart.min.js" defer></script>

    <style>
        :root {
            --cl-bg-deep:    #0d0f18;
            --cl-bg-card:    #161826;
            --cl-bg-card2:   #1e2035;
            --cl-accent:     #7c3aed;
            --cl-accent2:    #a78bfa;
            --cl-accent3:    #06b6d4;
            --cl-success:    #10b981;
            --cl-warning:    #f59e0b;
            --cl-danger:     #ef4444;
            --cl-text:       #e2e8f0;
            --cl-muted:      #94a3b8;
            --cl-border:     rgba(255,255,255,0.07);
        }
        body { font-family: 'Inter', sans-serif; background: var(--cl-bg-deep) !important; color: var(--cl-text) !important; }
        .card { background: var(--cl-bg-card) !important; border: 1px solid var(--cl-border) !important; border-radius: 12px !important; }
        .card-header { background: var(--cl-bg-card2) !important; border-bottom: 1px solid var(--cl-border) !important; }
        .btn-primary, .bg-primary { background: var(--cl-accent) !important; border-color: var(--cl-accent) !important; }
        .btn-primary:hover { background: #6d28d9 !important; border-color: #6d28d9 !important; }
        .btn-outline-primary { border-color: var(--cl-accent) !important; color: var(--cl-accent2) !important; }
        .btn-outline-primary:hover { background: var(--cl-accent) !important; color: #fff !important; }
        .badge-nivel-BASICO     { background: #10b981 !important; }
        .badge-nivel-INTERMEDIO { background: #f59e0b !important; color:#000!important; }
        .badge-nivel-AVANZADO   { background: #ef4444 !important; }
        .curso-card { transition: transform .2s, box-shadow .2s; cursor: pointer; }
        .curso-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(124,58,237,.25) !important; }
        .curso-card .card-img-top { height: 160px; object-fit: cover; border-radius: 12px 12px 0 0; background: linear-gradient(135deg, #1e2035 0%, #0d0f18 100%); }
        .video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; background: #000; }
        .video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; border-radius: 12px; }
        .leccion-item { transition: background .15s; cursor: pointer; border-left: 3px solid transparent; }
        .leccion-item:hover { background: var(--cl-bg-card2) !important; border-left-color: var(--cl-accent); }
        .leccion-item.activa { background: var(--cl-bg-card2) !important; border-left-color: var(--cl-accent); }
        .leccion-item.completada { border-left-color: var(--cl-success); }
        .progress { height: 6px !important; border-radius: 99px; background: var(--cl-bg-card2) !important; }
        .progress-bar { background: linear-gradient(90deg, var(--cl-accent), var(--cl-accent3)) !important; }
        .form-control, .form-select { background: var(--cl-bg-card2) !important; border-color: var(--cl-border) !important; color: var(--cl-text) !important; }
        .form-control:focus, .form-select:focus { border-color: var(--cl-accent) !important; box-shadow: 0 0 0 3px rgba(124,58,237,.2) !important; }
        .table { color: var(--cl-text) !important; }
        .table thead th { background: var(--cl-bg-card2) !important; border-color: var(--cl-border) !important; color: var(--cl-muted); font-size: .75rem; letter-spacing: .05em; text-transform: uppercase; }
        .table td { border-color: var(--cl-border) !important; vertical-align: middle; }
        .table tbody tr:hover td { background: rgba(124,58,237,.06) !important; }
        .seccion-header { background: var(--cl-bg-card2); border-radius: 8px; padding: 10px 14px; margin-bottom: 4px; cursor: pointer; }
        .seccion-header:hover { background: rgba(124,58,237,.15); }
        .stat-card { background: linear-gradient(135deg, var(--cl-bg-card2), var(--cl-bg-card)); border: 1px solid var(--cl-border); border-radius: 12px; padding: 20px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .text-accent { color: var(--cl-accent2) !important; }
        .text-cl-muted { color: var(--cl-muted) !important; }
        .home { text-decoration: none; color: #fff; }
        .home:hover { text-decoration: underline; color: #fff; }
        .bold { font-weight: 600; font-size: 13px; }
        .tipo-badge { font-size: .65rem; font-weight: 600; letter-spacing: .06em; padding: 2px 8px; border-radius: 99px; }
        .tipo-VIDEO   { background: rgba(6,182,212,.15); color: #06b6d4; }
        .tipo-TEXTO   { background: rgba(16,185,129,.15); color: #10b981; }
        .tipo-ARCHIVO { background: rgba(245,158,11,.15); color: #f59e0b; }
        .tipo-QUIZ    { background: rgba(239,68,68,.15); color: #ef4444; }
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-track { background: var(--cl-bg-deep); } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
    </style>
</head>
<body class="jumping">
<div id="root" class="root mn--max hd--expanded">
    <section id="content" class="content">
        <div class="content__header content__boxed overlapping" style="background: linear-gradient(135deg, #1e1b4b 0%, #0d0f18 100%);">
            <div class="content__wrap text-white">
                <div class="fs-5">
                    <a href="<?php echo base_url('application'); ?>" class="home"><i class="ti-home"></i> Inicio</a>
                    <i class="ti-angle-right"></i>
                    <span id="moduloRol">Dashboard</span>
                    <i class="ti-angle-right"></i>
                    <span id="nombreRol">Bienvenido</span>
                </div>
                <p id="descripcionRol" class="mb-0 mt-1" style="color:#a78bfa; font-size:.85rem;">Plataforma de cursos de programación · Puno</p>
            </div>
        </div>
        <div class="content__boxed">
            <div class="content__wrap">
                <div class="card border-0">
                    <div class="card-body" id="bodyApp">

    <script>
        // ── Core AJAX helpers ──────────────────────────────────────
        function cargarFuncion(url, modulo, nombre, descripcion) {
            openCargar();
            testing();
            $.get("<?php echo rtrim(base_url(),'/'); ?>" + url, function(data) {
                $("#moduloRol").html(modulo);
                $("#nombreRol").html(nombre);
                $("#descripcionRol").html(descripcion);
                $("#bodyApp").html(data);
                closeCargar();
            });
        }
        function openCargar(msg = "Procesando solicitud…") {
            $("#openCargarMensaje").html(msg);
            $("#openCargar").modal("show");
        }
        function closeCargar() { setTimeout(function(){ $("#openCargar").modal("hide"); }, 400); }
        function alertar(msg, clase="alert alert-success", icono="") {
            $("#alertarAlert").attr("class", clase);
            $("#alertarMensaje").html(msg);
            $("#alertarIcono").attr("class", icono + " fs-5");
            $("#alertar").modal("show");
        }
        function ajax(url, param, fn, open=true) {
            if(open) openCargar();
            $.post("<?php echo rtrim(base_url(),'/'); ?>" + url, param, function(data){ fn(data); });
        }
        function ajaxGet(url, param, fn, open=true) {
            if(open) openCargar();
            $.get("<?php echo rtrim(base_url(),'/'); ?>" + url, param, function(data){ fn(data); });
        }
        function cambiarClave() {
            openCargar("Cambiando clave…");
            $("#cambiarClave").modal("hide");
            $.post("<?php echo base_url('/setpass'); ?>", {
                anterior:$("#anterior").val(), nueva:$("#nueva").val(), repite:$("#repite").val()
            }, function(data){
                data = JSON.parse(data);
                alertar(data.mensaje, data.clase, data.icono);
                closeCargar();
                $("#anterior,#nueva,#repite").val("");
            });
        }
        function testing() {
            $.post("<?php echo site_url('testing'); ?>", function(data){
                if(data=="inactivo"){ alert("Sesión expirada."); location.href="<?php echo base_url('login'); ?>"; }
            });
        }
        setInterval(testing, 15000);

        // ── Video embed helper ─────────────────────────────────────
        function embedUrl(url) {
            if(!url) return null;
            // YouTube watch
            let yt = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_\-]{11})/);
            if(yt) return 'https://www.youtube.com/embed/' + yt[1] + '?rel=0&modestbranding=1';
            // YouTube embed already
            if(url.includes('youtube.com/embed/')) return url;
            // Google Drive
            let gd = url.match(/drive\.google\.com\/file\/d\/([^\/]+)/);
            if(gd) return 'https://drive.google.com/file/d/' + gd[1] + '/preview';
            return url;
        }
    </script>
