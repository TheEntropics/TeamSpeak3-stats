<div ng-controller="DailyGridCtrl">
    <h2>
        Utenti connessi per fascia oraria
        <small><a href="" ng-click="reload()"><span class="glyphicon glyphicon-refresh"></span></a></small>

        <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </span>
    </h2>

    <div ng-show="errored">
        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Error
    </div>

    <table class="table text-center" ng-hide="errored">
        <tr>
            <th></th>
            <th ng-repeat="hh in hours">
                {{hh}}:00
            </th>
        </tr>
        <tr ng-repeat="row in rows">
            <td>{{row.day}}</td>
            <td ng-repeat="cell in row.cells" ng-style="{'background-color': cell.color}">
                {{cell.value}}
            </td>
        </tr>
    </table>
</div>
