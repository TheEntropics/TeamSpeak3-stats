<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/FileManagerEventType.php';
require_once __DIR__ . '/../User.php';

class FileManagerEvent extends Event {
    public $type = EventType::FileManager;

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
}
