<?php

use App\Libraries\Componente;
?>
<?php if (count($data) == 0) { ?>
    <tr>
        <td colspan="<?php echo count($campos); ?>">No se encuentran registros para mostrar</td>
    </tr>
<?php } ?>
<?php
$i = intval($ini) + 1;
foreach ($data as $reg) {
    $tmp = (array)$reg;
?>
    <tr <!--onclick="alert(123)" -->>
        <td><?php echo $i++; ?></td>
        <?php foreach ($campos as $r) { ?>
            <td title="<?php echo $tmp[$r["Field"]]; ?>">
                <?php
                $format = $r["Format"];
                if ($format == "no") {
                    echo $tmp[$r["Field"]];
                } else if ($format == "archivos") {
                    //$server = $tmp["expe_server"];
                    $archivos = $tmp[$r["Field"]];
                    //$archivos = explode(",", $archivos);
                    $ccc = 1;
                    $base = base_url();
                    //$arch = $archivos;
                    //echo Componente::Link($base . "/archivos/" . $arch, "_black", "secondary btn-xs mb-1 mr-1", "Adjunto $ccc");
                    if ($archivos != "") {
                        echo Componente::Link("ti-file", "secondary", $archivos, $base . "/archivos/" . $archivos);
                    }
                    /*foreach ($archivos as $arch) {
                        if ($arch != "") {
                            $base = "";
                            $base = base_url();
                            if ($server == "this") {
                                $base = "http://xura-inc.com/tramitameuc/";
                            } else if ($server == "consophi") {
                                $base = "http://consorciophi.com/tramitameuc1/";
                            } else {
                                $base = base_url();
                            }
                            echo Componente::Link($base . "/archivos/" . $arch, "_black", "secondary btn-xs mb-1 mr-1", "Adjunto $ccc");
                            $ccc++;
                        }
                    }*/
                } else if ($format == "informe") {
                    $archivo = $tmp[$r["Field"]];
                    $base = base_url();
                    if ($archivo != "") {
                        echo Componente::Div(Componente::Link("ti-download", "secondary", "Descargar", $base . "/archivos/" . $archivo), "text-center", "");
                    } else {
                        echo Componente::Div(Componente::Boton("", "button", "danger", "ti-close", " No Cumplió"), "text-center", "");
                    }
                } else if ($format == "ocultar") {
                    //aqui va para ocultar
                } else if ($format == "check") {
                    echo Componente::CheckBox(
                        $grilla_ide . "_" . $r["Field"] . "_" . $tmp[$r["Field"]],
                        "seleccionados",
                        "",
                        $tmp[$r["Field"]]
                    );
                } else if (is_array($format)) {
                    if ($format[0] == "btn") {
                        echo Componente::Boton(
                            $grilla_ide . "_" . $r["Field"] . "_" . $tmp[$r["Field"]],
                            "button",
                            $format[1] . " btn-xs",
                            $format[2],
                            $format[3]
                        );
                    }
                    if ($format[0] == "estado") {
                        echo Componente::Estado(
                            $tmp["esta_clase"],
                            $tmp[$r["Field"]],
                            "h4"
                        );
                    }
                }
                ?>
            </td>
        <?php } ?>
        <?php if ($grilla_ide === 'grilla_aspectos') : ?>
            <td>
                <?= Componente::Boton($tmp['asp_ide'] ?? "", "button", "primary btn-sm btn_editar", " ti-pencil-alt", "") ?>
                <?= Componente::Boton($tmp['asp_ide'] ?? "", "button", "danger btn-sm btn_eliminar", " ti-trash", "") ?>
            </td>
        <?php endif; ?>
    </tr>
<?php } ?>


<script>
    function getPaginasGrilla() {
        opciones = "";
        for (i = 1; i <= <?php echo $pgs; ?>; i++) {
            opciones += "<option value='" + i + "'>" + i + " de " + <?php echo $pgs; ?> + "</option>";
        }
        $("<?php echo "#" . $grilla_ide . "_p" ?>").html(opciones);
        $("<?php echo "#" . $grilla_ide . "_p" ?>").val("<?php echo $pag; ?>");
    }
    getPaginasGrilla();
</script>