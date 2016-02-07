<?php

class UsernameUptimeAnalyzer extends BaseAnalyzer {

    public static function runAnalysis() {
        $ranges = OnlineRange::getRanges();
        $times = UsernameUptimeAnalyzer::computeTimes($ranges);
        UsernameUptimeAnalyzer::saveTimes($times);
    }

    private static function computeTimes($ranges) {
        $times = array();
        foreach ($ranges as $range) {
            $user_id = $range->user->id;
            if (!isset($times[$user_id])) $times[$user_id] = 0;

            $times[$user_id] += Utils::getTimestamp($range->end) - Utils::getTimestamp($range->start);
        }
        return $times;
    }

    private static function saveTimes($times) {
        if (count($times) > 500) {
            $chunkes = array_chunk($times, 500, true);
            foreach ($chunkes as $chunk)
                UsernameUptimeAnalyzer::saveTimes($chunk);
        } else {
            $sql = "INSERT INTO users_uptime (user_id, time) VALUES ";
            $chunkes = array();
            foreach ($times as $user_id => $time)
                $chunkes[] = "($user_id, $time)";
            $sql .= implode(', ', $chunkes);
            $sql .= " ON DUPLICATE KEY UPDATE time=VALUES(time)";
            DB::$DB->query($sql);
        }
    }
}
