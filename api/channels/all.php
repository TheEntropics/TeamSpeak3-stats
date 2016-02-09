<?php

require_once __DIR__ . '/../API.php';
API::init();

$channels = ChannelVisualizer::getChannels();

API::printJSON($channels);
