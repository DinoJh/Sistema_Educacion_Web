<?php

namespace App\Libraries;

class Componente
{
    public $subComponentes;

    public function __construct()
    {
        $this->subComponentes = array();
    }
    public function agregar($componente)
    {
        $this->subComponentes[] = $componente;
    }
    public function get($etiqueta, $clase, $propiedades)
    {
        $componentes = "";
        foreach ($this->subComponentes as $reg) {
            $componentes .= $reg;
        }
        return "
            <$etiqueta class='$clase' $propiedades>
                $componentes
            </$etiqueta>
        ";
    }

    public static function Tabla($id, $head, $data, $limit, $search, $paginacion, $clase, $botones, $js)
    {
        $data = array(
            "id" => $id,
            "head" => $head,
            "clase" => $clase,
            "data" => $data,
            "botones" => $botones,
            "limit" => $limit,
            "search" => $search,
            "paginacion" => $paginacion,
            "js" => $js
        );
        return view('componente/tabla', $data);
    }
    public static function Modal($id, $titulo, $body, $botonok, $size)
    {
        $data = array(
            "id" => $id,
            "titulo" => $titulo,
            "body" => $body,
            "botonok" => $botonok,
            "size" => $size
        );
        return view('componente/modal', $data);
    }
    public static function Boton($id, $type, $clase, $icono, $txt)
    {
        return "
            <button 
                id='$id' 
                name='$id' 
                type='$type' 
                class='btn btn-sm btn-$clase'
            >
                <i class='$icono'></i>
                $txt
            </button>
        ";
    }
    public static function Input($id, $tipo, $value, $placeholder, $clase, $attr = "")
    {
        return "
            <div class='form-floating mb-3'>
                <input 
                    type='$tipo' 
                    class='form-control form-control border-$clase'
                    id='$id'
                    name='$id'
                    placeholder='$placeholder'
                    value='$value'
                    autocomplete='off'
                    required='required'
                    $attr
                >
                <label for='$id' class='fs-5 text-$clase'>$placeholder</label>
            </div>
        ";
    }
    public static function Textarea($id, $value, $placeholder, $rows, $clase)
    {
        return "
            <div class='form-floating mb-3'>
                <textarea class='form-control $clase' placeholder='$placeholder' id='$id' name='$id' style='height:" . (25 * $rows) . "px' required='required'>$value</textarea>
                <label for='$id' class='fs-5 text-$clase'>$placeholder</label>
            </div>
        ";
    }
    public static function Select($id, $label, $data, $clase, $itemVacio, $value = "")
    {
        $data2 = array();
        foreach ($data as $reg) {
            $data2[] = (array)$reg;
        }
        $data = $data2;

        $opciones = "";
        if ($itemVacio === "*") {
            $opciones = "<option value='TODOS'>Todos</option>";
        } else if ($itemVacio === true) {
            $opciones = "<option value=''>Seleccione un item</option>";
        }
        foreach ($data as $reg) {
            $sel = "";
            if ($reg['id'] == $value) {
                $sel = "selected";
            }
            $opciones .= "<option $sel value='" . $reg['id'] . "'>" . $reg['nombre'] . "</option>";
        }
        return "
            <div class='form-floating mb-3'>
                <select 
                    class='form-select border-$clase' 
                    id='$id' 
                    name='$id' 
                    required='required'
                >
                    $opciones
                </select>
                <label for='$id' class='fs-5 text-$clase'>$label</label>
            </div>
        ";
    }

    public static function CheckBox($id, $clase, $txt, $value)
    {
        return "
            <div class='form-check'>
                <input id='$id' class='form-check-input $clase' type='checkbox' value='$value'>
                <label for='_dm-rememberCheck' class='form-check-label'>
                    $txt
                </label>
            </div>
        ";
    }

    public static function Badge($clase, $txt)
    {
        return "<span class='badge bg-" . $clase . "'>$txt</span>";
    }
    public static function Estado($clase, $txt, $h)
    {
        return "<$h><span class='badge bg-" . $clase . " d-grid'>$txt</span></$h>";
    }
    public static function Row($componente, $clase)
    {
        return "<div class='row $clase'>$componente</div>";
    }
    public static function Col($col, $componente)
    {
        return "<div class='$col'>$componente</div>";
    }
    public static function Rol($modulo, $nombre, $descripcion, $clase, $icono, $url)
    {
        return "
            <div class='d-flex align-items-stretch border-$clase' style='border: 1px solid'>
                <div class='d-flex align-items-center justify-content-center flex-shrink-0 bg-$clase px-4 text-white'>
                        <i class='$icono fs-1'></i>
                </div>
                <div class='flex-grow-1 py-3 ms-3 border-$clase'>
                    <div class='h5 mb-0 text-$clase'>
                        <b>Módulo:</b>
                        $modulo
                    </div>
                    <div>
                    <a 
                        class='btn btn-xs btn-link mt-2 text-$clase'
                        onClick='cargarFuncion(\"$url\",\"$modulo\",\"$nombre\",\"$descripcion\")'
                    >$nombre</a>
                    </div>
                </div>
            </div>
        ";
    }
    public static function Card1($titulo, $body, $clase)
    {
        return "
            <div class='card bg-$clase text-white'>
                <h5 class='card-header'>$titulo</h5>
                <div class='card-body'>
                    $body
                </div>
            </div>
        ";
    }
    public static function H1($body, $clase)
    {
        return "
            <h1 class='$clase'>$body</h1>
        ";
    }
    public static function H2($body, $clase)
    {
        return "
            <h2 class='$clase'>$body</h2>
        ";
    }
    public static function H3($body, $clase)
    {
        return "
            <h3 class='$clase'>$body</h3>
        ";
    }
    public static function H4($body, $clase)
    {
        return "
            <h4 class='$clase'>$body</h4>
        ";
    }
    public static function H5($body, $clase)
    {
        return "
            <h5 class='$clase'>$body</h5>
        ";
    }
    public static function H6($body, $clase)
    {
        return "
            <h6 class='$clase'>$body</h6>
        ";
    }
    public static function Div($body, $clase, $id = "")
    {
        return "
            <div class='$clase' id='$id'>$body</div>
        ";
    }
    public static function Alert($body, $clase)
    {
        return "
            <div class='alert alert-$clase'>$body</div>
        ";
    }
    public static function Js($codigo)
    {
        return "
            <script>$codigo</script>
        ";
    }
    public static function Img($id, $src, $class)
    {
        return "
            <img src='" . base_url($src) . "' id='$id' class='$class'>
        ";
    }
    public static function Br()
    {
        return "<br>";
    }

    // se estan añadiendo estas funciones
    public static function File($id, $tipo, $value, $placeholder, $clase, $tamaño = '200000', $extension = '')
    {
        return "
            <div class='form-floating mb-3'>
                <input 
                    type='file' 
                    class='form-control border-$clase'
                    id='$id'
                    name='$id'
                    placeholder='$placeholder'
                    required='required'
                    accept='$extension'
                >
                <label for='$id' class='fs-5 text-$clase'>$placeholder</label>
            </div>
            <embed src='' style='width:100%; height:400px; border:2px solid;' type='application/pdf' id='embed_$id' class='border-primary'></embed>
            <script> $('#$id').on('change', function(){
                var ext = $( this ).val().split('.').pop();
                if ($( this ).val() != '') {
                  if(ext == 'pdf'){
                    /*alert('La extensión es: ' + ext);*/

                    if($(this)[0].files[0].size > " . $tamaño . "){
                      alert('El documento excede el tamaño máximo, se solicita un archivo no mayor a " . ($tamaño / (1024 * 1024)) . "MB. Por favor verifica.');
                                 
                      $(this).val('');
                    }else{
                        var TmpPath = URL.createObjectURL($(this)[0].files[0]);
                        console.log(TmpPath);
                        $('#embed_$id').attr('src',TmpPath);
                    }
                  }
                  else
                  {
                    $( this ).val('');
                    alert('Extensión no permitida: ' + ext);
                  }
                }
              });
              </script>
        ";
    }
    public static function File2($id, $tipo, $value, $placeholder, $clase, $tamaño = '200000', $extension = '')
    {
        return "
            <div class='form-floating mb-3'>
                <input 
                    type='file' 
                    class='form-control border-$clase'
                    id='$id'
                    name='$id'
                    placeholder='$placeholder'
                    required='required'
                    accept='$extension'
                >
                <label for='$id' class='fs-5 text-$clase'>$placeholder</label>
            </div>
            <script> 
                $('#$id').on('change', function(){
                    var ext = $( this ).val().split('.').pop();
                    if ($( this ).val() != '') {
                        if($(this)[0].files[0].size > " . $tamaño . "){
                            alert('El documento excede el tamaño máximo, se solicita un archivo no mayor a " . ($tamaño / (1024 * 1024)) . "MB. Por favor verifica.');
                            $(this).val('');
                        }
                    }
                });
              </script>
        ";
    }
    // se estan añadiendo estas funciones
    public static function previewFile($id = '', $src = '', $altura = '500px', $ancho = '100%', $clase = '')
    {
        return "<iframe src='$src' style='width:$ancho; height:$altura;' type='application/pdf' id='embed_$id' ></iframe>";
    }

    public static function Hidden($id, $value)
    {
        return "
            <div class='form-floating mb-3'>
                <input 
                    type='hidden' 
                    id='$id'
                    name='$id'
                    value='$value'
                    autocomplete='off'
                    required='required'
                >
            </div>
        ";
    }

    public static function Link($icono, $clase, $txt, $href, $id = "")
    {
        return "
            <a id='$id' class='btn btn-$clase' href='$href' target='black'>
                <i class='$icono'></i>
                $txt
            </a>
        ";
    }

    public function informes($info_ide, $cate_nombre, $info_nombre, $info_alias, $info_formato, $clase)
    {
        $formato = "
        <a class='btn btn-secondary' href='" . base_url("public/formatos/$info_formato") . "' target='_black'>
            <i class='ti-file'></i>
            Descargar formato: $info_alias
        </a>
        ";
        if ($info_alias == "") {
            $formato = "";
        }
        return "
            <div class='card border-$clase mb-2' style='2px solid'>
                <h4 class='card-header bg-$clase text-white'>$cate_nombre</h4>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-sm-12'>
                            <div class=''>$info_nombre</div>
                        </div>
                    </div>
                    <div class='row mt-1'>
                        <div class='col-sm-6 d-grid'>$formato</div>
                        <div class='col-sm-6 d-grid'>
                            <button class='btn btn-$clase btnDetalleEnvio' info='$info_ide'>
                            <i class='ti-search'></i>
                            Ver documentos entregados
                        </button>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }

    public function informeEntregado($subi_ide, $info_ide, $iiee_ide, $usua_ide, $info_nombre, $subi_archivo, $subi_create_at, $clase, $esta_clase, $esta_nombre)
    {
        return "
            <div class='card border-$clase mb-2' style='2px solid'>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-sm-12'>                            
                            <div class='badge bg-$esta_clase'>$esta_nombre</div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm-12'>                            
                            <div class='text-center'>$info_nombre</div>
                        </div>
                    </div>
                    <div class='row mt-1'>
                        <div class='col-sm-6 d-grid'>
                            <button class='btn btn-danger btnDelEntre' subi='$subi_ide' info='$info_ide' iiee='$iiee_ide' usua='$usua_ide'>
                                <i class='ti-trash'></i>
                                Eliminar
                            </button>
                        </div>
                        <div class='col-sm-6 d-grid'>
                            <button class='btn btn-$clase btnVerEntre' pdf='$subi_archivo'>
                                <i class='ti-search'></i>
                                Previsualizar
                            </button>
                        </div>
                    </div>
                    <div class='row mt-3'>
                        <div class='col-sm-12 d-grid'>
                            <div class='text-end'><b class='text-dark'>Entregado el </b>$subi_create_at</div>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }
    public static function Alternativas($id, $preg, $num_alter, $clase, $marcado)
    {
        $alter = "";
        for ($i = 1; $i <= $num_alter; $i++) {
            $sel = "";
            if (chr($i + 64) == $marcado) {
                $sel = "selected";
            }
            $alter .= Componente::Col(
                "col-sm-1",
                "<div 
                    id='alter_" . $preg . "_$i'
                    class='alter preg_$preg $sel' 
                    onClick='marcar($preg,$i)' 
                    title='Seleccionar alternativa " . chr($i + 64) . "'
                >
                    " . chr($i + 64) . "
                </div>
                "
            );
        }
        return "
            <script>
                function marcar(pregunta, alternativa){
                    $('.preg_'+pregunta).removeClass('selected');
                    if($('#preg_'+pregunta).attr('alter')==String.fromCharCode(alternativa+64)){
                        $('#preg_'+pregunta).attr('alter','X');
                    }
                    else{
                        $('#alter_'+pregunta+'_'+alternativa).addClass('selected');
                        $('#preg_'+pregunta).attr('alter',String.fromCharCode(alternativa+64));    
                    }
                }
            </script>
            <style>
                .alter{
                    border: 2px solid #ccc;
                    border-radius: 6px;
                    padding: 10px;
                    text-align: center;
                    cursor: pointer;
                    transition: background-color 0.2s, border-color 0.2s;
                }
                .alter:hover {
                    background-color: #f0f8ff;
                    border-color: #007bff;
                }
                .alter.selected {
                    background-color: #007bff;
                    color: white;
                    border-color: #0056b3;
                }
            </style>
            <div class='row mb-1 preguntas' id='preg_$preg' alter='$marcado'>
                <div class='col-sm-2'>
                    <div class='h5 text-$clase text-end'>
                        Pregunta $preg
                    </div>
                </div>
                $alter
            </div>
        ";
    }
    public static function AutoCompletar($id, $value, $placeholder, $clase, $mb3, $url, $tabla, $campos, $where, $order, $destinos)
    {
        return "
            <div class='form-floating " . ($mb3 ? "mb-3" : "") . "'>
                <input 
                    type='text' 
                    class='form-control form-control border-$clase'
                    id='$id'
                    name='$id'
                    placeholder='$placeholder'
                    value='$value'
                    autocomplete='off'
                    required='required'
                >
                <label for='$id' class='fs-5 text-$clase'>$placeholder</label>
                <!--
                <div id='$id-counter' class='form-text text-muted'>0/150 caracteres</div>
                -->
                <div id='$id-error' class='text text-danger form-errores'></div>
            </div>
            <div id='suggestions-$id' class='suggestions' style='display:none; position:absolute; background-color:white; border:1px solid #ccc; z-index:1000;'></div>
            <style>
                .suggestions {
                    /*width: 300px;*/
                    border-radius: 6px;
                    border: 1px solid #ccc;
                    background: #fff;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    position: absolute;
                    margin-top: 5px;
                    z-index: 1000;
                }

                .suggestion-item {
                    padding: 10px;
                    cursor: pointer;
                    transition: background-color 0.2s;
                }

                .suggestion-item:hover {
                    background-color: #f0f8ff;
                }

            </style>
            <script>
                $('#$id').on('input', function() {
                    var query = $(this).val();
                    if (query.length > 1) {
                        param = { 
                            term: query,
                            tabla: '$tabla', 
                            campos: '$campos',
                            where: '$where',
                            order: '$order'
                        }
                        ajax('$url',param, function(data) {
                            let suggestions = '';
                            data.forEach(function(item) {
                                suggestions += '<div class=\"suggestion-item suggestion-$id-item\" style=\"padding:5px; cursor:pointer;\">' + item + '</div>';
                            });
                            $('#suggestions-$id').html(suggestions).show();
                        },false);
                    } else {
                        $('#suggestions-$id').hide();
                    }
                });

                $(document).on('click', '.suggestion-$id-item', function() {
                    //alert('aqui se selecciona');
                    //$('#$id').val($(this).text());

                    let desti = '$destinos';
                    desti = desti.split(',');
                    
                    let datos = $(this).text();
                    datos = datos.split(' -> ');
                    
                    for(i=0;i<desti.length;i++){
                        $('#'+desti[i]).val(datos[i]);
                    }

                    $('#suggestions-$id').hide();
                    $('#$id').change();
                });

                $(document).click(function(e) {
                    if (!$(e.target).closest('#$id, #suggestions-$id').length) {
                        $('#suggestions-$id').hide();
                    }
                });

                $('#$id').on('input change', function() {
                    chars = $(this).val().length
                    $('#$id-counter').text(chars + '/150 caracteres')
                    if (chars >= 130)
                        $('#$id-counter').removeClass('text-muted').addClass('text-warning')
                    else
                        $('#$id-counter').removeClass('text-warning').addClass('text-muted')
                });
            </script>
        ";
    }
}
