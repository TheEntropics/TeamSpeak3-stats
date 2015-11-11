<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/../User.php';

class ClientDisconnectedEvent extends Event {
    public $type = EventType::ClientDisconnected;

    public $reason;

    public function __construct($matches) {
        $this->date = new DateTime($matches[1]);
        $username = $matches[2];
        $client_id = $matches[3];
        $this->user_id = User::findOrCreate($username, $client_id)->id;
        // ban time = $matches[6]
        if (count($matches) > 5)
            $this->reason = "Ban";
        else
            $this->reason = $matches[4];
    }
}
