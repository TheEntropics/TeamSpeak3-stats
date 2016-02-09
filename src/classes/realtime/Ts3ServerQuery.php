<?php

require_once __DIR__ . '/Telnet.php';

class Ts3ServerQuery {

    /**
     * The connection timeout for the telnet
     */
    const CONNECTION_TIMEOUT = 0.1;

    private $telnet;
    private $lastError;

    /**
     * @param string $host Hostname of the teamspeak server
     * @param int $port Port of ts3 server (usually 10011)
     * @param string $username Username of admin profile (usually serveradmin)
     * @param string $password Password of admin profile
     * @param int $virtualServer The visrtual server to use
     * @throws Exception
     */
    public function __construct($host, $port, $username, $password, $virtualServer = 1) {
        $connectionTimeout = Config::get("realtime.timeout", Ts3ServerQuery::CONNECTION_TIMEOUT);

        $this->telnet = new Telnet($host, $port, 1, "error id=\\d+ msg=.*\n|specific command\\.\n", $connectionTimeout);

        $this->sendCommand("login $username $password");
        if ($this->getLastError() != 0) throw new Exception('Invalid login credentials');
        $this->sendCommand("use $virtualServer");
        if ($this->getLastError() != 0) throw new Exception('Cannot select virtual server');
    }

    /**
     * Send a command to teamspeak server
     * @param string $command Command to send
     * @return string Response of the server
     */
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

    /**
     * Get the last error, if any
     * @return int
     */
    public function getLastError() {
        return $this->lastError;
    }

    private function evalExitCode($row) {
        $matches = array();
        preg_match('/^error id=(\\d+) msg=.*$/', $row, $matches);
        $this->lastError = $matches[1];
    }

    /**
     * Explode the result string of the command into an array
     * @param string $entry The response of the server
     * @return array
     */
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
