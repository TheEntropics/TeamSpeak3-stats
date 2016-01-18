<?php

require_once __DIR__ . '/../API.php';
API::init();

$client_id = API::getParam('client_id');
$limit = API::getParam('limit', 10);
$offset = API::getParam('offset', 0);

if (!$client_id) {
    http_response_code(400);
    API::printJSON([ "error" => "Please specify a client_id" ]);
    return;
}

$usernames = UsernameUptimeVisualizer::getUsernameUptime($client_id, $limit, $offset);

API::printJSON($usernames);
