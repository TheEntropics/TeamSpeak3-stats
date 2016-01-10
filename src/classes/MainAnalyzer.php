<?php


class MainAnalyzer {
    public static function runAnalysis($fastOnly = false) {
        $analyzers = MainAnalyzer::loadAnalyzers($fastOnly);

        $analysisStart = microtime(true);

        Logger::log("Preparing ranges:");
        OnlineRange::getRanges();

        foreach ($analyzers as $analyzer) {
            Logger::log("Avviato $analyzer");

            $startTime = microtime(true);
            $analyzer::runAnalysis();
            $endTime = microtime(true);

            Logger::log("    Tempo impiegato:", $endTime-$startTime);
        }
        $analysisEnd = microtime(true);

        Logger::log("Fine analisi. Tempo impiegato:", $analysisEnd-$analysisStart);
    }

    private static function loadAnalyzers($fastOnly = false) {
        $analyzers = MainAnalyzer::getAnalyzers(__DIR__ . '/analyzers');
        $classes = array();
        foreach ($analyzers as $analyzer) {
            require_once $analyzer[0];
            $analyzerName = substr($analyzer[1], 0, -4);

            if (class_exists($analyzerName) || !in_array("BaseAnalyzer", class_parents($analyzerName)))
                // skip disabled analyzers and slow analyzers (if $fastOnly is true)
                if ($analyzerName::$enabled && (!$fastOnly || $analyzerName::$fast))
                    $classes[] = $analyzerName;
        }
        usort($classes, "MainAnalyzer::analyzerCmp");

        Logger::log("Analisi da eseguire: ");
        foreach ($classes as $class)
            Logger::log("   ", $class);

        return $classes;
    }

    private static function analyzerCmp($analyzerA, $analyzerB) {
        $priorityA = $analyzerA::$priority;
        $priorityB = $analyzerB::$priority;

        if ($priorityA == $priorityB) return 0;
        return ($priorityA < $priorityB) ? 1 : -1;
    }

    private static function getAnalyzers($folder) {
        $d = scandir($folder);
        $analyzers = array();
        foreach ($d as $file) {
            if ($file == "." || $file == "..") continue;
            if (is_dir("$folder/$file"))
                $analyzers = array_merge($analyzers, MainAnalyzer::loadFolder("$folder/$file"));
            else
                $analyzers[] = array("$folder/$file", $file);
        }
        return $analyzers;
    }
}
