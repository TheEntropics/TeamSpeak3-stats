<?php

require_once __DIR__ . '/../API.php';
API::init();

$lastDate = FooterVisualizer::getLastDate();

API::printJSON($lastDate);
