<?php
    $counters = CounterVisualizer::getCounters();
?>

<div class="col-md-6">
    <h2>Conteggi</h2>
    <h4>Connessioni</h4>
    <dl class="dl-horizontal">
        <dt>Totali</dt>
        <dd><?php echo $counters['connectionCount'] ?></dd>
    </dl>
    <h4>Disconnessioni</h4>
    <dl class="dl-horizontal">
        <dt>Normali</dt>
        <dd><?php echo $counters['leavingCount'] ?></dd>
        <dt>Connessione persa</dt>
        <dd><?php echo $counters['connectionLostCount'] ?></dd>
        <dt><i>Altro</i></dt>
        <dd><?php echo $counters['othersDisconnectCount'] ?></dd>
        <dt></dt>
        <dd><?php echo $counters['total'] ?></dd>
    </dl>
    <h4>File manager</h4>
    <dl class="dl-horizontal">
        <dt>File caricati</dt>
        <dd><?php echo $counters['fileUploadCount'] ?></dd>
        <dt>File scaricati</dt>
        <dd><?php echo $counters['fileDownloadCount'] ?></dd>
        <dt>File cancellati</dt>
        <dd><?php echo $counters['fileDeletedCount'] ?></dd>
    </dl>
</div>
