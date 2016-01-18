<?php

require_once __DIR__ . '/../API.php';
API::init();

$limit = API::getParam('limit', 10);
$offset = API::getParam('offset', 0);

$log = LogVisualizer::getLastLog($limit, $offset);

API::printJSON($log);
