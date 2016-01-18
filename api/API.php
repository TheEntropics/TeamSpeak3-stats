<?php

require_once __DIR__ . '/../src/classes/Controller.php';

class API {
    public static function init() {
        Controller::init(true);
    }

    public static function printJSON($result) {
        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public static function getParam($name, $default = null) {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }
}
