<?php
    $disconnected = DisconnectedVisualizer::getDisconnected();
?>

<div class="col-md-6">
    <h2>Conteggio disconnessioni</h2>
    <dl class="dl-horizontal">
        <dt>Normali</dt>
        <dd><?php echo $disconnected['leavingCount'] ?></dd>
        <dt>Connessione persa</dt>
        <dd><?php echo $disconnected['connectionLostCount'] ?></dd>
        <dt><i>Altro</i></dt>
        <dd><?php echo $disconnected['othersDisconnectCount'] ?></dd>
        <dt></dt>
        <dd><?php echo $disconnected['total'] ?></dd>
    </dl>
</div>
