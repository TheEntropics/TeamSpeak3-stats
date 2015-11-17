<?php


class Logger {

    private static $logFile;

    public static function init() {
        Logger::$logFile = fopen(__DIR__ . "/../../" . Config::APP_LOG_FILE, "a");
        fputs(Logger::$logFile, "--------------------------------------\n");
        if (!CONSOLE && !QUIET)
            echo "<pre>";
    }

    public static function log(...$string) {
        $line = date("Y-m-d H:i:s") . " | " . implode(" ", $string);
        Logger::write($line);
    }

    private static function write($string) {
        if (!QUIET)
            if (CONSOLE) echo $string . "\n";
            else         echo $string . "<br>";
        if (Logger::$logFile)
            fputs(Logger::$logFile, $string . "\n");
    }
}
