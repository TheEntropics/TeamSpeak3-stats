<?php


class Logger {

    private static $logFile;

    const LOG_FULL_FILE_NAME = __DIR__ . "/../../" . Config::APP_LOG_FILE;

    /**
     * Initialize the log file
     */
    public static function init() {
        Logger::$logFile = fopen(Logger::LOG_FULL_FILE_NAME, "a");
        if (!QUIET)
            fputs(Logger::$logFile, "--------------------------------------\n");
        if (!CONSOLE && !QUIET)
            echo "<pre>";
        set_error_handler("Logger::error_handler", E_ALL);
        set_exception_handler("Logger::exception_handler");
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
        if (!QUIET && Logger::$logFile)
            fputs(Logger::$logFile, $string . "\n");
    }

    public static function error_handler($errno, $errstr, $errfile, $errline) {
        Logger::log("AN ERROR OCCURRED: ", $errno, "[", $errfile, ":", $errline, "]");
        Logger::log($errstr);
    }

    public static function exception_handler($ex) {
        Logger::log("AN EXCEPTION OCCURRED:");
        Logger::log($ex);
    }
}
