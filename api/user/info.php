<?php

require_once __DIR__ . '/../API.php';
API::init();

$client_id = API::getClientId();

$username = ProbableUsernameVisualizer::getProbableUsername($client_id);
$online = OnlineRange::getOnlineRanges();
$is_online = isset($online[$client_id]);
if ($is_online) {
    $online_since = $online[$client_id]->start;
    $online_for = (new DateTime())->getTimestamp() - $online_since->getTimestamp();
} else {
    $online_since = null;
    $online_for = 0;
}

$info = [
    "client_id" => $client_id,
    "username" => $username,
    "online" => $is_online,
    "online_since" => $online_since,
    "online_for" => $online_for
];

API::printJSON($info);
