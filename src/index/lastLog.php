<div ng-controller="LogCtrl">
    <h2>
        Ultimi eventi
        <small><a href="" ng-click="reloadLog()"><span class="glyphicon glyphicon-refresh"></span></a></small>

        <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </span>
    </h2>

    <div ng-show="errored">
        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Error
    </div>

    <table class="table table-responsive table-condensed" ng-hide="errored">
        <thead>
            <tr>
                <th>Data</th>
                <th>Utente</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="log in logs">
                <td>{{Utils.formatDate(log.date)}}</td>
                <td><a href="user.php?client-id={{log.client_id}}" ng-bind-html="log.username"></a></td>
                <td>{{log.type}}</td>
            </tr>
        </tbody>
    </table>
    <a href="" ng-click="loadOthers()" ng-hide="errored">Carica altri...</a>
</div>
