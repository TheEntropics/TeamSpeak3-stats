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
            <td><?php echo Utils::formatDate($range['connect_date']) ?></td>
            <td><?php echo Utils::formatDate($range['disconnect_date']) ?></td>
            <td><?php echo Utils::formatTime($range['duration']) ?></td>
            <td><?php echo $range['username'] ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<a href="user.php?client-id=<?php echo $client_id ?>&limit=<?php echo 50*(int)(($limit+50)/50); ?>">Mostra altri...</a>
