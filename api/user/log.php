<?php

require_once __DIR__ . '/../API.php';
API::init();

$client_id = API::getClientId();
$limit = API::getParam('limit', 10);
$offset = API::getParam('offset', 0);

$log = UserRangeVisualizer::getUserRanges($client_id, $limit, $offset);

API::printJSON($log);
