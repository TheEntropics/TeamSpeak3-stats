<?php


class ProbableUsernameAnalyzer extends BaseAnalyzer {

    const USE_LAST_N = 20;
    private static $USE_LAST_N;

    public static $priority = 10;

    private static $ranges = array();
    private static $usernames = array();

    public static function runAnalysis() {
        ProbableUsernameAnalyzer::$USE_LAST_N = Config::get("analyzers.ProbableUsernameAnalyzer.use_last_n", ProbableUsernameAnalyzer::USE_LAST_N);

        $ranges = OnlineRange::getRanges();

        ProbableUsernameAnalyzer::splitRanges($ranges);
        ProbableUsernameAnalyzer::computeUsernames();
        ProbableUsernameAnalyzer::saveUsernames(ProbableUsernameAnalyzer::$usernames);
    }

    private static function splitRanges($ranges) {
        for ($i = count($ranges)-1; $i >= 0; $i--) {
            $range = $ranges[$i];

            $client_id = $range->user->master_client_id;

            if (!isset(ProbableUsernameAnalyzer::$ranges[$client_id]))
                ProbableUsernameAnalyzer::$ranges[$client_id] = array();
            ProbableUsernameAnalyzer::$ranges[$client_id][] = $range;
        }
    }

    private static function saveUsernames($usernames) {
        if (count($usernames) > Config::get("max_per_insert", 500)) {
            $chunks = array_chunk($usernames, Config::get("max_per_insert", 500), true);
            foreach ($chunks as $chunk)
                ProbableUsernameAnalyzer::saveUsernames($chunk);
        } else {
            $sql = "INSERT INTO probable_username (client_id, username) VALUES ";
            $data = array();
            foreach ($usernames as $client_id => $username)
                $data[] = "($client_id, " . DB::$DB->quote($username) . ")";
            $sql .= implode(",", $data);
            $sql .= " ON DUPLICATE KEY UPDATE username = VALUES(username)";
            DB::$DB->query($sql);
        }
    }

    private static function computeUsernames() {
        foreach (ProbableUsernameAnalyzer::$ranges as $client_id => $user) {
            usort($user, "OnlineRange::cmpByStart");

            $usernames = array();

            for ($i = max(0, count($user) - 1 - ProbableUsernameAnalyzer::$USE_LAST_N); $i < count($user); $i++) {
                $range = $user[$i];

                $username = $range->user->username;
                $time = $range->end->getTimestamp() - $range->start->getTimestamp();

                if (!isset($usernames[$username]))
                    $usernames[$username] = 0;
                $usernames[$username] += $time;
            }

            ProbableUsernameAnalyzer::$usernames[$client_id] = array_keys($usernames, max($usernames))[0];
        }
    }
}
