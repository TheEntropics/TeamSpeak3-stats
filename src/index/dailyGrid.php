<div ng-controller="DailyGridCtrl">
    <h2>Utenti connessi per fascia oraria <small><a href="" ng-click="reload()"><span class="glyphicon glyphicon-refresh"></span></a></small></h2>
    <div ng-show="loading" class="spinner" ng-style="{'background-color': spinnerColors[spinnerIndex]}"></div>

    <table class="table text-center">
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
