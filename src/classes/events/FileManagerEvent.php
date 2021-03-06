<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/FileManagerEventType.php';
require_once __DIR__ . '/../User.php';

class FileManagerEvent extends Event {
    public $type = EventType::FileManager;

    /**
     * @var int
     */
    public $fileManagerType;

    public function __construct($matches) {
        $this->date = new DateTime($matches[1]);
        $username = $matches[3];
        $client_id = $matches[4];
        $this->user_id = User::findOrCreate($username, $client_id)->id;
        switch ($matches[2]) {
            case "upload": $this->fileManagerType = FileManagerEventType::Upload; break;
            case "download": $this->fileManagerType = FileManagerEventType::Download; break;
            case "deleted": $this->fileManagerType = FileManagerEventType::Delete; break;
        }
    }

    protected function updateEvent() {
        $sql = "UPDATE file_manager_events SET date = :date, type = :type, user_id = :user_id WHERE id = :id";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("id", $this->id);
        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("type", $this->fileManagerType);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
    }

    protected function createEvent() {
        $sql = "INSERT IGNORE INTO file_manager_events (date, type, user_id)
                VALUE (:date, :type, :user_id)";
        $query = DB::$DB->prepare($sql);

        $query->bindParam("date", $this->date->format("Y-m-d H:i:s.u"));
        $query->bindParam("type", $this->fileManagerType);
        $query->bindParam("user_id", $this->user_id);

        $query->execute();
        $this->id = DB::$DB->lastInsertId();
    }

    protected static function getInsertHeader() {
        return "INSERT IGNORE INTO file_manager_events (date, type, user_id) VALUES ";
    }

    protected static function getInsertString($event) {
        return "('{$event->date->format("Y-m-d H:i:s.u")}', {$event->fileManagerType}, {$event->user_id})";
    }
}
