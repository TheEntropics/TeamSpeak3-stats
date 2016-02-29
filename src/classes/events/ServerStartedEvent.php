<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';

class ServerStartedEvent extends Event {
    public $type = EventType::ServerStarted;

    public function __construct($date) {
        $this->date = $date;
    }

    /**
     * Update the event using the stored id
     */
    protected function updateEvent() {
        $sql = "UPDATE server_started_events SET date = :date";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("id", $this->id);
        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));

        $query->execute();
    }

    /**
     * Create the new event
     */
    protected function createEvent() {
        $sql = "INSERT IGNORE INTO server_started_events (date)
                VALUE (:date)";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));

        $query->execute();
        $this->id = DB::$DB->lastInsertId();
    }

    protected static function getInsertHeader() {
        return "INSERT IGNORE INTO server_started_events (date) VALUES ";
    }

    protected static function getInsertString($event) {
        return "('{$event->date->format("Y-m-d H:i:s.u")}')";
    }
}
