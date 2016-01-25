<?php


class RealtimeFormatter {

    private static $channels = array();

    /**
     * Return a json with all the information about the channels and the online users
     * @param $users The users in the server
     * @param $channels The channels in the server
     * @return string
     */
    public static function getJSON($users, $channels) {
        self::processChannels($channels);
        self::processUsers($users);
        return json_encode(self::$channels);
    }

    /**
     * Like getJSON return the online status of users and channels as an array
     * @param $users The users in the server
     * @param $channels The channels in the server
     * @return array
     */
    public static function getRealtime($users, $channels) {
        self::processChannels($channels);
        self::processUsers($users);
        return self::$channels;
    }

    private static function processChannels($channels) {
        self::$channels[0] = array(
            "id" => 0,
            "parent" => 0,
            "name" => "",
            "users" => array(),
            "channels" => array()
        );

        foreach ($channels as $channel) {
            $id = $channel['cid'];
            $parent = $channel['pid'];
            $name = $channel['channel_name'];

            $ch = array(
                "id" => $id,
                "parent" => $parent,
                "name" => $name,
                "users" => array(),
                "channels" => array()
            );
            self::$channels[$parent]['channels'][] = $id;
            self::$channels[$id] = $ch;
        }
    }

    private static function processUsers($users) {
        foreach ($users as $user) {
            $channel = $user['cid'];

            self::$channels[$channel]['users'][] = array(
                "client_id" => $user['client_database_id'],
                "name" => $user['client_nickname'],
                "muted" => intval($user['client_input_muted']),
                "silenced" => intval($user['client_output_muted']),
                "away" => intval($user['client_away']),
                "away_message" => $user['client_away_message']
            );
        }
    }
}
