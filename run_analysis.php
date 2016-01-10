<?php

require_once __DIR__ . "/src/classes/Controller.php";

$options = getopt("hp:", array("passcode:"));

if (isset($options['h'])) {
    echo "Run the analysis of TeamSpeak-stats" . PHP_EOL;
    echo "Written by Edoardo Morassutto <edoardo.morassutto@gmail.com>" . PHP_EOL;
    echo "" . PHP_EOL;
    echo "Usage: php run_analysis.php [-h] [-p passcode|--passcode passcode]" . PHP_EOL;
    echo "" . PHP_EOL;
    echo "Options:" . PHP_EOL;
    echo "    -h           Display this help and exit" . PHP_EOL;
    echo "    -p passcode  Use the specified passcode" . PHP_EOL;
    exit;
}

$passcode = isset($options['passcode']) ? $options['passcode'] : (isset($options['p']) ? $options['p'] : null);

if ($passcode == null) {
    echo "Passcode non specificato...";
    exit(1);
}
if (md5($passcode) != Config::PASSCODE) {
    echo "Passcode non valido...";
    exit(2);
}

Controller::run();
