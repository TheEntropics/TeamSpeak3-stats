<?php

require_once __DIR__ . '/../API.php';
API::init();

$counts = CounterVisualizer::getCounters();

API::printJSON($counts);
