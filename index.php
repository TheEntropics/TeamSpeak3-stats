<?php

require_once __DIR__ . '/src/classes/Controller.php';

Controller::init(true);

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
<body class="container-fluid">
    <div class="page-header">
        <h1 class="text-center">
            <a href=".">TeamSpeak3</a> <br>
            <small>The Entropics</small>
        </h1>
    </div>


    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <?php include __DIR__ . "/src/index/scoreboard.php"; ?>
            <?php include __DIR__ . "/src/index/lastLog.php"; ?>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <?php include __DIR__ . "/src/index/counter.php"; ?>
            <?php if (Config::REALTIME_ENABLED) include __DIR__ . "/src/index/realtime.php"; ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php include __DIR__ . "/src/index/dailyGrid.php"; ?>

    <?php include __DIR__ . "/src/index/footer.php"; ?>
</body>
</html>
