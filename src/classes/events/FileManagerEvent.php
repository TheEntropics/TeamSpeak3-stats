<?php

require_once __DIR__ . '/Event.php';
require_once __DIR__ . '/EventType.php';
require_once __DIR__ . '/FileManagerEventType.php';

class FileManagerEvent extends Event {
    public $type = EventType::FileManager;

    public $fileManagerType;
}
