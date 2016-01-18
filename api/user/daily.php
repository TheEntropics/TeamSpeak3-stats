<?php

require_once __DIR__ . '/../API.php';
API::init();

$client_id = API::getClientId();

$daily = DailyUserUptimeVisualizer::getDailyUserUptime($client_id);

API::printJSON($daily);
