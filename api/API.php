<?php

require_once __DIR__ . '/../src/classes/Controller.php';

class API {
    public static function init() {
        Controller::init(true);
    }

    public static function printJSON($result) {
        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }

    public static function getParam($name, $default = null) {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    public static function getClientId() {
        $client_id = intval(API::getParam('client_id'));
        if (!$client_id) {
            http_response_code(400);
            API::printJSON([ "error" => "Please specify a valid client_id" ]);
            return;
        }

        return User::getMasterClientId($client_id);
    }
}
