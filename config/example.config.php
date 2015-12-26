<?php

class Config {
    const DB_STRING = "mysql:host=localhost;dbname=ts3stats";
    const USERNAME = "root";
    const PASSWORD = "password";
    const LOG_FOLDERS = array("/path/to/log/files", "path/relative/to/app");
    const APP_LOG_FILE = "logs/ts3stats.log";
    const PASSCODE = "9bbe58c112d645e732af4a0e1046c100"; // md5 di "TimurBaznat"
    const DEBUG = false;

    const REALTIME_ENABLED = true;
    const REALTIME_HOST = 'localhost';
    const REALTIME_PORT = 10011;
    const REALTIME_USER = 'serveradmin';
    const REALTIME_PASS = 'password';
}
