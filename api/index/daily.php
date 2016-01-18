<?php

require_once __DIR__ . '/../API.php';
API::init();

$averages = DailyVisualizer::getGrid();

API::printJSON($averages);
