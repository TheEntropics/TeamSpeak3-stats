<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/../User.php';

class ClientDisconnectedEvent extends Event {
    public $type = EventType::ClientDisconnected;

    /**
     * @var string
     */
    public $reason;

    public function __construct($matches) {
        $this->date = new DateTime($matches[1]);
        $username = $matches[2];
        $client_id = $matches[3];
        $this->user_id = User::findOrCreate($username, $client_id)->id;
        // ban time = $matches[6]
        if (count($matches) == 4 || count($matches) > 5)
            $this->reason = "Ban";
        else
            $this->reason = $matches[4];
    }

    protected function updateEvent() {
        $sql = "UPDATE client_disconnected_events SET date = :date, reason = :reason, user_id = :user_id WHERE id = :id";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("id", $this->id);
        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("reason", $this->reason);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
    }

    protected function createEvent() {
        $sql = "INSERT INTO client_disconnected_events (date, reason, user_id)
                VALUE (:date, :reason, :user_id)";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("reason", $this->reason);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
        $this->id = DB::$DB->lastInsertId();
    }

    protected static function getInsertHeader() {
        return "INSERT INTO client_disconnected_events (date, reason, user_id) VALUES ";
    }

    protected static function getInsertString($event) {
        return "('{$event->date->format("Y-m-d H:i:s.u")}', '{$event->reason}', {$event->user_id})";
    }
}
