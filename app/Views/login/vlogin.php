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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base; ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base; ?>/assets/css/nifty.min.css">
    <link rel="stylesheet" href="<?= $base; ?>/fontawesome/css/all.css">
    <script src="<?= $base; ?>/assets/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="<?= $base; ?>/jquery/jquery-3.6.2.js"></script>
    <title>Monitoreo Regional</title>
    <style>
        .logoU { width: 25%; }
        .logoT { text-align: center; width: 100%; }
        .fondoT { margin-top:2em;background-color:#fffc;height:100%;border:1px solid #25476a;border-left:6px solid #25476a;border-radius:7px; }
        .borde { padding:3em;padding-top:4em;height:100vh;background-color:#e6f1f5ee; }
        .imgFondo { background-image:url(<?php echo $base."/img/".$fondo;?>);background-position:0px 0px;background-size:cover; }
        .inputLogin { border:1px solid;border-left:6px solid;color:black!important;font-weight:bold; }
        .inputLogin:focus { background-color:white;font-weight:100; }
        .text-coment { color:black;font-weight:bold; }

        /* ── Estilos solo para el modal de registro ── */
        .reg-ugel-card { border:1px solid #dee2e6;border-radius:8px;padding:8px 12px;cursor:pointer;font-size:.85rem;transition:border-color .15s,background .15s;margin-bottom:6px; }
        .reg-ugel-card:hover { border-color:#25476a;background:#f0f6fb; }
        .reg-ugel-card.activo { border-color:#25476a;border-left:4px solid #25476a;background:#e6f1f5;font-weight:600;color:#25476a; }
        .reg-paso-hdr { background:#e6f1f5;border-left:4px solid #25476a;border-radius:4px;padding:8px 12px;font-size:.82rem;color:#25476a;margin-bottom:16px; }
        .reg-dots { display:flex;gap:8px;justify-content:center;margin-bottom:16px; }
        .reg-dots .dot { width:10px;height:10px;border-radius:50%;background:#dee2e6;transition:background .2s; }
        .reg-dots .dot.done { background:#25476a; }
        .reg-dots .dot.active { background:#0d6efd;transform:scale(1.3); }
        .step-reg { display:none; }
        .step-reg.active { display:block; }
    </style>
</head>
<body class="imgFondo">

    <?php use App\Libraries\Componente; ?>

    <?php
    $logoUGEL      = Componente::Img("", "public/img/".$logo, "logoU");
    $logoTramitame = Componente::Img("", "public/img/tramitame.png", "logoT");
    $logo          = Componente::Div($logoUGEL, "text-center", "");
    $sigla         = Componente::H1($sigla, "text-center sigla text-primary");

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

    $ingresar = Componente::Boton("", "submit", "primary mb-2", "fas fa-check", "Ingresa al sistema");

    // Botón nuevo: registrarse como estudiante
    $registrar = '<button type="button" class="btn btn-outline-secondary w-100 mt-1"
        style="border-left:6px solid #6c757d;font-size:.85rem;"
        onclick="$(\'#modalRegistro\').modal(\'show\')">
        <i class="fas fa-user-plus me-1"></i> Crear cuenta de estudiante
    </button>';

    $botones = new Componente;
    $botones->agregar($ingresar);
    $botones->agregar($registrar);
    $login->agregar($botones->get("div", "d-grid", ""));

    $login  = $login->get("form", "borde", "action='".base_url("logearse")."' method='POST'");
    $colIzq = Componente::Col("col-lg-4 col-sm-6 col-12", $login);
    $colMid = Componente::Col("col-sm-6", "");
    echo Componente::Row($colIzq.$colMid, "");

    $mensajeError = Componente::Alert("Usuario y/o contraseña incorrectos", "danger");
    echo Componente::Modal("error", "Error al ingresar al sistema", $mensajeError, "", "");

    if ($error == "true") {
        echo Componente::Js("\$(document).ready(function(){ \$('#error').modal('show'); });");
    }
    if (isset($exito) && $exito == "true") {
        echo Componente::Js("\$(document).ready(function(){ \$('#modalExito').modal('show'); });");
    }
    ?>

    <!-- Modal: registro exitoso -->
    <div class="modal fade" id="modalExito" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center py-4">
            <div style="font-size:3rem;margin-bottom:.5rem;">✅</div>
            <h5 class="fw-bold text-success">¡Cuenta creada exitosamente!</h5>
            <p class="text-muted">Ya puedes ingresar con tu usuario y contraseña.</p>
            <button class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: registro de estudiante (3 pasos) -->
    <div class="modal fade" id="modalRegistro" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fw-bold">
                <i class="fas fa-user-plus me-2 text-primary"></i>Crear cuenta de estudiante
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

            <div class="reg-dots">
                <div class="dot active" id="rdot1"></div>
                <div class="dot"        id="rdot2"></div>
                <div class="dot"        id="rdot3"></div>
            </div>

            <div id="regAlerta" class="alert alert-danger py-2" style="display:none;font-size:.85rem;"></div>

            <!-- Paso 1: Datos personales -->
            <div class="step-reg active" id="rstep1">
                <div class="reg-paso-hdr"><strong>Paso 1 de 3</strong> — Datos personales</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Apellido paterno <span class="text-danger">*</span></label>
                        <input type="text" id="rPaterno" class="form-control inputLogin" placeholder="APELLIDO PATERNO" style="text-transform:uppercase;">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Apellido materno</label>
                        <input type="text" id="rMaterno" class="form-control inputLogin" placeholder="APELLIDO MATERNO" style="text-transform:uppercase;">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Nombres <span class="text-danger">*</span></label>
                        <input type="text" id="rNombres" class="form-control inputLogin" placeholder="TUS NOMBRE(S)" style="text-transform:uppercase;">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Correo electrónico</label>
                        <input type="email" id="rEmail" class="form-control inputLogin" placeholder="tucorreo@gmail.com">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Celular</label>
                        <input type="tel" id="rCelular" class="form-control inputLogin" placeholder="9XXXXXXXX">
                    </div>
                </div>
            </div>

            <!-- Paso 2: Usuario y contraseña -->
            <div class="step-reg" id="rstep2">
                <div class="reg-paso-hdr"><strong>Paso 2 de 3</strong> — Elige tu usuario y contraseña</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nombre de usuario <span class="text-danger">*</span></label>
                        <input type="text" id="rUser" class="form-control inputLogin" placeholder="Ej: juan.mamani" autocomplete="off" oninput="this.value=this.value.toLowerCase().replace(/\s/g,'')">
                        <div class="form-text">Solo letras minúsculas, números y puntos. Sin espacios.</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" id="rPass" class="form-control inputLogin" placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small">Confirmar contraseña <span class="text-danger">*</span></label>
                        <input type="password" id="rPass2" class="form-control inputLogin" placeholder="Repite tu contraseña" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <!-- Paso 3: UGEL y colegio -->
            <div class="step-reg" id="rstep3">
                <div class="reg-paso-hdr"><strong>Paso 3 de 3</strong> — Tu institución educativa</div>

                <div class="form-check mb-3 p-3 rounded" style="border:1px solid #ffc107;background:#fff8e1;">
                    <input class="form-check-input" type="checkbox" id="chkSinColegio" onchange="toggleSinColegio(this)">
                    <label class="form-check-label fw-semibold" for="chkSinColegio">
                        No soy alumno de ningún colegio
                        <div class="text-muted fw-normal" style="font-size:.78rem;">Estudiante independiente, universitario o egresado</div>
                    </label>
                </div>

                <div id="bloqueColegio">
                    <label class="form-label fw-semibold small">¿A qué UGEL pertenece tu colegio?</label>
                    <div class="row g-2 mb-3">
                        <?php foreach($ugeles as $u): ?>
                        <div class="col-sm-6">
                            <div class="reg-ugel-card" onclick="selUgel(this, <?=$u->ugel_ide?>)">
                                <strong><?=htmlspecialchars($u->ugel_nombre)?></strong>
                                <span class="text-muted ms-1" style="font-size:.75rem;">— <?=htmlspecialchars($u->ugel_ciudad??'')?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="rUgelIde" value="0">

                    <div id="bloqueSelectorColegio" style="display:none;">
                        <label class="form-label fw-semibold small">Selecciona tu colegio</label>
                        <select id="rColeIde" class="form-select inputLogin mb-2" onchange="toggleColeTexto(this.value)">
                            <option value="">— Elige tu colegio —</option>
                        </select>
                        <input type="hidden" id="rColeIdeVal" value="0">
                        <div id="bloqueColeTexto" style="display:none;">
                            <label class="form-label fw-semibold small">Escribe el nombre de tu colegio</label>
                            <input type="text" id="rColeTxt" class="form-control inputLogin" placeholder="Nombre completo de tu institución educativa">
                            <div class="form-text">Lo agregaremos pronto a la lista.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer justify-content-between">
            <button class="btn btn-secondary btn-sm" id="btnRegAtras" onclick="regAtras()" style="display:none;">← Atrás</button>
            <div class="d-flex gap-2 align-items-center">
                <span id="spinnerReg" style="display:none;font-size:.85rem;color:#25476a;">
                    <span class="spinner-border spinner-border-sm me-1"></span> Creando cuenta...
                </span>
                <button class="btn btn-primary" id="btnRegSiguiente" onclick="regSiguiente()">Siguiente →</button>
            </div>
        </div>
    </div></div></div>

    <script>
    var rPaso = 1;
    function regAlerta(msg){ var a=document.getElementById('regAlerta');a.innerHTML=msg;a.style.display='block';setTimeout(function(){a.style.display='none';},5000); }
    function regIrAPaso(n){
        document.querySelectorAll('.step-reg').forEach(function(el){el.classList.remove('active');});
        document.getElementById('rstep'+n).classList.add('active');
        for(var i=1;i<=3;i++){var d=document.getElementById('rdot'+i);d.className='dot';if(i<n)d.classList.add('done');else if(i===n)d.classList.add('active');}
        document.getElementById('btnRegAtras').style.display=(n>1)?'inline-block':'none';
        document.getElementById('btnRegSiguiente').textContent=(n<3)?'Siguiente →':'✔ Crear mi cuenta';
        document.getElementById('regAlerta').style.display='none';
        rPaso=n;
    }
    function regAtras(){ if(rPaso>1) regIrAPaso(rPaso-1); }
    function regSiguiente(){
        if(rPaso===1){
            if(!document.getElementById('rPaterno').value.trim()||!document.getElementById('rNombres').value.trim()){regAlerta('El apellido paterno y los nombres son obligatorios.');return;}
            var email=document.getElementById('rEmail').value.trim();
            if(email&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){regAlerta('El correo no es válido.');return;}
            regIrAPaso(2);
        } else if(rPaso===2){
            var user=document.getElementById('rUser').value.trim();
            var pass=document.getElementById('rPass').value;
            var pass2=document.getElementById('rPass2').value;
            if(!user){regAlerta('Escribe un nombre de usuario.');return;}
            if(!/^[a-z0-9._-]+$/.test(user)){regAlerta('El usuario solo puede tener letras minúsculas, números y puntos.');return;}
            if(!pass||pass.length<6){regAlerta('La contraseña debe tener al menos 6 caracteres.');return;}
            if(pass!==pass2){regAlerta('Las contraseñas no coinciden.');return;}
            regIrAPaso(3);
        } else if(rPaso===3){
            var sinCole=document.getElementById('chkSinColegio').checked;
            if(!sinCole){
                if(!parseInt(document.getElementById('rUgelIde').value)){regAlerta('Selecciona la UGEL de tu colegio.');return;}
                var cv=document.getElementById('rColeIde').value;
                if(!cv){regAlerta('Selecciona tu colegio de la lista.');return;}
                if(cv==='otro'&&!document.getElementById('rColeTxt').value.trim()){regAlerta('Escribe el nombre de tu colegio.');return;}
            }
            regEnviar();
        }
    }
    function regEnviar(){
        var cv=document.getElementById('rColeIde').value;
        var data={
            nombres:document.getElementById('rNombres').value.trim().toUpperCase(),
            paterno:document.getElementById('rPaterno').value.trim().toUpperCase(),
            materno:document.getElementById('rMaterno').value.trim().toUpperCase(),
            email:document.getElementById('rEmail').value.trim(),
            celular:document.getElementById('rCelular').value.trim(),
            user:document.getElementById('rUser').value.trim().toLowerCase(),
            pass:document.getElementById('rPass').value,
            pass2:document.getElementById('rPass2').value,
            sin_colegio:document.getElementById('chkSinColegio').checked?1:0,
            ugel_ide:parseInt(document.getElementById('rUgelIde').value||'0'),
            cole_ide:(cv&&cv!=='otro')?parseInt(cv):0,
            cole_texto:document.getElementById('rColeTxt').value.trim()
        };
        document.getElementById('btnRegSiguiente').style.display='none';
        document.getElementById('spinnerReg').style.display='inline';
        $.post('<?=base_url('/login/registrar')?>', data, function(r){
            document.getElementById('btnRegSiguiente').style.display='inline-block';
            document.getElementById('spinnerReg').style.display='none';
            try{r=JSON.parse(r);}catch(e){}
            if(r.ok){$('#modalRegistro').modal('hide');$('#modalExito').modal('show');regResetear();}
            else{regAlerta(r.msg||'Ocurrió un error. Intenta de nuevo.');}
        }).fail(function(){
            document.getElementById('btnRegSiguiente').style.display='inline-block';
            document.getElementById('spinnerReg').style.display='none';
            regAlerta('Error de conexión. Intenta de nuevo.');
        });
    }
    function regResetear(){
        ['rPaterno','rMaterno','rNombres','rEmail','rCelular','rUser','rPass','rPass2','rColeTxt'].forEach(function(id){var el=document.getElementById(id);if(el)el.value='';});
        document.getElementById('rUgelIde').value='0';
        document.getElementById('rColeIdeVal').value='0';
        document.getElementById('chkSinColegio').checked=false;
        document.getElementById('bloqueColegio').style.display='';
        document.getElementById('bloqueSelectorColegio').style.display='none';
        document.getElementById('bloqueColeTexto').style.display='none';
        document.querySelectorAll('.reg-ugel-card').forEach(function(c){c.classList.remove('activo');});
        regIrAPaso(1);
    }
    function selUgel(el,ugelIde){
        document.querySelectorAll('.reg-ugel-card').forEach(function(c){c.classList.remove('activo');});
        el.classList.add('activo');
        document.getElementById('rUgelIde').value=ugelIde;
        var sel=document.getElementById('rColeIde');
        sel.innerHTML='<option value="">⏳ Cargando...</option>';
        document.getElementById('bloqueSelectorColegio').style.display='block';
        document.getElementById('bloqueColeTexto').style.display='none';
        $.getJSON('<?=base_url('/login/colegios')?>/' + ugelIde, function(data){
            sel.innerHTML='<option value="">— Elige tu colegio —</option>';
            data.forEach(function(c){sel.innerHTML+='<option value="'+c.ide+'">'+c.nombre+'</option>';});
            sel.innerHTML+='<option value="otro">🔍 Mi colegio no está en la lista</option>';
        });
    }
    function toggleColeTexto(val){
        document.getElementById('rColeIdeVal').value=(val&&val!=='otro')?val:'0';
        document.getElementById('bloqueColeTexto').style.display=(val==='otro')?'block':'none';
    }
    function toggleSinColegio(chk){ document.getElementById('bloqueColegio').style.display=chk.checked?'none':''; }
    document.getElementById('modalRegistro').addEventListener('hidden.bs.modal',function(){regResetear();});
    </script>

</body>
</html>
