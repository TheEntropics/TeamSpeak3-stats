<?php


class ClientConnectedEvent extends Event {
    public $type = EventType::ClientConnected;

    public $ip;
    public $user_id;
}