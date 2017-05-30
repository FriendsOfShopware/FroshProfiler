<div style="position: relative">
    <canvas id="canvas"></canvas>
</div>

<script>
    window.onload = function () {
        var ctx = document.getElementById("canvas").getContext("2d");
        var chartData = {
            labels: {$sDetail.events.chartLabels|@json_encode},
            datasets: [
                {
                    label: 'milliseconds',
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: "rgb(255, 99, 132)",
                    borderWidth: 1,
                    data: {$sDetail.events.chartValues|@json_encode}
                }
            ]
        };

        window.myHorizontalBar = new Chart(ctx, {
            type: 'horizontalBar',
            data: chartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2
                    }
                },
                responsive: true,
                legend: {
                    position: 'right'
                },
                title: {
                    display: true,
                    text: 'Event-Duration'
                }
            }
        });
    };
</script>