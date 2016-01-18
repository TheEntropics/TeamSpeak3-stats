<?php

require_once __DIR__ . '/../API.php';
API::init();

$limit = API::getParam('limit', 50);
$offset = API::getParam('offset', 0);

$scoreboard = UptimeVisualizer::getUptimeScoreboard($limit, $offset);

API::printJSON($scoreboard);
