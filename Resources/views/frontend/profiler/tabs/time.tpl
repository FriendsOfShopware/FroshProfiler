<h2>Performance Metrics</h2>
<div class="metrics">
    <div class="metric">
        <span class="value">{$sDetail.profileTime|number_format:2} <span class="unit">s</span></span>
        <span class="label">Request Time</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.php.used_memory|convertMemory}</span>
        <span class="label">Memory Usage</span>
    </div>
</div>

<canvas id="canvas"></canvas>

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