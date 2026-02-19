<script src="<?= base_url('public/assets/vendors/chart.js/chart.min.js') ?>"></script>
<script src="<?= base_url('public/assets/pages/chart.js') ?>"></script>

<canvas id="_dm-barChart">Hola</canvas>

<script>
    // Bar Chart
    // ----------------------------------------------
    //_body = getComputedStyle(document.body);
    /*primaryColor = _body.getPropertyValue("--bs-comp-active-bg");
    successColor = _body.getPropertyValue("--bs-success");
    infoColor = _body.getPropertyValue("--bs-info");
    warningColor = _body.getPropertyValue("--bs-warning");*/
    //mutedColorRGB = _body.getPropertyValue("--bs-muted-color-rgb");
    //grayColor = "rgba( 180,180,180, .2 )";


    function dibujar() {
        _body = getComputedStyle(document.body);
        mutedColorRGB = _body.getPropertyValue("--bs-muted-color-rgb");
        const barData = <?php echo json_encode($data); ?>;
        const label = <?php echo json_encode($label); ?>;
        const bg = [
            _body.getPropertyValue("--bs-comp-active-bg"),
            _body.getPropertyValue("--bs-success"),
            _body.getPropertyValue("--bs-info"),
            _body.getPropertyValue("--bs-warning")
        ];
        ds = Array();
        for (i = 0; i < label.length; i++) {
            ds[i] = {
                label: label[i],
                data: barData,
                borderWidth: 2,
                borderColor: bg[i],
                backgroundColor: bg[i],
                parsing: {
                    xAxisKey: "ejex",
                    yAxisKey: "ejey" + (i + 1)
                }
            };
        }

        const barChart = new Chart(
            document.getElementById("_dm-barChart"), {
                type: "bar",
                data: {
                    datasets: ds
                },

                options: {
                    plugins: {
                        legend: {
                            display: true,
                            align: "end",
                            labels: {
                                color: `rgb( ${ mutedColorRGB })`,
                                boxWidth: 10,
                            }
                        },
                        tooltip: {
                            position: "nearest"
                        }
                    },

                    interaction: {
                        mode: "index",
                        intersect: false,
                    },

                    scales: {
                        y: {
                            grid: {
                                borderWidth: 0,
                                color: `rgba( ${ mutedColorRGB }, .07 )`
                            },
                            //suggestedMax: 150,
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: `rgb( ${ mutedColorRGB })`,
                                beginAtZero: false,
                                stepSize: 10
                            }
                        },
                        x: {
                            grid: {
                                borderWidth: 0,
                                drawOnChartArea: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: `rgb( ${ mutedColorRGB })`,
                                autoSkip: false,
                                maxRotation: 0,
                                minRotation: 45,
                                maxTicksLimit: 7
                            }
                        }
                    },

                    elements: {
                        // Dot width
                        point: {
                            radius: 3,
                            hoverRadius: 5
                        },
                        // Smooth lines
                        line: {
                            tension: 0.2
                        }
                    }
                }
            }
        );
    }
    dibujar();
</script>