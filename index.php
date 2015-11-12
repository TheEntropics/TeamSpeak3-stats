<?php

// TODO: implementare la home page delle statistiche
// i dati verranno estratti dal database, non sarà necessario calcolarli, basta formattarli

require_once __DIR__ . '/src/classes/Controller.php';

Controller::run();

$uptimeSql = "SELECT * FROM uptime_results JOIN users ON uptime_results.client_id = users.client_id GROUP BY users.client_id ORDER BY uptime DESC";
$uptimeResults = DB::$DB->query($uptimeSql)->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>TeamSpeak3-stats</title>
</head>
<body>
    <h1>Timur Baznat</h1>

    <h2>Uptime</h2>
    <ol>
        <?php foreach($uptimeResults as $user) { ?>
            <li><?php echo $user['username'] ?> <small><?php echo $user['uptime'] ?></small></li>
        <?php } ?>
    </ol>
</body>
</html>
