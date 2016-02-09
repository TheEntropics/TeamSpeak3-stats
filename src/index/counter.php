<div ng-controller="CounterCtrl">
    <h2>
        Statistiche
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

    <div ng-hide="errored">
        <h4>Connessioni</h4>
        <dl class="dl-horizontal">
            <dt>Totali</dt>
            <dd>{{counter.connectionCount}}</dd>
        </dl>

        <h4>Disconnessioni</h4>
        <dl class="dl-horizontal">
            <dt>Normali</dt>
            <dd>{{counter.leavingCount}}</dd>
            <dt>Connessione persa</dt>
            <dd>{{counter.connectionLostCount}}</dd>
            <dt><i>Altro</i></dt>
            <dd>{{counter.othersDisconnectCount}}</dd>
            <dt></dt>
            <dd>{{counter.total}}</dd>
        </dl>

        <h4>File manager</h4>
        <dl class="dl-horizontal">
            <dt>File caricati</dt>
            <dd>{{counter.fileUploadCount}}</dd>
            <dt>File scaricati</dt>
            <dd>{{counter.fileDownloadCount}}</dd>
            <dt>File cancellati</dt>
            <dd>{{counter.fileDeletedCount}}</dd>
        </dl>

        <h4><a href="#/channels">Canali</a></h4>
        <dl class="dl-horizontal">
            <dt>Canali creati</dt>
            <dd>{{counter.channelCreated}}</dd>
            <dt>Canali eliminati</dt>
            <dd>{{counter.channelDeleted}}</dd>
        </dl>

        <h4>Picco utenti</h4>
        <dl class="dl-horizontal">
            <dt>Utenti massimi</dt>
            <dd>{{counter.maxOnline}}</dd>
            <dt>il</dt>
            <dd>{{Utils.formatDate(counter.maxOnlineTime + ' UTC')}}</dd>
        </dl>
    </div>
</div>
