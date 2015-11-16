<?php

require_once __DIR__ . '/src/classes/Controller.php';

Controller::init();

?>
<!DOCTYPE html>
<html>
<head>
    <title>TeamSpeak3-stats</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap-theme.min.css" rel="stylesheet">
    <script src="/js/jquery-2.1.4.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
</head>
<body class="container-fluid">
    <h1>TeamSpeak3-stats</h1>

    <?php include __DIR__ . "/src/index/scoreboard.php"; ?>
    <?php include __DIR__ . "/src/index/dailyGrid.php"; ?>

</body>
</html>
