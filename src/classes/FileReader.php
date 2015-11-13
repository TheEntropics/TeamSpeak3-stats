<?php

require_once __DIR__ . '/../../config/config.php';

class FileReader {
    private $logFiles;
    private $currentFile;
    private $currentHandle;

    public function __construct() {
        $this->logFiles = array();

        foreach(Config::LOG_FOLDERS as $i)
            $this->loadFolder($i);

        sort($this->logFiles);

        $this->currentFile = 0;
        $this->currentHandle = fopen($this->logFiles[0], "r");
    }

    public function getLine() {
        $line = fgets($this->currentHandle);

        if ($line == null) {
            fclose($this->currentHandle);
            $this->currentFile++;
            if ($this->currentFile == count($this->logFiles))
                return null;
            $this->currentHandle = fopen($this->logFiles[$this->currentFile], "r");

            $line = fgets($this->currentHandle);
        }
        return $line;
    }

    private function loadFolder($folder) {
        $d = scandir($folder);
        foreach ($d as $file) {
            if ($file == "." || $file == "..") continue;
            if (is_dir("$folder/$file"))
                loadFolder("$folder/$file");
            else if (Utils::endWith($file, ".log"))
                $this->logFiles[] = "$folder/$file";
        }
    }
}
