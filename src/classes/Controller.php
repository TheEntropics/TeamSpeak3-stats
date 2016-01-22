<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/CacheService.php';
require_once __DIR__ . '/MainAnalyzer.php';
require_once __DIR__ . '/Logger.php';

class Controller {

    private static $alreadyInited = false;

    public static function run($runAnalysis = true, $fastOnly = false) {
        Controller::init();
        Logger::log("Controller avviato");
        $count = Controller::updateCache();
        Logger::log($count, "nuovi eventi nei log");
        if ($runAnalysis) {
            if ($count > 0 || Config::DEBUG || Utils::getMiscResult("pending_analysis") == "yes")
                Controller::runAnalysis($fastOnly);
            else
                Logger::log("Nessuna azione eseguita");
        } else {
            if ($count > 0 || Config::DEBUG) {
                Utils::saveMiscResult("pending_analysis", "yes");
                if ($fastOnly) {
                    Logger::log("Running fast-only analysis even if \$runAnalysis is false");
                    Controller::runAnalysis($fastOnly);
                } else
                    Logger::log("Analysis skipped...");
            } else
                Logger::log("Nessuna azione eseguita");
        }
    }

    public static function init($quiet = false) {
        if (Controller::$alreadyInited) return;

        global $argv;
        if (!isset($argv) || isset($_SERVER['REQUEST_METHOD']))
            define("CONSOLE", false);
        else
            define("CONSOLE", true);
        define("QUIET", $quiet);
        Logger::init();
        date_default_timezone_set("UTC");
        Controller::initDB();
        Controller::loadClasses();
        Controller::$alreadyInited = true;
    }

    public static function updateCache() {
        return CacheService::updateCache();
    }

    private static function runAnalysis($fastOnly = false) {
        MainAnalyzer::runAnalysis($fastOnly);
        if ($fastOnly == false) {
            Utils::saveMiscResult("lastDate", date('Y-m-d H:i:s'));
            Utils::saveMiscResult("pending_analysis", "no");
        }
    }


    private static function loadClasses() {
        Controller::loadFolder(__DIR__);
        Controller::loadFolder(__DIR__ . '/events');
    }

    public static function loadFolder($folder) {
        $d = scandir($folder);
        foreach ($d as $file) {
            if ($file == "." || $file == "..") continue;
            if (is_dir("$folder/$file"))
                Controller::loadFolder("$folder/$file");
            else
                require_once "$folder/$file";
        }
    }

    private static function initDB() {
        try {
            DB::$DB = new PDO(Config::DB_STRING, Config::USERNAME, Config::PASSWORD);
            DB::$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            DB::$DB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $ex) {
            Logger::log($ex->getMessage());
            Logger::log($ex->getTraceAsString());
            die("Error connecting to db");
        }
    }
}
