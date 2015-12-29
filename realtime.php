<?php

require_once __DIR__ . '/src/classes/Controller.php';
Controller::init(true);

if (!Config::REALTIME_ENABLED)
    die('Realtime not enabled');

Controller::loadFolder(__DIR__ . '/src/classes/realtime');

try {
    $ts3 = new Ts3ServerQuery(Config::REALTIME_HOST, Config::REALTIME_PORT, Config::REALTIME_USER, Config::REALTIME_PASS);

    $users = RealtimeUsers::getOnlineUsers($ts3);
    $channels = RealtimeChannels::getChannels($ts3);
} catch (Exception $e) {
    print_r($e);
    die('Internal error... :(');
}

header('Content-Type: application/json');
echo RealtimeFormatter::getJSON($users, $channels);
