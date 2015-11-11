<?php

require_once __DIR__ . '/../../config/config.php';

class FileReader {
    private $logFiles;

    public function __construct() {
        $this->logFiles = array();

        foreach(Config::LOG_FOLDERS as $i) {
            $this->loadFolder($i);
        }
    }

    public function getLine() {

    }

    private function loadFolder($folder) {
        $d = scandir($folder);
        foreach ($d as $file) {
            if ($file == "." || $file == "..") continue;
            if (is_dir("$folder/$file"))
                loadFolder("$folder/$file");
            else
                $this->logFiles[] = "$folder/$file";
        }
    }
}
