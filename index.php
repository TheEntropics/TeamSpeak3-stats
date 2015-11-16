<?php

require_once __DIR__ . '/src/classes/Controller.php';

Controller::init();

$scoreboard = UptimeVisualizer::getUptimeScoreboard();

$grid = DailyVisualizer::getGrid();

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
            <li><?php echo $user['username'] ?> <small><?php echo $user['score'] ?></small></li>
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
                    <td style="background-color: <?php echo $grid[$day][$i]['color'] ?>">
                        <?php echo $grid[$day][$i]['value'] ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
