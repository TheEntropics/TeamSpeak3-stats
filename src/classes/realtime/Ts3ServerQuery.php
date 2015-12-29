<?php

require_once __DIR__ . '/Telnet.php';

class Ts3ServerQuery {

    const CONNECTION_TIMEOUT = 0.1;

    private $telnet;
    private $lastError;

    public function __construct($host, $port, $username, $password, $virtualServer = 1) {
        $this->telnet = new Telnet($host, $port, 1, "error id=\\d+ msg=.*\n|specific command\\.\n", self::CONNECTION_TIMEOUT);

        $this->sendCommand("login $username $password");
        if ($this->getLastError() != 0) throw new Exception('Invalid login credentials');
        $this->sendCommand("use $virtualServer");
        if ($this->getLastError() != 0) throw new Exception('Cannot select virtual server');
    }

    public function sendCommand($command) {
        $result = $this->telnet->exec($command);
        $pieces = explode(PHP_EOL, $result);
        if (count($pieces) == 1) {
            $this->evalExitCode($pieces[0]);
            return "";
        } else {
            $this->evalExitCode(array_pop($pieces));
            return implode(PHP_EOL, $pieces);
        }
    }

    public function getLastError() {
        return $this->lastError;
    }

    private function evalExitCode($row) {
        $matches = array();
        preg_match('/^error id=(\\d+) msg=.*$/', $row, $matches);
        $this->lastError = $matches[1];
    }

    public static function explodeProperties($entry) {
        $raw_info = explode(' ', $entry);
        $info = array();
        foreach($raw_info as $inf) {
            $prop = explode('=', $inf);
            $info[$prop[0]] = count($prop) > 1 ? str_replace('\s', ' ', $prop[1]) : "";
        }
        return $info;
    }
}
