<?php

require_once __DIR__ . '/Ts3ServerQuery.php';

class RealtimeChannels {
    public static function getChannels($ts3) {
        $result = $ts3->sendCommand('channellist');
        if ($ts3->getLastError() != 0) throw new Exception('Cannot get channels');

        $channels = explode('|', $result);
        $result = array();
        foreach ($channels as $raw_user) {
            $channel = Ts3ServerQuery::explodeProperties($raw_user);
            $result[] = $channel;
        }

        return $result;
    }
}
