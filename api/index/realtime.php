<?php

require_once __DIR__ . '/../API.php';
API::init();

if (!Config::REALTIME_ENABLED)
    die('Realtime not enabled');

try {
    $ts3 = new Ts3ServerQuery(Config::REALTIME_HOST, Config::REALTIME_PORT, Config::REALTIME_USER, Config::REALTIME_PASS);

    $users = RealtimeUsers::getOnlineUsers($ts3);
    $channels = RealtimeChannels::getChannels($ts3);
} catch (Exception $e) {
    die('Internal error... :(');
}

API::printJSON(RealtimeFormatter::getRealtime($users, $channels));
