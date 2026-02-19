<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $base = base_url("public");
    $tipo = array("blurred", "polygon", "abstract");
    $t = $tipo[rand(0, 2)];
    $i = rand(1, 16);
    $bg = "background-image: url(" . $base . "/assets/premium/boxed-bg/$t/bg/$i.jpg)";

    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts [ OPTIONAL ] -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="<?= $base; ?>/assets/css/bootstrap.min.css">

    <!-- Nifty CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="<?= $base; ?>/assets/css/nifty.min.css">

    <!-- Demo purpose CSS [ DEMO ] -->
    <!--<link rel="stylesheet" href="<?= $base; ?>/assets/css/demo-purpose/demo-settings.min.css">-->

    <!-- fontawesome -->
    <link rel="stylesheet" href="<?= $base; ?>/fontawesome/css/all.css">

    <script src="<?= $base; ?>/assets/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="<?= $base; ?>/jquery/jquery-3.6.2.js"></script>

    <title>Monitoreo Regional</title>

    <style>
        .logoU {
            width: 25%;
        }

        .logoT {
            text-align: center;
            width: 100%;
        }

        .fondoT {
            margin-top: 2em;
            background-color: #fffc;
            height: 100%;
            border: 1px solid #25476a;
            border-left: 6px solid #25476a;
            border-radius: 7px;
        }

        .borde {
            padding: 3em;
            padding-top: 4em;
            height: 100vh;
            background-color: #e6f1f5ee;
        }

        .imgFondo {
            /*object-fit: cover;
            object-position: left 20% top 25%;
            height: 100%;*/
            background-image: url(<?php echo $base . "/img/" . $fondo; ?>);
            background-position: 0px 0px;
            background-size: cover;
        }

        .inputLogin {
            /*background-color:transparent;*/
            border: 1px solid;
            border-left: 6px solid;
            color: black !important;
            font-weight: bold;
            /*border-radius:0px;*/
        }

        /*.inputLogin:hover{
            background-color:white;
            font-weight: 100;
        }*/
        .inputLogin:focus {
            background-color: white;
            font-weight: 100;
        }

        .text-coment {
            color: black;
            font-weight: bold;
        }
    </style>

</head>

<body class="imgFondo">

    <?php

    use App\Libraries\Componente; ?>
    <?php

    
    use PharIo\Manifest\ComponentElement;

    ?>
    <?php
    $logoUGEL = Componente::Img("", "public/img/" . $logo, "logoU");
    $logoTramitame = Componente::Img("", "public/img/tramitame.png", "logoT");
    $logo = Componente::Div($logoUGEL, "text-center", "");
    $sigla = Componente::H1($sigla, "text-center sigla text-primary");
    $login = new Componente;
    $login->agregar($logo);
    $login->agregar(Componente::Br());
    $login->agregar($sigla);
    $login->agregar(Componente::Br());
    $login->agregar(Componente::H4("SISTEMA DE MONITOREO REGIONAL", "text-center text-coment", ""));
    $login->agregar(Componente::Div("Ingresa al sistema", "text-center text-coment", ""));
    $login->agregar(Componente::Br());
    $login->agregar(Componente::Input("user", "text", "", "Usuario", "primary inputLogin"));
    $login->agregar(Componente::Input("pass", "password", "", "Contraseña", "primary inputLogin"));
    //$login->add(Componente::Hidden("inst", $inst_ide));
    $ingresar = Componente::Boton("", "submit", "primary mb-2", "fas fa-check", "Ingresa al sistema",);
    $consultar = Componente::Boton("", "button", "danger mb-2", "fas fa-search", "Consulta tu expediente");
    //$registrar = Componentes::Boton("btnRegistrar","button","secondary mb-2","fa-solid fa-angles-right","Registrate como usuario");
    $botones = new Componente;
    $botones->agregar($ingresar);
    //$botones->add($consultar);
    //$botones->add($registrar);
    $login->agregar($botones->get("div", "d-grid", ""));



    $login = $login->get("form", "borde", "action='" . base_url("logearse") . "' method='POST'");
    $colIzq = Componente::Col("col-lg-4 col-sm-6 col-12", $login);
    $colMid = Componente::Col("col-sm-6", "");
    $colDer = Componente::Col("col-sm-2 fondoT", $logoTramitame);
    $colDer = "";
    echo Componente::Row($colIzq . $colMid . $colDer, "");

    $mensajeError = Componente::Alert("Usuario y/o contraseña incorrectos", "danger");
    echo Componente::Modal("error", "Error al ingresar al sistema", $mensajeError, "", "");

    /*$registro = new SuperComponente;
    $registro->add(Componente::Col("col-sm-12", Componente::Alert("Buen día, complete la información solicitada para registrarse como nuevo usuario dentro del sistema y realice sus trámites usando las opciones que hemos creado especialmente para usted.<br><b>
        Una vez creado su cuenta en el sistema usted podrá realizar tramites y hacer su seguimiento y si existiesen notificaciones realizadas podrá visualizarlas</b>", "info text-center fs-5")));
    $registro->add(Componente::Hidden("inst", $inst_ide));
    $registro->add(Componente::Col("col-sm-3", Componente::Input("dni", "text", "", "DNI", "primary")));
    $registro->add(Componente::Col("col-sm-4", Componente::Input("nombres", "text", "", "NOMBRES", "primary text-uppercase")));
    $registro->add(Componente::Col("col-sm-5", Componente::Input("apellidos", "text", "", "APELLIDOS", "primary text-uppercase")));
    $registro->add(Componente::Col("col-sm-2", Componente::Input("cell", "number", "", "CELULAR", "primary")));
    $registro->add(Componente::Col("col-sm-5", Componente::Input("email1", "email", "", "CORREO ELECTRONICO", "primary")));
    $registro->add(Componente::Col("col-sm-5", Componente::Input("email2", "email", "", "CONFIRMA TU CORREO ELECTRONICO", "primary")));
    //$registro->add(Componentes::Col("col-sm-2",""));
    $registro->add(Componentes::Col("col-sm-12", Componentes::Alert("<b>IMPORTANTE:</b> La contraseña de acceso al sistema se enviará al correo electrónico consignado", "danger fs-5")));
    $registro->add(Componentes::Div(Componentes::Icono("spinner-grow spinner-grow-sm text-primary") . " . . . Registrando . . . " . Componentes::Icono("spinner-grow spinner-grow-sm text-primary"), "text-center fs-5", "divLoading"));
    $registro->add(Componentes::Div("", "text-center fs-5", "divRespuesta"));
    $registro->add(Componentes::Col("col-sm-12 d-grid", Componentes::Boton("btnRegistro", "submit", "primary", "fas fa-check", "Registrarme")));
    $registro->add(Componentes::Div("", "", "registro-errores"));
    echo Componente::Modal("modalRegistro", "CREA TU CUENTA DE USUARIO", $registro->get("form", "row", "id='formRegistro' action='" . base_url("/login/registrar") . "' method='POST'"), "", "modal-xl");
        */
    /*$procesar = new SuperComponente;
        $procesar->add(Componentes::Col("col-sm-2","123"));
        $procesar->add(Componentes::Col("col-sm-2","Procesando"));
        echo Componentes::Modal("modalLoading","PROCESANDO",$procesar->get("div","row",""),"","modal-xl");*/

    if ($error == "true") {
        echo Componente::Js("
                $(document).ready(function(){
                    $('#error').modal('show');
                });
            ");
    }
    /*echo Componente::Js("
            $(document).ready(function(){
                $('#divLoading').css('display','none');
                $('#btnRegistrar').click(function(){
                    $('#modalRegistro').modal('show');
                });
                $('#formRegistro').submit(function(e){
                    e.preventDefault();
                    $('#btnRegistro').css('display','none');
                    $('#divLoading').css('display','inline');
                    $.post('" . site_url('login/registro') . "',$(this).serialize(),function(data){
                        data=JSON.parse(data);
                        $('#registro-errores').html(data.errores);
                        if(data.errorVali=='si'){
                            $('#btnRegistro').css('display','inline');
                            $('#divLoading').css('display','none');
                        }
                        else{
                            $('#divRespuesta').attr('class','');
                            if(data.email=='enviado'){
                                $('#divLoading').css('display','none');
                                $('#divRespuesta').addClass('alert alert-success fs-5');
                                $('#divRespuesta').html('<b>REGISTRO SATISFACTORIO</b><br>Se ha enviado la contraseña al correo: '+$('#email1').val());
                            }
                            else{
                                $('#btnRegistro').css('display','inline');
                                $('#divLoading').css('display','none');
                                $('#divRespuesta').addClass('alert alert-danger fs-5');
                                $('#divRespuesta').html('<b>OCURRIO UN ERROR</b><br>Al parecer hay un inconveniente con el correo: '+$('#email1').val()+' intente con otro correo electrónico');
                            }
                        }
                    });
                });
            });
        ");*/

    ?>
</body>

</html>