<?php

if (!isset($_GET['client-id'])) {
    header('location: .');
    exit;
}

require_once __DIR__ . '/src/classes/Controller.php';
Controller::init(true);

$client_id1 = intval($_GET['client-id']);
$client_id = User::getMasterClientId($client_id1);

$username = ProbableUsernameVisualizer::getProbableUsername($client_id);

if ($username == null) {
    header('location: .');
    exit;
}

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
    <script src="js/utils.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="container">
    <div class="page-header">
        <h1 class="text-center">
            <a href=".">TeamSpeak3</a> <br>
            <small>The Entropics</small>
        </h1>
    </div>
    <?php require __DIR__ . '/src/user/online.php' ?>
    <h2>Statistiche di <small><?php echo $username ?></small></h2>
    <?php require __DIR__ . '/src/user/ranges.php' ?>
    <?php require __DIR__ . '/src/user/username.php' ?>
    <?php require __DIR__ . '/src/user/streak.php' ?>
    <?php require __DIR__ . '/src/user/daily_graph.php' ?>
</body>
</html>
