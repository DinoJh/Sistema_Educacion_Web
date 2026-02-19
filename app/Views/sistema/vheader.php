<!DOCTYPE html>
<html lang="es">

<head>
    <meta name="generator" content="Hugo 0.87.0" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta name="description" content="Nifty is a responsive admin dashboard template based on Bootstrap 5 framework. There are a lot of useful components.">
    <title>Web Application - <?php echo $system_name; ?></title>

    <!-- STYLESHEETS -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~--- -->

    <!-- Fonts [ OPTIONAL ] -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/bootstrap.min.css">

    <!-- Nifty CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/nifty.min.css">

    <!-- Nifty Demo Icons [ OPTIONAL ] -->
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/demo-purpose/demo-icons.min.css">

    <!-- Demo purpose CSS [ DEMO ] -->
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/demo-purpose/demo-settings.min.css">

    <!-- Demo tags[ DEMO ] -->
    <link rel="stylesheet" href="<?= base_url('public/assets/vendors/bootstrap5-tags/bootstrap5-tags.min.css') ?>">

    <script src="<?= base_url('public/assets/vendors/tagin/tagin.min.js') ?>" defer></script>

    <!-- Bootstrap 5 tags and initialize [ SAMPLE ] -->
    <script src="<?= base_url('public/assets/pages/forms-tags.js') ?>" defer></script>

    <!-- Chart Js -->
    <script src="<?= base_url('public/assets/vendors/chart.js/chart.min.js') ?>" defer></script>
    <!-- SweetAlert -->


    <style>
        .hd--expanded .content__header.overlapping+.content__boxed {
            z-index: auto !important;
        }

        .bold {
            font-weight: 500;
            font-size: 13px;
        }

        .home {
            text-decoration: none;
            color: white
        }

        .home:hover {
            text-decoration: underline;
            color: white
        }

        /******************************/
        /*.form-floating>.form-control:focus, .form-floating>.form-control:not(:placeholder-shown){
            padding: 0.7rem 0.5rem 0.3rem 0.5rem;
        }
        .form-floating>.form-control{
            padding-top: 0px;
        }
        .form-floating>.form-control, .form-floating>.form-select {
            height: auto;
            line-height: 0px;
        }
        .form-floating>.form-control {
            padding: 0.5rem 0.5rem;
        }
        .form-floating>label{
            padding: 0px 10px 0px 10px;
            height: auto;
            transform: scale(1) translateY(0.5rem) translateX(0.75rem);
            background-color: white;
            color: #1f3c5a;
            border-radius: 5px;
        }
        .form-floating>.form-control:focus~label, .form-floating>.form-control:not(:placeholder-shown)~label, .form-floating>.form-select~label{
            opacity: 1;
            transform: scale(1) translateY(-0.7rem) translateX(0.5rem);
            background-color: #1f3c5a;
            color: white;
        }
        /******************************/
    </style>

    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~---

    [ REQUIRED ]
    You must include this category in your project.


    [ OPTIONAL ]
    This is an optional plugin. You may choose to include it in your project.


    [ DEMO ]
    Used for demonstration purposes only. This category should NOT be included in your project.


    [ SAMPLE ]
    Here's a sample script that explains how to initialize plugins and/or components: This category should NOT be included in your project.


    Detailed information and more samples can be found in the documentation.

    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~--- -->
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/vendors/themify-icons/themify-icons.min.css">
    <link rel="stylesheet" href="<?= $base; ?>/assets/vendors/gridjs/gridjs.min.css">


    <script src="<?= $base; ?>/assets/vendors/gridjs/gridjs.umd.min.js" defer></script>

    <script>
        function cargarFuncion(url, modulo, nombre, descripcion) {
            openCargar();
            testing();
            param = {
                url: url
            };
            $.get("<?php echo base_url(); ?>" + url, param, function(data) {
                $("#moduloRol").html(modulo);
                $("#nombreRol").html(nombre);
                $("#descripcionRol").html(descripcion);
                $("#bodyApp").html(data);
                closeCargar();
            });
        }

        function openCargar(mensaje = "Estamos procesando su solicitud...") {
            $("#openCargarMensaje").html(mensaje);
            $("#openCargar").modal("show");
        }

        function closeCargar() {
            setTimeout(function() {
                $("#openCargar").modal("hide");
            }, 500);
        }

        function alertar(mensaje, clase = "alert alert-success", icono = "") {
            $("#alertarAlert").attr("class", clase);
            $("#alertarMensaje").html(mensaje);
            $("#alertarIcono").attr("class", icono + " fs-5");
            $("#alertar").modal("show");
        }

        function ajax(url, param, mifuncion, open = true) {
            //closeCargar();
            if (open) {
                openCargar("Procesando su solicitud...");
            }
            $.post("<?php echo base_url(); ?>" + url, param, function(data) {
                mifuncion(data);
                //closeCargar();
            });
        }

        function ajaxGet(url, param, mifuncion, open = true) {
            //closeCargar();
            if (open) {
                openCargar("Procesando su solicitud...");
            }
            $.get("<?php echo base_url(); ?>" + url, param, function(data) {
                mifuncion(data);
                //closeCargar();
            });
        }

        function ajaxFile(url, form_ide, mifuncion) {
            openCargar("Procesando su solicitud...");
            var formData = new FormData($("#" + form_ide)[0]);
            $.ajax({
                url: "<?php echo base_url(); ?>" + url,
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    mifuncion(data);
                    //closeCargar();
                }
            });
        }

        function cambiarClave() {
            openCargar("Cambiando de clave...");
            $("#cambiarClave").modal("hide");
            param = {
                anterior: $("#anterior").val(),
                nueva: $("#nueva").val(),
                repite: $("#repite").val(),
            }
            $.post("<?php echo base_url("/setpass") ?>", param, function(data) {
                data = JSON.parse(data);
                alertar(data.mensaje, data.clase, data.icono);
                closeCargar();
                $("#anterior").val("");
                $("#nueva").val("");
                $("#repite").val("");
            });
        }

        function testing() {
            $.post("<?php echo site_url("testing"); ?>", function(data) {
                if (data == "inactivo") {
                    alert("Su sesion ha caducado, ingrese nuevamente");
                    location.href = "<?php echo base_url("login"); ?>";
                }
            });
        }
        setInterval(testing, 60000 / 4);
    </script>

</head>

<body class="jumping">
    <!-- PAGE CONTAINER -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <div id="root" class="root mn--max hd--expanded">

        <!-- CONTENTS -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <section id="content" class="content">
            <div class="content__header content__boxed overlapping">
                <div class="content__wrap text-white">
                    <!-- Page title and information -->
                    <div class="fs-5">
                        <a href="<?php echo base_url("application"); ?>" class="home">
                            <i class="ti-desktop"></i> Inicio
                        </a>
                        <i class="ti-angle-right"></i>
                        <span class="mb-2" id="moduloRol">Dashboard</span>
                        <i class="ti-angle-right"></i>
                        <span class="mb-2" id="nombreRol">Roles asignados</span>
                    </div>
                    <p id="descripcionRol">Roles asignados para el usuario dentro de la entidad</p>
                    <!-- END : Page title and information -->
                </div>
            </div>
            <div class="content__boxed">
                <div class="content__wrap">
                    <div class="card border-2 border-primaryX">
                        <div class="card-body" id="bodyApp">