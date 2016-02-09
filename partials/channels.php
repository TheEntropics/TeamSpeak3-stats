<div ng-controller="ChannelsCtrl">
    <h1>
        Elenco canali

        <span ng-show="loading" class="la-ball-grid-beat la-dark la-sm">
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
            <div></div><div></div><div></div>
        </span>
    </h1>

    <table ng-hide="loading" class="table table-responsive table-condensed">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Creato il</th>
            <th>Eliminato il</th>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="channel in channels">
                <td ng-bind-html="channel.name"></td>
                <td>{{Utils.formatDate(channel.created)}}</td>
                <td>{{Utils.formatDate(channel.deleted)}}</td>
            </tr>
        </tbody>
    </table>
</div>
