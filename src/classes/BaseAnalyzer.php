<?php

abstract class BaseAnalyzer {

    public $enabled = true;
    public $priority = 100;

    public static abstract function runAnalysis();
}