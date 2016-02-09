<?php

require_once __DIR__ . '/src/classes/Controller.php';
Controller::init(true);

$channels = ChannelVisualizer::getChannels();

?>
<!DOCTYPE html>
<html>
<head>
    <title>TeamSpeak3 - The Entropics</title>
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="bower_components/bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                    <td><?php echo Utils::formatDate($channel[0]['date']) ?></td>
                    <td><?php echo Utils::formatDate($channel[1]['date']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>

