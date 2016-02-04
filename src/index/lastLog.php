<div ng-controller="LogCtrl">
    <h2>Ultimi eventi <small><a href="" ng-click="reloadLog()"><span class="glyphicon glyphicon-refresh"></span></a></small></h2>
    <div ng-show="loading" class="spinner" ng-style="{'background-color': spinnerColors[spinnerIndex]}"></div>

    <table class="table table-responsive table-condensed">
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
    <a href="" ng-click="loadOthers()">Carica altri...</a>
</div>
