<div ng-controller="CounterCtrl">
    <h2>Statistiche <small><a href="" ng-click="reload()">(Reload)</a></small></h2>

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

    <h4><a href="channels.php">Canali</a></h4>
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
