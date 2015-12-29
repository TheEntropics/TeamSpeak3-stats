<?php

require_once __DIR__ . '/Ts3ServerQuery.php';


class RealtimeUsers {
    public static function getOnlineUsers($ts3) {
        $result = $ts3->sendCommand('clientlist -voice');
        if ($ts3->getLastError() != 0) throw new Exception('Cannot get online users');

        $users = explode('|', $result);
        $result = array();
        foreach ($users as $raw_user) {
            $user = Ts3ServerQuery::explodeProperties($raw_user);
            // skip non-user clients
            if ($user['client_type'] != 0)
                continue;

            $result[] = $user;
        }

        return $result;
    }
}
