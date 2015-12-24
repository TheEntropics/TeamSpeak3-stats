<?php
    $limit = 10;
    if (isset($_GET['limit']))
        $limit = intval($_GET['limit']);
    $ranges = UserRangeVisualizer::getUserRanges($client_id, $limit);
?>
<h3>Ultime <?php echo count($ranges) ?> connessioni</h3>
<table class="table table-responsive table-condensed">
    <thead>
        <tr>
            <th>Connesso il</th>
            <th>Disconnesso il</th>
            <th>Durata</th>
            <th>Username</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($ranges as $range) { ?>
        <tr>
            <td><?php echo (new DateTime($range['connect_date']))->format('d/m/Y \a\l\l\e H:i:s') ?></td>
            <td><?php echo (new DateTime($range['disconnect_date']))->format('d/m/Y \a\l\l\e H:i:s') ?></td>
            <td><?php echo UptimeVisualizer::formatTime($range['duration']) ?></td>
            <td><?php echo $range['username'] ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<a href="user.php?client-id=<?php echo $client_id ?>&limit=<?php echo 50*(int)(($limit+50)/50); ?>">Mostra altri...</a>
