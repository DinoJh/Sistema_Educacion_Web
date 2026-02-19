<!DOCTYPE html>
<html lang="es">

<head>

    <?php
        $base=base_url("public");
        $tipo=array("blurred","polygon","abstract");
        $t=$tipo[rand(0,2)];
        $i=rand(1,16);
        $bg="background-image: url(http://localhost/plantillaciv4/public/assets/premium/boxed-bg/$t/bg/$i.jpg)";

    ?>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta name="description" content="The login page allows a user to gain access to an application by entering their username and password">
    <title>Login  - <?php echo $system_name; ?></title>

    <!-- STYLESHEETS -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~--- -->

    <!-- Fonts [ OPTIONAL ] -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="<?=$base;?>/assets/css/bootstrap.min.css">

    <!-- Nifty CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="<?=$base;?>/assets/css/nifty.min.css">

    <!-- Nifty Demo Icons [ OPTIONAL ] -->
    <link rel="stylesheet" href="<?=$base;?>/assets/css/demo-purpose/demo-icons.min.css">

    <!-- Demo purpose CSS [ DEMO ] -->
    <link rel="stylesheet" href="<?=$base;?>/assets/css/demo-purpose/demo-settings.min.css">

    <style>
        .card{
            background-color: #fffb;
        }
    </style>


</head>

<body class="" style="<?php echo $bg; ?>">

    <!-- PAGE CONTAINER -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <div id="root" class="root front-container">

        <!-- CONTENTS -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <section id="content" class="content">
            <div class="content__boxed w-100 min-vh-100 d-flex flex-column align-items-center justify-content-center">
                <div class="content__wrap">

                    <!-- Login card -->
                    <div class="card shadow-lg" style="border: 3px solid #25476a;">
                        <div class="card-body">

                            <div class="text-center">
                                <h1 class="h3"><?php echo $system_name; ?></h1>
                                <p>Iniciar sesión en su cuenta</p>
                            </div>

                            <div class="row text-center">
                                <div class="col-sm-12">
                                    <img 
                                        src="<?php echo base_url("public/img/".$logo); ?>" 
                                        alt=""
                                        height="100px"
                                    >  
                                </div>
                            </div>

                            <form 
                                method="post" 
                                class="mt-4" 
                                action="<?php echo base_url("logearse") ?>" 
                                autocomplete="off"
                            >

                                <div class="form-floating mb-1">
                                    <input 
                                        type="text" 
                                        class="form-control form-control-lg" 
                                        id="user" 
                                        placeholder="Usuario"
                                        name="user"
                                    >
                                    <label for="user" class="text-primary"><b>Usuario</b></label>
                                </div>

                                <div class="form-floating mb-1">
                                    <input 
                                        type="password" 
                                        class="form-control form-control-lg"
                                        id="pass" 
                                        name="pass"
                                        placeholder="Contraseña"
                                    >
                                    <label for="pass" class="text-primary"><b>Contraseña</b></label>
                                </div>

                                <?php if ($error=="true"): ?>
                                <div class="alert alert-danger">
                                    Usuario y/o contraseña incorrectos
                                </div>
                                <?php endif ?>

                                <div class="d-grid mt-3">
                                    <button class="btn btn-primary btn-lg" type="submit">Ingresar</button>
                                </div>
                            </form>

                            <!--
                            <div class="d-flex justify-content-between mt-4">
                                <a 
                                    href="./front-pages-password-reminder.html" 
                                    class="btn btn-outline-danger text-decoration-none"
                                >Recuperar mi clave ?</a>
                                &nbsp;
                                <a 
                                    href="./front-pages-register.html" 
                                    class="btn btn-outline-dark text-decoration-none"
                                >Crear nueva cuenta</a>
                            </div>
                            -->

                            <!--<div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                                <h5 class="m-0">Login with</h5>
                                <div class="ms-3">
                                    <a href="#" class="btn btn-sm btn-icon btn-hover btn-primary text-inherit">
                                        <i class="demo-psi-facebook fs-5"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-icon btn-hover btn-info text-inherit">
                                        <i class="demo-psi-twitter fs-5"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-icon btn-hover btn-danger text-inherit">
                                        <i class="demo-psi-google-plus fs-5"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-icon btn-hover btn-warning text-inherit">
                                        <i class="demo-psi-instagram fs-5"></i>
                                    </a>
                                </div>
                            </div>
                            -->

                        </div>
                    </div>
                    <!-- END : Login card -->

                    <!-- Show the background images container -->
                    <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
                        <button class="btn btn-danger hstack gap-2" data-bs-toggle="offcanvas" data-bs-target="#_dm-boxedBgContent">
                            <i class=" demo-psi-photos fs-4"></i>
                            <span class="vr"></span>
                            Background image
                        </button>
                        <button class="btn btn-light" onclick="window.history.back()">Back</button>
                    </div>
                    <!-- END : Show the background images container -->

                </div>
            </div>
        </section>

        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <!-- END - CONTENTS -->
    </div>
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <!-- END - PAGE CONTAINER -->

    <!-- BOXED LAYOUT : BACKGROUND IMAGES CONTENT [ DEMO ] -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <div id="_dm-boxedBgContent" class="_dm-boxbg offcanvas offcanvas-bottom" data-bs-backdrop="false" data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-body p-4">

            <!-- Content Header -->
            <div class="offcanvas-header border-bottom p-0 pb-3">
                <div>
                    <h4 class="offcanvas-title">Imágenes de fondo</h4>
                    <span class="text-muted">Seleccione una imagen para reemplazar el color de fondo sólido</span>
                </div>
                <button type="button" class="btn-close btn-lg text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <!-- End - Content header -->

            <!-- Collection Of Images -->
            <div id="_dm-boxedBgContainer" class="row mt-3">

                <!-- Blurred Background Images -->
                <div class="col-lg-4">
                    <h5 class="mb-4">Borrosa</h5>
                    <div class="_dm-boxbg__img-container d-flex flex-wrap">
                        <?php for($i=1;$i<=16;$i++){?>
                        <a href="#" class="_dm-boxbg__thumb ratio ratio-16x9">
                            <img 
                                class="img-responsive" 
                                src="<?=$base; ?>/assets/premium/boxed-bg/blurred/thumbs/<?=$i;?>.jpg" 
                                alt="Background Image" 
                                loading="lazy"
                            >
                        </a>
                        <?php } ?>
                    </div>
                </div>
                <!-- End - Blurred Background Images -->

                <!-- Polygon Background Images -->
                <div class="col-lg-4">
                    <h5 class="mb-4">Polígono y geométrico</h5>
                    <div class="_dm-boxbg__img-container d-flex flex-wrap">
                        <?php for($i=1;$i<=16;$i++){?>
                        <a href="#" class="_dm-boxbg__thumb ratio ratio-16x9">
                            <img 
                                class="img-responsive" 
                                src="<?=$base; ?>/assets/premium/boxed-bg/polygon/thumbs/<?=$i;?>.jpg" 
                                alt="Background Image" 
                                loading="lazy"
                            >
                        </a>
                        <?php } ?>
                    </div>
                </div>
                <!-- End - Polygon Background Images -->

                <!-- Abstract Background Images -->
                <div class="col-lg-4">
                    <h5 class="mb-4">Abstracto</h5>
                    <div class="_dm-boxbg__img-container d-flex flex-wrap">
                        <?php for($i=1;$i<=16;$i++){?>
                        <a href="#" class="_dm-boxbg__thumb ratio ratio-16x9">
                            <img 
                                class="img-responsive" 
                                src="<?=$base; ?>/assets/premium/boxed-bg/abstract/thumbs/<?=$i;?>.jpg" 
                                alt="Background Image" 
                                loading="lazy"
                            >
                        </a>
                        <?php } ?>
                    </div>
                </div>
                <!-- End - Abstract Background Images -->
            </div>
            <!-- End - Collection Of Images -->
        </div>
    </div>
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <!-- END - BOXED LAYOUT : BACKGROUND IMAGES CONTENT [ DEMO ] -->
    <!-- JAVASCRIPTS -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->


    <!-- Popper JS [ OPTIONAL ] -->
    <script src="<?=$base;?>/assets/vendors/popperjs/popper.min.js" defer></script>

    <!-- Bootstrap JS [ OPTIONAL ] -->
    <script src="<?=$base;?>/assets/vendors/bootstrap/bootstrap.min.js" defer></script>

    <!-- Nifty JS [ OPTIONAL ] -->
    <script src="<?=$base;?>/assets/js/nifty.js" defer></script>

    <!-- Nifty Settings [ DEMO ] -->
    <script src="<?=$base;?>/assets/js/demo-purpose-only.js" defer></script>

</body>

</html>