<?php


class MainAnalyzer {
    public static function runAnalysis() {
        $analyzers = MainAnalyzer::loadAnalyzers();
        // TODO avviare l'analisi per ogni analizzatore
    }

    private static function loadAnalyzers() {
        /* TODO caricare i file nella cartella ../analyzers
         * filtrarli per enabled e ordinarli per priority */
    }
}