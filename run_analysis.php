<?php

require_once __DIR__ . "/src/classes/Atomic.php";
require_once __DIR__ . "/src/classes/Controller.php";

$options = getopt("hufdp:", array("passcode:", "update", "fast", "debug"));

if (isset($options['h'])) {
    echo "Run the analysis of TeamSpeak-stats" . PHP_EOL;
    echo "Written by Edoardo Morassutto <edoardo.morassutto@gmail.com>" . PHP_EOL;
    echo "" . PHP_EOL;
    echo "Usage: php run_analysis.php [-h] [-u|--update] [-f|--fast]" . PHP_EOL;
    echo "           [-d|--debug] [-p passcode|--passcode passcode]" . PHP_EOL;
    echo "" . PHP_EOL;
    echo "Options:" . PHP_EOL;
    echo "    -h           Display this help and exit" . PHP_EOL;
    echo "    -p passcode  Use the specified passcode" . PHP_EOL;
    echo "    -u           Cache update only, do not do the analysis" . PHP_EOL;
    echo "    -f           Do the analysis and run only fast analyzers" . PHP_EOL;
    echo "    -d           Enable debug features" . PHP_EOL;
    exit;
}

$passcode = isset($options['passcode']) ? $options['passcode'] : (isset($options['p']) ? $options['p'] : null);
$runAnalysis = !isset($options['update']) && !isset($options['u']);
$fastOnly = isset($options['fast']) || isset($options['f']);
$debug = isset($options['debug']) || isset($options['d']);

$correctPasscode = Config::get("passcode");

if ($correctPasscode) {
    if ($passcode == null) {
        echo "Passcode non specificato...";
        exit(1);
    }
    if (md5($passcode) != $correctPasscode) {
        echo "Passcode non valido...";
        exit(2);
    }
}

$locked = Atomic::isLocked();
if ($locked) echo "Analysis is locked... waiting up to 60 seconds" . PHP_EOL;
Atomic::waitForLock(60.0);

if ($debug) {
    Controller::init();
    Config::override("debug", true);
}

Controller::run($runAnalysis, $fastOnly);

Atomic::unlock();

