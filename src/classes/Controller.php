<?php

require_once __DIR__ . '/../../config/config.php';

class Controller {
    public static function run() {
        Controller::init();
        Controller::updateCache();
        Controller::runAnalysis();
    }

    private static function init() {
        // TODO caricare il database
        Controller::loadFiles();
    }

    private static function updateCache() {
        // TODO aggiornare la cache con CacheService
    }

    private static function runAnalysis() {
        // TODO avviare l'analisi con MainAnalyzer
    }

    private static function loadFiles() {
        Controller::loadFolder(__DIR__);
        Controller::loadFolder(__DIR__ . '/events');
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
}
