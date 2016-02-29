<?php

require_once __DIR__ . '/../../config/config.php';

class FileReader {
    private $logFiles;
    private $currentFile;
    private $currentHandle;
    private $lastFile;

    /**
     * @param null|DateTime $lastDate Skip all the files before the specified date
     */
    public function __construct($lastDate = null) {
        $this->logFiles = array();

        foreach(Config::get("input") as $i)
            $this->loadFolder($i);

        sort($this->logFiles);

        $this->currentFile = $lastDate ? $this->skipFiles($lastDate) : 0;
        Logger::log("    Skipped {$this->currentFile} log files");
        $this->currentHandle = fopen($this->logFiles[$this->currentFile], "r");
        $this->newFileFlag = "";
    }

    /**
     * @return null|string The next line to read, null on end
     */
    public function getLine() {
        if ($this->logFiles[$this->currentFile] != $this->lastFile) {
            $this->lastFile = $this->logFiles[$this->currentFile];
            return FileReader::getDateFromFilename($this->lastFile);
        }

        $line = fgets($this->currentHandle);

        if ($line == null) {
            fclose($this->currentHandle);
            $this->currentFile++;
            if ($this->currentFile == count($this->logFiles))
                return null;

            $this->currentHandle = fopen($this->logFiles[$this->currentFile], "r");

            $this->lastFile = $this->logFiles[$this->currentFile];
            return FileReader::getDateFromFilename($this->lastFile);
        }
        return $line;
    }

    private function loadFolder($folder) {
        $d = scandir($folder);
        foreach ($d as $file) {
            if ($file == "." || $file == "..") continue;
            if (is_dir("$folder/$file"))
                loadFolder("$folder/$file");
            else if (Utils::endWith($file, "_" . Config::get("virtual_server") . ".log"))
                $this->logFiles[] = "$folder/$file";
        }
    }

    private function skipFiles($lastDate) {
        for ($i = 0; $i < count($this->logFiles); $i++) {
            if ($this->getDateFromFilename($this->logFiles[$i]) > $lastDate)
                break;
        }
        return max($i-1, 0);
    }

    private function getDateFromFilename($file) {
        $matches = array();
        preg_match('/ts3server_([^_]+)__(\d+)_(\d+)_(\d+)\.(\d+)_\d+\.log/', $file, $matches);
        return new DateTime($matches[1] . "T" . $matches[2] . ":" . $matches[3] . ":" . $matches[4] . "." . $matches[5]);
    }
}
