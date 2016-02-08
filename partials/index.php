<?php

require_once __DIR__ . '/../config/config.php';

?>
<div ng-controller="IndexCtrl">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <?php include __DIR__ . "/../src/index/scoreboard.php"; ?>
            <?php include __DIR__ . "/../src/index/lastLog.php"; ?>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <?php include __DIR__ . "/../src/index/counter.php"; ?>
            <?php if (Config::REALTIME_ENABLED) include __DIR__ . "/../src/index/realtime.php"; ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php include __DIR__ . "/../src/index/dailyGrid.php"; ?>

    <?php include __DIR__ . "/../src/index/footer.php"; ?>
</div>
