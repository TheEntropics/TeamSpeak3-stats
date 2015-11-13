<pre><?php

// TODO: implementare la home page delle statistiche
// i dati verranno estratti dal database, non sarà necessario calcolarli, basta formattarli

require_once __DIR__ . '/src/classes/Controller.php';

$start = microtime(true);
Controller::run();
echo "Total time: " . (microtime(true) - $start) . "<br>";

$uptimeSql = "SELECT * FROM uptime_results JOIN users ON uptime_results.client_id = users.client_id GROUP BY users.client_id ORDER BY uptime DESC";
$uptimeResults = DB::$DB->query($uptimeSql)->fetchAll();

$grid = DailyAnalyzer::runAnalysis();

function toColor($n) {
    $r = 30;
    $g = (int)($n*256/4);
    $b = 200;
    $color = $r*256*256 + $g*256 + $b;
    return("#".substr("000000".dechex($color),-6));
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>TeamSpeak3-stats</title>
    <style>
        td { padding: 5px; min-width: 42px; text-align: center; }
    </style>
</head>
<body>
    <h1>TeamSpeak3-stats</h1>

    <h2>Uptime</h2>
    <ol>
        <?php foreach($uptimeResults as $user) { ?>
            <li><?php echo $user['username'] ?> <small><?php echo $user['uptime'] ?></small></li>
        <?php } ?>
    </ol>

    <h2>Grid</h2>
    <table>
        <tr>
            <th></th>
            <?php for ($i = 0; $i < 24; $i++) { ?>
                <th><?php echo "$i - " . ($i + 1); ?></th>
            <?php } ?>
        </tr>
        <?php for ($day = 0; $day < 7; $day++) { ?>
            <tr>
                <td><?php echo "Day $day"; ?></td>
                <?php for ($i = 0; $i < 24; $i++) { ?>
                    <td style="background-color: <?php echo toColor($grid[$day*24+$i]) ?>">
                        <?php echo round($grid[$day*24+$i], 2); ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
