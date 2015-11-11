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

    public function saveEvent() {
        if ($this->id) $this->updateEvent();
        else $this->createEvent();
    }

    private function updateEvent() {
        $sql = "UPDATE channel_events SET date = :date, type = :type, name = :name, user_id = :user_id WHERE id = :id";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("id", $this->id);
        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("type", $this->type);
        $query->bindParam("name", $this->name);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
    }

    private function createEvent() {
        $sql = "INSERT INTO channel_events (date, type, name, user_id)
                VALUE (:date, :type, :name, :user_id)";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("type", $this->type);
        $query->bindParam("name", $this->name);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
        $this->id = DB::$DB->lastInsertId();
    }
}
