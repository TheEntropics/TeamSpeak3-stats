<?php

require_once __DIR__ . "/src/classes/Controller.php";

function getPasscode() {
    if (isset($_GET['passcode']))
        return $_GET['passcode'];

    global $argv;
    if (isset($argv[1]))
        return $argv[1];

    return null;
}

$passcode = getPasscode();

if ($passcode == null) {
    echo "Passcode non specificato...";
    exit(1);
}
if (md5($passcode) != Config::PASSCODE) {
    echo "Passcode non valido...";
    exit(2);
}

Controller::run();

echo "done";
