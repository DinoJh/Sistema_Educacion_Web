<style>
    .table-hover>tbody>tr:hover {
        /* --bs-table-accent-bg: #2f5fa9; */
        color: var(--bs-primary);
        border-left: 3px solid var(--bs-primary);
        /*font-weight:bold;*/
        font-style: italic;
        cursor: pointer;
    }

    th {
        cursor: pointer;
    }
</style>
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card border-2 border-primary">
            <div class="card-body">
                <?php if ($grilla_ide === 'grilla_aspectos') : ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0"><?php echo $tituloTabla; ?></h5>
                        <button type="button" id="btn_agregar" class="btn btn-primary">Agregar</button>
                    </div>
                <?php else : ?>
                    <h5 class="card-title"><?php echo $tituloTabla; ?></h5>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="<?php echo $grilla_ide . "_tabla"; ?>" class="gridjs-table table table-bordered table-hover table-striped" style="max-width:<?php echo $widthTabla; ?>;width:<?php echo $widthTabla; ?>!important;">
                        <thead class="gridjs-thead bg-light">
                            <tr class="gridjs-tr text-center">
                                <th width="5%">Nro.</th>
                                <?php foreach ($campos as $reg) { ?>
                                    <th width="<?php echo $reg["Width"] . "%"; ?>"><?php echo $reg["Label"]; ?> </th>
                                <?php } ?>
                                <?php if ($grilla_ide === 'grilla_aspectos') : ?>
                                    <th width="12%">Opciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>

                        <tbody id="<?php echo $grilla_ide . "_data"; ?>">
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <form id="form_buscar_grilla" autocomplete="no-save"> <input type="text" id="<?php echo $grilla_ide . "_b"; ?>" placeholder="Buscar..." class="form-control form-control-sm border-primary mb-1" onchange="getDataGrilla()"></form>
                    </div>
                    <div class="col-sm-1 d-grid">
                        <button class="btn btn-sm btn-secondary mb-1" onclick="getDataGrilla()">
                            <i class="fas fa-search ti-search"></i>
                            Buscar
                        </button>
                    </div>
                    <div class="col-sm-1 text-end">
                        Número de registros
                    </div>
                    <div class="col-sm-2">
                        <select id="<?php echo $grilla_ide . "_m"; ?>" class="form-select form-select-sm border-primary mb-1" onchange="getDataGrilla()">
                            <?php foreach ($listaMostrar as $lis) { ?>
                                <option value="<?php echo $lis; ?>"><?php echo $lis; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-1 text-end">
                        Número de Páginas
                    </div>
                    <!--<div class="col-sm-2" id="<?php echo $grilla_ide . "_p"; ?>">
                    </div>-->
                    <div class="col-sm-2">
                        <select id="<?php echo $grilla_ide . "_p"; ?>" class="form-select form-select-sm border-primary mb-1" onchange="getDataGrilla()">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php foreach ($componentes as $reg) {
                            echo $reg;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function getDataGrilla() {
        openCargar();
        param = {
            load: true,
            m: $("<?php echo "#" . $grilla_ide . "_m"; ?>").val(),
            p: $("<?php echo "#" . $grilla_ide . "_p"; ?>").val(),
            b: $("<?php echo "#" . $grilla_ide . "_b"; ?>").val(),
        };
        p = "?" + "m=" + param.m + "&";
        p += "p=" + param.p + "&";
        p += "b=" + param.b;
        $.get("<?php echo $url; ?>" + p, param, function(data) {
            $("<?php echo "#" . $grilla_ide . "_data"; ?>").html(data);
            closeCargar();
        });
    }
    getDataGrilla();

    $('#form_buscar_grilla').on('submit', (event) => {
        event.preventDefault();
    });
</script>