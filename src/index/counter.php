<?php
    $counters = CounterVisualizer::getCounters();
?>
<paper-card class="counter">
    <div class="card-content">
        <h5>Connessioni</h5>
        <h2><?php echo $counters['connectionCount'] ?></h2>
    </div>
</paper-card>

<paper-card class="counter">
    <div class="card-content">
        <h5>Disconnessioni</h5>
        <h2><?php echo $counters['total'] ?></h2>
        <p>Normali: <?php echo $counters['leavingCount'] ?></p>
        <p>Connessione persa: <?php echo $counters['connectionLostCount'] ?></p>
        <p>Altro: <?php echo $counters['othersDisconnectCount'] ?></p>
    </div>
</paper-card>

<paper-card class="counter">
    <div class="card-content">
        <h5>Utenti massimi</h5>
        <h2><?php echo $counters['maxOnline'] ?></h2>
        <p><?php echo Utils::formatDate($counters['maxOnlineTime']) ?></p>
    </div>
</paper-card>
