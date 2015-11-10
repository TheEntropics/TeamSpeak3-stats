<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/ChannelEvent.php';

class ChannelEvent extends Event {
    public $channelType = EventType::Channel;

    public $name;
    public $user_id;
}
