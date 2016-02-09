<?php

require_once __DIR__ . '/../API.php';
API::init();

if (!Config::get("realtime.enabled")) {
    http_response_code(403);
    API::printJSON(array('error' => 'Realtime disabled'));
}

try {
    $ts3 = new Ts3ServerQuery(Config::get("realtime.host"), Config::get("realtime.port", 10011),
            Config::get("realtime.username", "serveradmin"), Config::get("realtime.password"));

    $users = RealtimeUsers::getOnlineUsers($ts3);
    $channels = RealtimeChannels::getChannels($ts3);
} catch (Exception $e) {
    http_response_code(500);
    API::printJSON(array('error' => 'Boh...'));
}

API::printJSON(RealtimeFormatter::getRealtime($users, $channels));
