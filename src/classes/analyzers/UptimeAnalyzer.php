<?php


class UptimeAnalyzer extends BaseAnalyzer {

    public static function runAnalysis() {
        $times = UptimeAnalyzer::buildTimes();
        UptimeAnalyzer::saveTimes($times);
    }

    private static function buildTimes() {
        $times = array();
        $ranges = OnlineRange::getRanges();

        foreach ($ranges as $range) {
            if (!isset($times[$range->user->client_id]))
                $times[$range->user->client_id] = 0;
            $times[$range->user->client_id] += $range->end->getTimestamp() - $range->start->getTimestamp();
        }

        return $times;
    }

    private static function saveTimes($times) {
        foreach ($times as $client_id => $uptime) {
            $sql = "INSERT INTO uptime_results (client_id, uptime) VALUES (:client_id, :uptime) ON DUPLICATE KEY UPDATE uptime = VALUES(uptime)";
            $query = DB::$DB->prepare($sql);

            $query->bindValue("client_id", $client_id);
            $query->bindValue("uptime", $uptime);

            $query->execute();
        }
    }
}
