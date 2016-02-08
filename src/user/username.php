<div ng-controller="UserUsernameCtrl">
    <h3>Username utilizzati</h3>
    <ol>
        <li ng-repeat="user in usernames">
            <span ng-bind-html="user.username"></span>
            <small>{{Utils.formatTime(user.total_time)}}</small>
        </li>
    </ol>
</div>
