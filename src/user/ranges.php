<?php
    $ranges = UserRangeVisualizer::getUserRanges($client_id, 10);
?>
<h3>Last connections</h3>
<table class="table table-responsive table-condensed">
    <thead>
        <tr>
            <th>Connected at</th>
            <th>Disconnected at</th>
            <th>Duration</th>
            <th>Username</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($ranges as $range) { ?>
        <tr>
            <td><?php echo $range['connect_date'] ?></td>
            <td><?php echo $range['disconnect_date'] ?></td>
            <td><?php echo UptimeVisualizer::formatTime($range['duration']) ?></td>
            <td><?php echo $range['username'] ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
