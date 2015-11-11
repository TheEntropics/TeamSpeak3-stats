<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/../User.php';

class ClientConnectedEvent extends Event {
    public $type = EventType::ClientConnected;

    public $ip;

    public function __construct($matches) {
        $this->date = new DateTime($matches[1]);
        $this->ip = $matches[4];
        $username = $matches[2];
        $client_id = $matches[3];
        $this->user_id = User::findOrCreate($username, $client_id)->id;
    }
}
