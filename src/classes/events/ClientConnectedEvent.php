<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/../User.php';

class ClientConnectedEvent extends Event {
    public $type = EventType::ClientConnected;

    /**
     * @var string
     */
    public $ip;

    public function __construct($matches) {
        $this->date = new DateTime($matches[1]);
        $this->ip = $matches[4];
        $username = $matches[2];
        $client_id = $matches[3];
        $this->user_id = User::findOrCreate($username, $client_id)->id;
    }

    protected function updateEvent() {
        $sql = "UPDATE client_connected_events SET date = :date, ip = :ip, user_id = :user_id WHERE id = :id";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("id", $this->id);
        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("ip", $this->ip);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
    }

    protected function createEvent() {
        $sql = "INSERT IGNORE INTO client_connected_events (date, ip, user_id)
                VALUE (:date, :ip, :user_id)";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("ip", $this->ip);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
        $this->id = DB::$DB->lastInsertId();
    }

    protected static function getInsertHeader() {
        return "INSERT IGNORE INTO client_connected_events (date, ip, user_id) VALUES ";
    }

    protected static function getInsertString($event) {
        return "('{$event->date->format("Y-m-d H:i:s.u")}', '{$event->ip}', {$event->user_id})";
    }
}
