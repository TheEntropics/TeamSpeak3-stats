<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';

class ClientConnectedEvent extends Event {
    public $type = EventType::ClientConnected;

    public $ip;
}
