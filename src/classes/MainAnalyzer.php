<?php


class MainAnalyzer {
    public static function runAnalysis() {
        $analyzers = MainAnalyzer::loadAnalyzers();
        foreach ($analyzers as $analyzer) {
            $analyzer::runAnalysis();
        }
    }

    private static function loadAnalyzers() {
        $analyzers = MainAnalyzer::getAnalyzers(__DIR__ . '/analyzers');
        $classes = array();
        foreach ($analyzers as $analyzer) {
            require_once $analyzer[0];
            $analyzerName = substr($analyzer[1], 0, -4);

            if (class_exists($analyzerName) || !in_array("BaseAnalyzer", class_parents($analyzerName)))
                if ($analyzerName::$enabled)
                    $classes[] = $analyzerName;
        }
        usort($classes, "MainAnalyzer::analyzerCmp");
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
