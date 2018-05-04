<h2>Performance Metrics</h2>
<div class="metrics">
    <div class="metric">
        <span class="value">{$sDetail.profileTime|number_format:2} <span class="unit">s</span></span>
        <span class="label">Request Time</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.db.queryTime|number_format:2} <span class="unit">ms</span></span>
        <span class="label">SQL Time</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.db.totalQueries}</span>
        <span class="label">Total Queries</span>
    </div>
    <div class="metric">
        <span class="value">{$sDetail.php.used_memory|convertMemory}</span>
        <span class="label">Memory Usage</span>
    </div>
</div>

<h2>Event Chart</h2>
<small>Press to a chart bar for more informations</small>

<canvas id="canvas"></canvas>

<div id="eventsOverview" style="display: none">
    <h2>Executed listeners for event <span id="eventName"></span></h2>
    <table class="">
        <thead>
        <tr>
            <th scope="col">Callable</th>
            <th scope="col">Time</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
    var listeners = {$sDetail.events.eventListeners|@json_encode};
    var eventsOverview = document.getElementById('eventsOverview');
    var eventsTBody = eventsOverview.querySelector('tbody');
    var eventName = document.getElementById('eventName');
    window.onload = function () {
        var ct = document.getElementById("canvas");
        var ctx = ct.getContext("2d");
        ct.onclick = function (evt) {
            var activePoints = window.myHorizontalBar.getElementsAtEvent(evt);
            if (activePoints[0] && listeners[activePoints[0]._view.label] !== undefined) {
                eventsOverview.style.display = "block";
                eventName.innerHTML = activePoints[0]._view.label;

                var html = '';
                listeners[activePoints[0]._view.label].forEach(function (listener) {
                    html += '<tr><td class="key">' + listener.name + '</td><td>' + listener.duration.toString() + ' ms</td></tr>';
                });
                eventsTBody.innerHTML = html;
            }
        };

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
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            padding: 0
                        }
                    }],
                }
            }
        });
    };
</script>