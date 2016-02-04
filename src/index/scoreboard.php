<div ng-controller="ScoreboardCtrl">
    <h2>Classifica per uptime <small><a href="" ng-click="reloadScoreboard()"><span class="glyphicon glyphicon-refresh"></span></a></small></h2>
    <div ng-show="loading" class="spinner" ng-style="{'background-color': spinnerColors[spinnerIndex]}"></div>
    <ol>
        <li ng-repeat="user in users | orderBy:'-uptime'"
            ng-data-online="{{user.username}}"
            ng-data-online-since="{{user.username}}"
            ng-data-uptime="{{user.username}}">
            <a href="user.php?client-id={{user.client_id}}"><span ng-bind-html="user.username"></span></a>
            <span ng-show="user.online" class="text-success">(online)</span>
            <small class="uptime">{{Utils.formatTime(user.uptime)}}</small>
        </li>
    </ol>
    <a href="" ng-click="loadOthers()" ng-hide="loading">Carica altri...</a>
</div>
