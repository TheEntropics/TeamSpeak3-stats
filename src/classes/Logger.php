<?php


class Logger {

    private static $logFile;

    /**
     * Initialize the log file
     */
    public static function init() {
        Logger::$logFile = fopen(__DIR__ . "/../../" . Config::APP_LOG_FILE, "a");
        if (!QUIET)
            fputs(Logger::$logFile, "--------------------------------------\n");
        if (!CONSOLE && !QUIET)
            echo "<pre>";
    }

    /**
     * Log a new line of information
     * @param ...$string A list of "things" to save in the log. The items will be space separated
     */
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
