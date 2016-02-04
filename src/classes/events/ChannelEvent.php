<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/ChannelEventType.php';
require_once __DIR__ . '/../User.php';

class ChannelEvent extends Event {
    public $type = EventType::Channel;

    /**
     * @var string
     */
    public $name;
    /**
     * @var int|ChannelEventType
     */
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

    protected function updateEvent() {
        $sql = "UPDATE channel_events SET date = :date, type = :type, name = :name, user_id = :user_id WHERE id = :id";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("id", $this->id);
        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("type", $this->channelType);
        $query->bindParam("name", $this->name);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
    }

    protected function createEvent() {
        $sql = "INSERT IGNORE INTO channel_events (date, type, name, user_id)
                VALUE (:date, :type, :name, :user_id)";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("type", $this->channelType);
        $query->bindParam("name", $this->name);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
        $this->id = DB::$DB->lastInsertId();
    }

    protected static function getInsertHeader() {
        return "INSERT IGNORE INTO channel_events (date, type, name, user_id) VALUES ";
    }
    protected static function getInsertString($event) {
        return "('{$event->date->format("Y-m-d H:i:s.u")}', {$event->channelType}, '{$event->name}', {$event->user_id})";
    }
}
