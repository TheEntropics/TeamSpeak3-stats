<?php


class UserCollapser extends BaseAnalyzer {

    public static $priority = 1000;
    public static $enabled = true;

    const K1 = 0.2;
    const K2 = 0.5;
    const IP_SUBNET = 16;
    const MERGE_THRESHOLD = 0.9;

    private static $unionFind = array();

    private static $users = array();

    public static function runAnalysis() {
        UserCollapser::$users = UserCollapser::prepare();
        UserCollapser::loop();
        UserCollapser::saveResults();
    }

    private static function prepare() {
        $users = array();
        $ranges = OnlineRange::getRanges();

        foreach ($ranges as $range) {
            $client_id = $range->user->client_id;
            $username = UserCollapser::preprocessUsername($range->user->username);
            $ip = UserCollapser::preprocessIP($range->ip);
            $time = $range->end->getTimestamp() - $range->start->getTimestamp();

            if (!isset($users[$client_id]))
                $users[$client_id] = array(
                    "client_id" => $client_id,
                    "ips" => array(),
                    "usernames" => array(),
                    "time" => 0
                );

            if (!isset($users[$client_id]['ips'][$ip]))
                $users[$client_id]['ips'][$ip] = 0;
            $users[$client_id]['ips'][$ip] += $time;

            if (!isset($users[$client_id]['usernames'][$username]))
                $users[$client_id]['usernames'][$username] = 0;
            $users[$client_id]['usernames'][$username] += $time;

            $users[$client_id]['time'] += $time;

            UserCollapser::$unionFind[$client_id] = $client_id;
        }

        return $users;
    }

    private static function loop() {
        do {
            $changes = false;
            foreach (UserCollapser::$users as $user1) foreach(UserCollapser::$users as $user2) {
                $client_id1 = $user1['client_id'];
                $client_id2 = $user2['client_id'];
                if (!UserCollapser::inSameSet($client_id1, $client_id2))
                    if (UserCollapser::mergable($client_id1, $client_id2)) {
                        UserCollapser::UFMerge($client_id1, $client_id2);
                        $changes = true;
                    }
            }
        } while ($changes);
    }

    private static function saveResults() {
        $sql = "INSERT IGNORE INTO user_collapser_results (client_id1, client_id2) VALUES (?, ?)";
        $query = DB::$DB->prepare($sql);

        foreach (UserCollapser::$unionFind as $client_id => $parent) {
            if ($client_id == $parent) continue;

            $query->execute(array($client_id, $parent));
        }
    }

    private static function mergable($client_id1, $client_id2) {
        $parent1 = UserCollapser::UFFind($client_id1);
        $parent2 = UserCollapser::UFFind($client_id2);

        if ($parent1 == $parent2) return false;

        $ip = UserCollapser::testIP($parent1, $parent2);
        $username = UserCollapser::testUsername($parent1, $parent2);

        $value = $ip * 0.5 + $username * 0.5;
        return $value >= UserCollapser::MERGE_THRESHOLD;
    }

    private static function testIP($client_id1, $client_id2) {
        $user1 = UserCollapser::$users[$client_id1];
        $user2 = UserCollapser::$users[$client_id2];

        $commonIPs = array_intersect(
            array_keys($user1['ips']),
            array_keys($user2['ips']));

        $value = 0;

        // se ogni ip è usato da entrambi per almeno K1% del tempo totale allora lo considero ed avrà un valore
        // proporzionale al tempo utilizzato rispetto al tempo totale
        foreach ($commonIPs as $ip)
            if ($user1['ips'][$ip] >= UserCollapser::K1 * $user1['time'] && $user2['ips'][$ip] >= UserCollapser::K1 * $user2['time'])
                $value += $user1['ips'][$ip]/$user1['time'] + $user2['ips'][$ip]/$user2['time'];

        return min($value / 2, 2.0);
    }

    private static function testUsername($client_id1, $client_id2) {
        $user1 = UserCollapser::$users[$client_id1];
        $user2 = UserCollapser::$users[$client_id2];

        $value = 0;

        // ogni username lo considero solo se ha una edit-distance minore del K2% della stringa più corta ed avrà un
        // valore proporzionale al tempo utilizzato rispetto al tempo totale
        foreach ($user1['usernames'] as $username1 => $time1)
            foreach ($user2['usernames'] as $username2 => $time2) {
                if (levenshtein($username1, $username2) < min(strlen($username1), strlen($username2)) * UserCollapser::K2)
                    $value += $user1['usernames'][$username1]/$user1['time'] + $user2['usernames'][$username2]/$user2['time'];
            }

        return min($value / 2, 2.0);
    }

    private static function inSameSet($client_id1, $client_id2) {
        return UserCollapser::UFFind($client_id1) == UserCollapser::UFFind($client_id2);
    }

    private static function UFMerge($client_id1, $client_id2) {
        $parent1 = UserCollapser::UFFind($client_id1);
        $parent2 = UserCollapser::UFFind($client_id2);

        $user1 = UserCollapser::$users[$parent1];
        $user2 = UserCollapser::$users[$parent2];

        foreach ($user1['usernames'] as $username => $time) {
            if (!isset($user2['usernames'][$username])) UserCollapser::$users[$parent2]['usernames'][$username] = 0;
            UserCollapser::$users[$parent2]['usernames'][$username] += $time;
        }

        foreach ($user1['ips'] as $ip => $time) {
            if (!isset($user2['ips'][$ip])) UserCollapser::$users[$parent2]['ips'][$ip] = 0;
            UserCollapser::$users[$parent2]['ips'][$ip] += $time;
        }

        UserCollapser::$users[$parent2]['time'] += $user1['time'];

        UserCollapser::$unionFind[$parent1] = $parent2;
    }

    private static function UFFind($client_id) {
        if (UserCollapser::$unionFind[$client_id] == $client_id) return $client_id;
        return UserCollapser::$unionFind[$client_id] = UserCollapser::UFFind(UserCollapser::$unionFind[$client_id]);
    }

    private static function preprocessIP($ip) {
        $num = ip2long($ip);
        $mask = (-1 << (32 - UserCollapser::IP_SUBNET)) & ip2long('255.255.255.255');
        return $num & $mask;
    }

    private static function preprocessUsername($username) {
        $newUsername = preg_replace("/#\d+;/", "", $username);
        return $newUsername;
    }
}
