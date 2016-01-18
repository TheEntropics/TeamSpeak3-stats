<?php

require_once __DIR__ . '/../API.php';
API::init();

$client_id = API::getParam('client_id');

if (!$client_id) {
    http_response_code(400);
    API::printJSON([ "error" => "Please specify a client_id" ]);
    return;
}

$streak = StreakVisualizer::getStreak($client_id);

API::printJSON($streak);
