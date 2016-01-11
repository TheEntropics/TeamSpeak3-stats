<?php
$online = OnlineRange::getOnlineRanges();
$is_online = isset($online[$client_id]);

if ($is_online) {
    $online_since = $online[$client_id]->start;
    $online_for = Utils::formatTime((new DateTime())->getTimestamp() - $online_since->getTimestamp());
?>
    <div class="pull-right">
        <strong>Online</strong>
        da <span class="uptime" data-online-since="<?php echo Utils::formatDate($online_since, 'r') ?>"><?php echo $online_for ?></span>
        <script src="js/user.js"></script>
    </div>
<?php } ?>
