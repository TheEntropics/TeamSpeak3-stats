<?php
    $scoreboard = UptimeVisualizer::getUptimeScoreboard();
?>
<h2>Classifica per uptime</h2>
<ol>
    <?php for ($i = 0; $i < 10; $i++) { ?>
        <li>
            <?php echo $scoreboard[$i]['username'] ?>
            <small><?php echo $scoreboard[$i]['score'] ?></small>
        </li>
    <?php } ?>
</ol>
<div id="extended-scoreboard" class="collapse">
    <ol start="11">
        <?php for ($i = 10; $i < count($scoreboard); $i++) { ?>
            <li>
                <?php echo $scoreboard[$i]['username'] ?>
                <small><?php echo $scoreboard[$i]['score'] ?></small>
            </li>
        <?php } ?>
    </ol>
</div>
<a href="#extended-scoreboard" data-toggle="collapse" aria-expanded="false" aria-controls="extended-scoreboard">
    Mostra/nascondi altri
</a>
