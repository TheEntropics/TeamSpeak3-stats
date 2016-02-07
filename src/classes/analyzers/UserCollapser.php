<?php


class UserCollapser extends BaseAnalyzer {

    public static $fast = false;
    public static $priority = 1000;

    const K1 = 0.6;
    const K2 = 0.5;
    const IP_SUBNET = 16;
    const MERGE_THRESHOLD = 0.9;
    const MERGE_FIXED_THRESHOLD = 0.95;

    private static $unionFind = array();

    private static $users = array();

    public static function runAnalysis() {
        $startTime = microtime(true);
        UserCollapser::$users = UserCollapser::prepare();
        $endTime = microtime(true);
        Logger::log("    prepare() ->", $endTime-$startTime);

        $startTime = microtime(true);
        UserCollapser::loop();
        $endTime = microtime(true);
        Logger::log("    loop() ->", $endTime-$startTime);

        UserCollapser::saveResults();
    }

    /**
     * Prepare the analyzer
     * @return array An array with all users with the usernames and the ips set
     */
    private static function prepare() {
        $users = array();
        $ranges = OnlineRange::getRanges();

        foreach ($ranges as $range) {
            $client_id = $range->user->client_id;
            $username = $range->user->username;
            $ip = UserCollapser::preprocessIP($range->ip);
            $time = Utils::getTimestamp($range->end) - Utils::getTimestamp($range->start);

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

    /**
     * Main loop of the analyzer. Repeat the merge process until the process is stable
     */
    private static function loop() {
        do {
            $changes = false;
            foreach (UserCollapser::$users as $user1) foreach(UserCollapser::$users as $user2) {
                $client_id1 = $user1['client_id'];
                $client_id2 = $user2['client_id'];
                if (!UserCollapser::inSameSet($client_id1, $client_id2))
                    if (UserCollapser::mergeable($client_id1, $client_id2)) {
                        UserCollapser::UFMerge($client_id1, $client_id2);
                        $changes = true;
                    }
            }
        } while ($changes);
    }

    private static function saveResults($data = null) {
        if ($data == null) {
            DB::$DB->query("DELETE FROM user_collapser_results");

            $data = array();
            foreach (UserCollapser::$unionFind as $client_id => $parent)
                $data[] = array($client_id, $parent);

            UserCollapser::saveResults($data);
        } elseif (count($data) > 500) {
            $chunks = array_chunk($data, 500);
            foreach ($chunks as $chunk)
                UserCollapser::saveResults($chunk);
        } else {
            $sql = "INSERT INTO user_collapser_results (client_id1, client_id2) VALUES ";
            $chunks = array();
            foreach ($data as $row)
                $chunks[] = "($row[0], $row[1])";
            $sql .= implode(', ', $chunks);
            DB::$DB->query($sql);
        }
    }

    /**
     * Check if two client_id can be merged and to be considered as the same user
     * @param $client_id1
     * @param $client_id2
     * @return bool
     */
    private static function mergeable($client_id1, $client_id2) {
        $parent1 = UserCollapser::UFFind($client_id1);
        $parent2 = UserCollapser::UFFind($client_id2);

        if ($parent1 == $parent2) return false;

        $ip = UserCollapser::testIP($parent1, $parent2);
        $username = UserCollapser::testUsername($parent1, $parent2);

        $ip = 2 * $ip - $ip * $ip;
        $username = 2 * $username - $username * $username;

        if (($ip >= UserCollapser::MERGE_FIXED_THRESHOLD && $username >= 1 - UserCollapser::MERGE_FIXED_THRESHOLD) ||
            ($username >= UserCollapser::MERGE_FIXED_THRESHOLD && $ip >= 1 - UserCollapser::MERGE_FIXED_THRESHOLD)) {
            return true;
        }

        $value = $ip * 0.5 + $username * 0.5;
        return $value >= UserCollapser::MERGE_THRESHOLD;
    }

    /**
     * Test how similar are two client id based on the IPs
     * @param $client_id1
     * @param $client_id2
     * @return float The value is in range [0.0, 1.0]
     */
    private static function testIP($client_id1, $client_id2) {
        $user1 = UserCollapser::$users[$client_id1];
        $user2 = UserCollapser::$users[$client_id2];

        $commonIPs = array_intersect(
            array_keys($user1['ips']),
            array_keys($user2['ips']));

        $value = 0;

        // se ogni ip è usato da entrambi per almeno K1% del tempo totale allora lo considero ed avrà un valore
        // proporzionale al tempo utilizzato rispetto al tempo totale
        foreach ($commonIPs as $ip) {
            $tresh1 = $user1['time'] / count($user1['ips']) * UserCollapser::K1;
            $tresh2 = $user2['time'] / count($user2['ips']) * UserCollapser::K1;
            if ($user1['ips'][$ip] >= $tresh1 && $user2['ips'][$ip] >= $tresh2)
                $value += $user1['ips'][$ip] / $user1['time'] + $user2['ips'][$ip] / $user2['time'];
        }

        return min($value / 2, 1.0);
    }

    /**
     * Test how similar are two client id based on the username
     * @param $client_id1
     * @param $client_id2
     * @return float The value is in range [0.0, 1.0]
     */
    private static function testUsername($client_id1, $client_id2) {
        $user1 = UserCollapser::$users[$client_id1];
        $user2 = UserCollapser::$users[$client_id2];

        $value = 0;

        // ogni username lo considero solo se ha una edit-distance minore del K2% della stringa più corta ed avrà un
        // valore proporzionale al tempo utilizzato rispetto al tempo totale
        foreach ($user1['usernames'] as $username1 => $time1)
            foreach ($user2['usernames'] as $username2 => $time2) {
                $username1_stripped = UserCollapser::preprocessUsername($username1);
                $username2_stripped = UserCollapser::preprocessUsername($username2);
                $l1 = strlen($username1_stripped);
                $l2 = strlen($username2_stripped);
                    // firstly use the levenshtein method
                if (levenshtein($username1_stripped, $username2_stripped) < min($l1, $l2) * UserCollapser::K2 ||
                    // if it failed try using similar_text only on usernames longer than 8
                    (min($l1, $l2) >= 8 && similar_text($username1_stripped, $username2_stripped, $perc) > 0 && $perc/100 > UserCollapser::K2))
                    $value += $user1['usernames'][$username1]/$user1['time'] + $user2['usernames'][$username2]/$user2['time'];
            }

        return min($value / 2, 1.0);
    }

    /**
     * Check if two client_id have been merged
     * @param $client_id1
     * @param $client_id2
     * @return bool True is they have been merged
     */
    private static function inSameSet($client_id1, $client_id2) {
        return UserCollapser::UFFind($client_id1) == UserCollapser::UFFind($client_id2);
    }

    /**
     * Merge client_id1 into client_id2
     * @param $client_id1
     * @param $client_id2
     */
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

    /**
     * Return the client_id of the representative of the client_id
     * @param $client_id
     * @return int The master client id
     */
    private static function UFFind($client_id) {
        if (UserCollapser::$unionFind[$client_id] == $client_id) return $client_id;
        return UserCollapser::$unionFind[$client_id] = UserCollapser::UFFind(UserCollapser::$unionFind[$client_id]);
    }

    /**
     * Preprocess an ip applying the subnet mask
     * @param $ip string The ip to process
     * @return int
     */
    private static function preprocessIP($ip) {
        $num = ip2long($ip);
        $mask = (-1 << (32 - UserCollapser::IP_SUBNET)) & ip2long('255.255.255.255');
        return $num & $mask;
    }

    /**
     * Preprocess the username removing useless chars (unicode chats in form &#XXXX; => &)
     * @param $username string The username to process
     * @return mixed
     */
    private static function preprocessUsername($username) {
        $newUsername = preg_replace("/&#\d+;/", "&", $username);
        return $newUsername;
    }

    /**
     * Print some information about a couple of groups of ids
     */
    public static function debugClientsIds($groupA, $groupB, $merges = array()) {
        UserCollapser::$users = UserCollapser::prepare();
        foreach ($merges as $merge)
            UserCollapser::UFMerge($merge[0], $merge[1]);
        foreach(array_merge($groupA, $groupB) as $client)
            print_r(UserCollapser::$users[$client]);

        foreach ($groupA as $client1)
            foreach ($groupB as $client2) {
                echo "$client1 $client2 -> IP:" . UserCollapser::testIP($client1, $client2) . " Username:" . UserCollapser::testUsername($client1, $client2) . " Mergable: " . (UserCollapser::mergeable($client1, $client2) ? 'true' : 'false') . PHP_EOL;
            }
    }
}
