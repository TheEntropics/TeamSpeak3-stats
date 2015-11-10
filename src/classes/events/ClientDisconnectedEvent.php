<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';

class ClientDiconnectedEvent extends Event {
    public $type = EventType::ClientDisconnected;

    public $ip;
    public $reason;
}
