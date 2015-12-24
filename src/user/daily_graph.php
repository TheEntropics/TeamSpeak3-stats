<h3>Uptime giornaliero</h3>
<div id="user_calendar" style="width: 100%; height: 350px;"></div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1.1", {packages:["calendar"]});
    google.setOnLoadCallback(drawChart);

    function parseDate(input) {
        var parts = input.split('-');
        return new Date(parts[0], parts[1]-1, parts[2]);
    }
    function processData(data) {
        rows = [];
        for (var i in data) {
            var date = parseDate(data[i].date);
            var time = parseInt(data[i].day_time);
            var tooltip = data[i].tooltip;
            rows.push([date, time, tooltip]);
        }
        return rows;
    }
    function drawChart() {
        $.ajax({
            url: './user_graph.php?client-id=<?php echo $client_id ?>',
            dataType: 'JSON',
            success: function (data) {
                console.log(data);
                console.log(processData(data));
                var dataTable = new google.visualization.DataTable();
                dataTable.addColumn({type: 'date', id: 'Date'});
                dataTable.addColumn({type: 'number', id: 'Seconds'});
                dataTable.addColumn({type: 'string', id: 'Text', role: 'tooltip', p: {html: true}});
                dataTable.addRows(processData(data));

                var chart = new google.visualization.Calendar(document.getElementById('user_calendar'));

                var options = {
                    height: 350,
                };

                chart.draw(dataTable, options);
            }
        });
    }
</script>
