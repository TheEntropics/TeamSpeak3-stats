<?php

$streak = StreakVisualizer::getStreak($client_id);

?>
<h2>Serie</h2>
<dl class="dl-horizontal">
    <dt>Serie più lunga</dt>
    <dd><?php echo $streak['longest'] ?></dd>
    <dt>Dal</dt>
    <dd><?php echo Utils::formatDate($streak['startLongest'], 'd/m/Y') ?></dd>
    <dt>Serie corrente</dt>
    <dd><?php echo $streak['current'] ?></dd>
    <?php if ($streak['current'] > 0) { ?>
        <dt>Dal</dt>
        <dd><?php echo Utils::formatDate($streak['startCurrent'], 'd/m/Y') ?></dd>
    <?php } ?>
</dl>
