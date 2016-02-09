<?php

class Config {
    public static $config = [
        // Configure the database connection info
        "database" => [
            // PDO connection string
            "string" => "mysql:host=localhost;dbname=ts3stats",
            // Database username
            "username" => "root",
            // Database password
            "password" => "password"
        ],
        // A list of folders where find the TeamSpeak3 logs
        "input" => [
            "/path/to/log/files",
            "path/relative/to/app"
        ],
        // The virtual server in use (only one virtual server per time is supported)
        "virtual_server" => 1,
        // Where to put log files of the analyzer
        "log" => "logs/ts3stats.log",
        // md5 of the passcode needed to run the analysis.
        // If it is set to null the passcode is not needed
        "passcode" => "9bbe58c112d645e732af4a0e1046c100",

        // Enable debug features like run always the analysis, detailed log files
        "debug" => true,

        // Realtime configuration
        "realtime" => [
            // enable or disable realtime features
            "enabled" => true,
            // telnet connection timeout
            "timeout" => 0.1,
            // serverquery host
            "host" => "direct.serben.tk",
            // serverquery port
            "port" => 10011,
            // serverquery credentials
            "username" => "serveradmin",
            "password" => "ChiaccherataDiGruppo"
        ],

        // configuration for the analyzers, keep as is if you don't know what are they
        "analyzers" => [
            "DailyAnalyzer3" => [
                "time_scale" => 60
            ],
            "ProbableUsernameAnalyzer" => [
                "use_last_n" => 20
            ],
            "UserCollapser" => [
                "K1" => 0.6,
                "K2" => 0.5,
                "ip_subnet" => 16,
                "merge_threshold" => 0.9,
                "merge_fixed_threshold" => 0.95
            ]
        ],
        // how long a session can be before ignoring it
        "max_online_time" => 60*60*24,
        // maximum number of insert can be done in a single query (useful if the
        // database doesn't support long queries)
        "max_per_insert" => 500
    ];



    /****************************************************
     *                                                  *
     *         DON'T EDIT THE CODE DOWN HERE!!          *
     *                                                  *
     ****************************************************/

    /**
     * Get the specified configuration variable. To access to hierarchy use . (dot) to separate the nodes
     * @param string $name The name of the config variable (dot-separated)
     * @param null $default Default value if the config is unaviable
     * @return any
     */
    public static function get($name, $default = null) {
        $path = explode(".", $name);
        $object = Config::$config;
        foreach ($path as $node)
            if (isset($object[$node]))
                $object = $object[$node];
            else
                return $default;
        return $object;
    }

    /**
     * Override a specific configuration option
     * @param string $name The name of the configuration to change
     * @param any $value The value to change
     * @return bool False on error
     */
    public static function override($name, $value) {
        $path = explode(".", $name);
        $key = array_pop($path);
        $object = &Config::$config;
        foreach ($path as $node)
            if (isset($object[$node]))
                $object = &$object[$node];
            else
                return false;
        $object[$key] = $value;
        return true;
    }
}
