<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/CacheService.php';
require_once __DIR__ . '/MainAnalyzer.php';

class Controller {

    public static function run() {
        Controller::init();
        Controller::updateCache();
        Controller::runAnalysis();
    }

    private static function init() {
        Controller::initDB();
    }

    private static function updateCache() {
        CacheService::updateCache();
    }

    private static function runAnalysis() {
        MainAnalyzer::runAnalysis();
    }

    private static function loadFolder($folder) {
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
        } catch (PDOException $ex) {
            die("Error connecting to db");
        }
    }
}
