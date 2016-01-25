<?php

abstract class BaseAnalyzer {

    /**
     * @var bool The analyzer is enabled
     */
    public static $enabled = true;
    /**
     * @var bool The analyzer is pretty fast
     */
    public static $fast = true;
    /**
     * @var int Priority of the analyzer, greater values have greater priority
     */
    public static $priority = 100;

    /**
     * Run the analysis
     */
    public static abstract function runAnalysis();
}
