<div ng-controller="UserCtrl">
    <div ng-controller="UserInfoCtrl">
        <div class="pull-right" ng-show="info.online">
            <strong>Online</strong>
            da <span class="uptime">{{Utils.formatTime(info.uptime)}}</span>
        </div>
        <h2>
            Statistiche di
            <small ng-hide="loading" ng-bind-html="info.username"></small>
            <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
                <div></div><div></div><div></div>
                <div></div><div></div><div></div>
                <div></div><div></div><div></div>
            </span>
        </h2>
    </div>
    <?php require __DIR__ . '/../src/user/log.php' ?>
    <?php require __DIR__ . '/../src/user/username.php' ?>
    <?php require __DIR__ . '/../src/user/streak.php' ?>
    <?php require __DIR__ . '/../src/user/daily_graph.php' ?>
</div>
