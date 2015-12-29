<?php

$usernames = UsernameUptimeVisualizer::getUsernameUptime($client_id);

?>
<h3>Username utilizzati</h3>
<ol>
    <?php foreach($usernames as $row) { ?>
        <li><?php echo $row['username'] ?> <small><?php echo Utils::formatTime($row['total_time']) ?></small></li>
    <?php } ?>
</ol>
