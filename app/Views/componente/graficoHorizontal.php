

<script src="<?= base_url('public/assets/vendors/chart.js/chart.min.js') ?>" defer></script>
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title">Bar chart</h5>

                <!-- Bar Chart -->
                <canvas id="_dm-barChart"></canvas>
                <!-- END : Bar Chart -->

            </div>
        </div>
    </div>
</div>




<?php
    $g = array();
     print_r($evaluacion);
     foreach ($grados as $grad) {
        array_push($g, $grad->grad_nombre);
     }


     /**
      * $g = nombre de Grados
      */

?>

<script>
    // Color variables based on css variable.
    // ----------------------------------------------
    var _body = getComputedStyle(document.body);
    var primaryColor = _body.getPropertyValue("--bs-comp-active-bg");
    var successColor = _body.getPropertyValue("--bs-success");
    var infoColor = _body.getPropertyValue("--bs-info");
    var warningColor = _body.getPropertyValue("--bs-warning");
    var dangerColor = _body.getPropertyValue("--bs-danger");
    var mutedColorRGB = _body.getPropertyValue("--bs-muted-color-rgb");
    var grayColor = "rgba( 180,180,180, .2 )";

    // Bar Chart
    // ----------------------------------------------
    var barDataC = [10, 14, 10, 12, 23, 10];
    var barDataB = [20, 26, 30, 27, 29, 15];
    var barDataA = [50, 40, 41, 28, 13, 45];
    var barDataAD = [20, 20, 19, 33, 35, 30];

    var labels = <?=json_encode($g);?>;
    
    var colorA = {
        C: {
            border: 'rgb(255, 99, 132)',
            background: 'rgb(255, 99, 132,0.8)'
        },
        B: {
            border: 'rgb(153, 102, 255)',
            background: 'rgb(153, 102, 255, 0.8)'
        },
        A: {
            border: 'rgb(255, 205, 86)',
            background: 'rgb(255, 205, 86, 0.8)'
        },
        AD: {
            border: 'rgb(139, 221, 66)',
            background: 'rgb(139, 221, 66, 0.8)'
        },

    }


    var barChart = new Chart(
        document.getElementById("_dm-barChart"), {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                        label: "C",
                        data: barDataC,
                        borderWidth: 1,
                        borderColor: colorA.C.border,
                        backgroundColor: colorA.C.background,

                        stack: 0
                    },
                    {
                        label: "B",
                        data: barDataB,
                        borderWidth: 1,
                        borderColor: colorA.B.border,
                        backgroundColor: colorA.B.background,
                        stack: 0
                    },
                    {
                        label: "A",
                        data: barDataA,
                        borderWidth: 1,
                        borderColor: colorA.A.border,
                        backgroundColor: colorA.A.background,
                        stack: 0
                    },
                    {
                        label: "AD",
                        data: barDataAD,
                        borderWidth: 1,
                        borderColor: colorA.AD.border,
                        backgroundColor: colorA.AD.background,
                        stack: 0
                    }
                ]
            },

            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: true,
                        align: "bot",
                        labels: {
                            color: `rgb( ${ mutedColorRGB })`,
                            boxWidth: 10,
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgb(0,0,0,0.5)',
                        position: "nearest"
                    }
                },
                interaction: {
                    mode: "index",
                    intersect: true,
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100
                    },
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        align: 'end',
                        labels: {
                            boxWidth: 10,

                        }
                    },
                    title: {
                        display: true,
                        text: 'Area: Personal Social'
                    },
                    subtitle: {
                        display: true,
                        text: 'Competencia: Construye su mente '
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'bottom',
                        formatter: function(value, context) {
                            return value.toFixed(0) + '%';
                        }
                    }
                }
            }
        }
    );
</script>