<?php

require_once __DIR__ . '/src/classes/Controller.php';
Controller::init(true);

$channels = ChannelVisualizer::getChannels();

?>
<!DOCTYPE html>
<html>
<head>
    <title>TeamSpeak3 - The Entropics</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body class="container">
    <div class="page-header">
        <h1 class="text-center">
            <a href=".">TeamSpeak3</a> <br>
            <small>The Entropics</small>
        </h1>
    </div>
    <h1>Elenco canali</h1>
    <table class="table table-responsive table-condensed">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Creato il</th>
                <th>Eliminato il</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($channels as $channel) { ?>
                <tr>
                    <td><?php echo $channel[0]['name'] ?></td>
                    <td><?php echo (new DateTime($channel[0]['date']))->format('d/m/Y \a\l\l\e H:i:s') ?></td>
                    <td><?php echo (new DateTime($channel[1]['date']))->format('d/m/Y \a\l\l\e H:i:s') ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>

