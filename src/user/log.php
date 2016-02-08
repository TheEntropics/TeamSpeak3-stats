<div ng-controller="UserLogCtrl">
    <h3>
        Ultime {{logs.length}} connessioni

        <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </span>
    </h3>

    <div ng-show="errored">
        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Error
    </div>

    <table class="table table-responsive table-condensed" ng-hide="errored">
        <thead>
            <tr>
                <th>Connesso il</th>
                <th>Disconnesso il</th>
                <th>Durata</th>
                <th>Username</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="log in logs">
                <td>{{Utils.formatDate(log.connect_date)}}</td>
                <td>{{Utils.formatDate(log.disconnect_date)}}</td>
                <td>{{Utils.formatTime(log.duration)}}</td>
                <td>{{log.username}}</td>
            </tr>
        </tbody>
    </table>

    <a href="" ng-click="loadOthers()" ng-hide="errored">Carica altri...</a>
</div>
