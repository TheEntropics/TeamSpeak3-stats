<?php


class UptimeAnalyzer extends BaseAnalyzer {

    public static function runAnalysis() {
        $ranges = OnlineRange::getRanges();
        $times = UptimeAnalyzer::computeTimes($ranges);
        UptimeAnalyzer::saveTimes($times);
    }

    private static function computeTimes($ranges) {
        $times = array();
        foreach ($ranges as $range) {
            $client_id = $range->user->client_id;
            if (!isset($times[$client_id])) $times[$client_id] = 0;

            $times[$client_id] += Utils::getTimestamp($range->end) - Utils::getTimestamp($range->start);
        }
        return $times;
    }

    private static function saveTimes($times) {
        if (count($times) > Config::get("max_per_insert", 500)) {
            $chunkes = array_chunk($times, Config::get("max_per_insert", 500), true);
            foreach ($chunkes as $chunk)
                UptimeAnalyzer::saveTimes($chunk);
        } else {
            $sql = "INSERT INTO uptime_results (client_id, uptime) VALUES ";
            $chunkes = array();
            foreach ($times as $client_id => $time)
                $chunkes[] = "($client_id, $time)";
            $sql .= implode(', ', $chunkes);
            $sql .= " ON DUPLICATE KEY UPDATE uptime=VALUES(uptime)";
            DB::$DB->query($sql);
        }
    }
}
