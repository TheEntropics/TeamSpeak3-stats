<div ng-controller="ScoreboardCtrl">
    <h2>
        Classifica per uptime
        <small><a href="" ng-click="reloadScoreboard()"><span class="glyphicon glyphicon-refresh"></span></a></small>

        <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </span>
    </h2>

    <div ng-show="errored">
        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Error
    </div>

    <ol ng-hide="errored">
        <li ng-repeat="user in users | orderBy:'-uptime'"
            ng-data-online="{{user.username}}"
            ng-data-online-since="{{user.username}}"
            ng-data-uptime="{{user.username}}">
            <a ng-href="#/user/{{user.client_id}}"><span ng-bind-html="user.username"></span></a>
            <span ng-show="user.online" class="text-success">(online)</span>
            <small class="uptime">{{Utils.formatTime(user.uptime)}}</small>
        </li>
    </ol>
    <a href="" ng-click="loadOthers()" ng-hide="errored">Carica altri...</a>
</div>
