<?php

class ClientDiconnectedEvent extends Event {
    public $type = EventType::ClientDisconnected;

    public $ip;
    public $reason;
}