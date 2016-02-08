<div ng-controller="UserUsernameCtrl">
    <h3>Username utilizzati</h3>
    <ol>
        <li ng-repeat="user in usernames">
            {{user.username}}
            <small>{{Utils.formatTime(user.total_time)}}</small>
        </li>
    </ol>
</div>
