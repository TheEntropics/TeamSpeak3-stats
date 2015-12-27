<?php

require_once __DIR__ . '/src/classes/Controller.php';

Controller::init(true);

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
<body class="container-fluid">
    <div class="page-header">
        <h1 class="text-center">
            <a href=".">TeamSpeak3</a> <br>
            <small>The Entropics</small>
        </h1>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?php include __DIR__ . "/src/index/scoreboard.php"; ?>
            <?php include __DIR__ . "/src/index/lastLog.php"; ?>
        </div>
        <div class="col-md-6">
            <?php include __DIR__ . "/src/index/counter.php"; ?>
            <?php if (Config::REALTIME_ENABLED) include __DIR__ . "/src/index/realtime.php"; ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php include __DIR__ . "/src/index/dailyGrid.php"; ?>

    <?php include __DIR__ . "/src/index/footer.php"; ?>
</body>
</html>
