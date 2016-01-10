<?php

abstract class BaseAnalyzer {

    public static $enabled = true;
    public static $fast = true;
    public static $priority = 100;

    public static abstract function runAnalysis();
}
