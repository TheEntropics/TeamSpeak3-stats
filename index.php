<?php

require_once __DIR__ . '/src/classes/Controller.php';

Controller::run();

$scoreboard = UptimeVisualizer::getUptimeScoreboard();

$grid = array();
$gridSql = "SELECT * FROM daily_results";
$gridQuery = DB::$DB->query($gridSql)->fetchAll();
foreach ($gridQuery as $row) $grid[$row['cell_id']] = $row['average'];

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
        <?php foreach($scoreboard as $user) { ?>
            <li><?php echo $user['username'] ?> <small><?php echo $user['total_uptime'] ?></small></li>
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
