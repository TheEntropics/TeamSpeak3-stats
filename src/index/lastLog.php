<?php

$logs = LogVisualizer::getLastLog();

?>
<h2>Ultimi eventi</h2>
<table class="table table-responsive table-condensed">
    <thead>
        <tr>
            <th>Data</th>
            <th>Utente</th>
            <th>Tipo</th>
        </tr>
    </thead>
    <?php foreach ($logs as $log) { ?>
        <tr>
            <td><?php echo Utils::formatDate($log['date']) ?></td>
            <td><a href="user.php?client-id=<?php echo $log['client_id2'] ?>"><?php echo $log['username'] ?></a></td>
            <td><?php echo $log['type'] ?></td>
        </tr>
    <?php } ?>
</table>
