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
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body class="container-fluid">
    <div class="page-header">
        <h1 class="text-center">
            TeamSpeak3 <br>
            <small>The Entropics</small>
        </h1>
    </div>


    <?php include __DIR__ . "/src/index/scoreboard.php"; ?>
    <?php include __DIR__ . "/src/index/counter.php"; ?>
    <div class="clearfix"></div>
    <?php include __DIR__ . "/src/index/dailyGrid.php"; ?>

    <?php include __DIR__ . "/src/index/footer.php"; ?>
</body>
</html>
