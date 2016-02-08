<div ng-controller="UserStreakCtrl">
    <h2>
        Serie

        <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </span>
    </h2>

    <div ng-show="errored">
        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Error
    </div>

    <dl ng-hide="loading || errored" class="dl-horizontal">
        <dt>Serie più lunga</dt>
        <dd>{{streak.longest}}</dd>
        <dt>Dal</dt>
        <dd>{{Utils.formatShortDate(streak.startLongest)}}</dd>
        <dt>Serie corrente</dt>
        <dd>{{streak.current}}</dd>
        <div ng-show="streak.current > 0">
            <dt>Dal</dt>
            <dd>{{Utils.formatShortDate(streak.startCurrent)}}</dd>
        </div>
    </dl>
</div>
