<?php


class StreakAnalyzer extends BaseAnalyzer {

    public static $priority = 10;

    public static function runAnalysis() {
        $startTime = microtime(true);
        $days = StreakAnalyzer::getDays();
        $endTime = microtime(true);
        Logger::log("    getDays() ->", $endTime-$startTime);

        $startTime = microtime(true);
        $streaks = array();
        foreach ($days as $client_id => $clientDays)
            $streaks[] = array("client_id" => $client_id, "streak" => StreakAnalyzer::getStreaks($client_id, $clientDays));
        $endTime = microtime(true);
        Logger::log("    getStreaks() ->", $endTime-$startTime);

        StreakAnalyzer::saveStreaks($streaks);
    }

    private static function getDays() {
        $sql = "SELECT client_id2, DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s.%f') as date FROM daily_user_result " .
                    "JOIN user_collapser_results ON daily_user_result.client_id = user_collapser_results.client_id1 " .
                    "WHERE time > 0 GROUP BY client_id2,date ORDER BY client_id2, date";
        $query = DB::$DB->query($sql);
        $res = $query->fetchAll();

        $days = array();
        $current_client_id = -1;
        foreach ($res as $row) {
            if ($row['client_id2'] != $current_client_id) {
                $current_client_id = $row['client_id2'];
                $days[$current_client_id] = array();
            }
            $days[$current_client_id][] = array("date" => $row['date']);
        }

        return $days;
    }

    private static function getStreaks($client_id, $userDays) {
        if (count($userDays) == 0) return array("length" => 0, "startDate" => "");

        $longestStreak = 1;
        $longestStartDate = new DateTime($userDays[0]['date']);

        $lastDate = $longestStartDate;
        $streak = 1;
        $startDate = $longestStartDate;
        foreach ($userDays as $row) {
            $date = new DateTime($row['date']);

            $diff = intval($lastDate->diff($date)->format('%a'));
            if ($diff == 1) {
                $streak++;
                if ($streak > $longestStreak) {
                    $longestStreak = $streak;
                    $longestStartDate = $startDate;
                }
            } else {
                $startDate = $date;
                $streak = 1;
            }
            $lastDate = $date;
        }

        $today = $lastDate->format('Y-m-d') == (new DateTime())->format('Y-m-d');
        $online = isset(OnlineRange::getOnlineRanges()[$client_id]);
        $yesterday = $lastDate >= (new DateTime())->modify("-1 day")->setTime(0, 0, 0);

        if ($yesterday && !$today && $online)
            $streak++;
        if (!$yesterday && ($today || $online)) {
            $streak = 1;
            $startDate = new DateTime();
        }
        if (!$yesterday && !$today && !$online) {
            $streak = 0;
            $startDate = new DateTime();
        }
        if ($startDate == $longestStartDate)
            $longestStreak = $streak;

        return array("longest" => $longestStreak, "startLongest" => $longestStartDate, "current" => $streak, "startCurrent" => $startDate);
    }

    private static function saveStreaks($streaks) {
        if (count($streaks) > 500) {
            $chunkes = array_chunk($streaks, 500);
            foreach ($chunkes as $chunk)
                StreakAnalyzer::saveStreaks($chunk);
        } else {
            $sql = "INSERT INTO streak_results (client_id, longest, startLongest, current, startCurrent) VALUES ";
            $chunkes = array();
            foreach ($streaks as $streak)
                $chunkes[] = "({$streak['client_id']}, " .
                    "{$streak['streak']['longest']}, '{$streak['streak']['startLongest']->format('Y-m-d')}', " .
                    "{$streak['streak']['current']}, '{$streak['streak']['startCurrent']->format('Y-m-d')}')";
            $sql .= implode(', ', $chunkes);
            $sql .= " ON DUPLICATE KEY UPDATE longest=VALUES(longest), startLongest=VALUES(startLongest), current=VALUES(current), startCurrent=VALUES(startCurrent)";
            DB::$DB->query($sql);
        }
    }
}
