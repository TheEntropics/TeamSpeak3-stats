<?php

require_once __DIR__ . '/../API.php';
API::init();

$client_id = API::getClientId();

$streak = StreakVisualizer::getStreak($client_id);

API::printJSON($streak);
