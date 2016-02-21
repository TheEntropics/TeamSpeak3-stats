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
            <td ng-repeat="cell in row.cells" ng-style="{'background-color': cell.value < 0 ? '#bbb' : cell.color}">
                <span ng-show="cell.value >= 0">{{cell.value.toFixed(2)}}</span>
                <span ng-show="cell.value < 0">???</span>
            </td>
        </tr>
    </table>

    <rzslider ng-hide="loading || errored" rz-slider-model="slider.minValue" rz-slider-high="slider.maxValue" rz-slider-options="slider.options"></rzslider>
</div>
