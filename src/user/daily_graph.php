<div ng-controller="UserDailyGraphCtrl">
    <h3>
        Uptime giornaliero

        <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </span>
    </h3>

    <div ng-show="errored">
        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Error
    </div>
    <div ng-hide="errored" google-chart chart="chartObject" style="height:350px; width:100%;"></div>
</div>
