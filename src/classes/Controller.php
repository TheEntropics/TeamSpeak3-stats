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
        // TODO caricare tutte le classi
    }

    private static function updateCache() {
        // TODO aggiornare la cache con CacheService
    }

    private static function runAnalysis() {
        // TODO avviare l'analisi con MainAnalyzer
    }
}