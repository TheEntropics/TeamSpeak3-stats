<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';

class ClientDisconnectedEvent extends Event {
    public $type = EventType::ClientDisconnected;

    public $reason;
}
