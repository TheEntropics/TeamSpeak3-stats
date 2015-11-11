<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/ChannelEventType.php';
require_once __DIR__ . '/../User.php';

class ChannelEvent extends Event {
    public $type = EventType::Channel;

    public $name;
    public $channelType;

    public function __construct($matches) {
        $this->date = new DateTime($matches[1]);
        $this->name = $matches[2];
        $username = $matches[4];
        $client_id = $matches[5];
        $this->user_id = User::findOrCreate($username, $client_id)->id;
        switch ($matches[3]) {
            case "created": $this->channelType = ChannelEventType::ChannelCreatedEvent; break;
            case "deleted": $this->channelType = ChannelEventType::ChannelDeletedEvent; break;
        }
    }
}
