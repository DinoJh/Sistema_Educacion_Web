<script src="<?= base_url('public/assets/vendors/chart.js/chart.min.js') ?>"></script>
<script src="<?= base_url('public/assets/pages/chart.js') ?>"></script>

<canvas id="_dm-pieChart">Hola</canvas>

<script>
    // Bar Chart
    // ----------------------------------------------
    _body = getComputedStyle(document.body);
    primaryColor = _body.getPropertyValue("--bs-comp-active-bg");
    successColor = _body.getPropertyValue("--bs-success");
    infoColor = _body.getPropertyValue("--bs-info");
    warningColor = _body.getPropertyValue("--bs-warning");
    mutedColorRGB = _body.getPropertyValue("--bs-muted-color-rgb");
    grayColor = "rgba( 180,180,180, .2 )";

    function dibujar(primaryColor, infoColor, mutedColorRGB) {
        const circleData = [25, 35, 98, 59, 17];
        const pieChart = new Chart(
            document.getElementById("_dm-pieChart"), {
                type: "pie",
                data: {
                    labels: ["Blue", "Orange", "Navy", "Green", "Gray"],
                    datasets: [{
                        data: circleData,
                        borderColor: "transparent",
                        backgroundColor: [infoColor, warningColor, primaryColor, successColor, grayColor],
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: `rgb( ${ mutedColorRGB })`,
                                boxWidth: 10,
                            }
                        },
                    }
                }
            }
        );
    }
    dibujar(primaryColor, infoColor, mutedColorRGB);
</script>