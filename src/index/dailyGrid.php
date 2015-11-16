<?php
    $grid = DailyVisualizer::getGrid();

    $days = array(
        'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'
    );
?>
<h2>Utenti connessi per fascia oraria</h2>
<table class="table">
    <tr>
        <th></th>
        <?php for ($i = 0; $i < 24; $i++) { ?>
            <th><?php echo "$i:00"; ?></th>
        <?php } ?>
    </tr>
    <?php for ($day = 0; $day < 7; $day++) { ?>
        <tr>
            <td><?php echo $days[$day]; ?></td>
            <?php for ($i = 0; $i < 24; $i++) { ?>
                <td style="background-color: <?php echo $grid[$day][$i]['color'] ?>">
                    <?php echo $grid[$day][$i]['value'] ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>
