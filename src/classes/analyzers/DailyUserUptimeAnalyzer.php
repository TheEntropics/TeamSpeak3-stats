<?php


class DailyUserUptimeAnalyzer extends BaseAnalyzer {

    private static $times = array();

    public static function runAnalysis() {
        $startTime = microtime(true);
        DailyUserUptimeAnalyzer::buildTimes();
        $endTime = microtime(true);

        Logger::log("    DailyUserUptimeAnalyzer::buildTimes() ->", $endTime-$startTime);

        DailyUserUptimeAnalyzer::saveTimes(DailyUserUptimeAnalyzer::$times);
    }

    private static function buildTimes() {
        $ranges = OnlineRange::getRanges();

        foreach ($ranges as $range)
            DailyUserUptimeAnalyzer::addRangeTime($range);
    }

    private static function addRangeTime($range) {
        $start = $range->start;
        $end = $range->end;
        // TODO : trovare il vero client_id? se non è necessario non verrà fatto
        $client_id = $range->user->client_id;

        if (!isset(DailyUserUptimeAnalyzer::$times[$client_id]))
            DailyUserUptimeAnalyzer::$times[$client_id] = array();

        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');

        while ($start_date < $end_date) {
            $next = new DateTime($start_date);
            $next->add(new DateInterval("P1D"));

            $time = $next->getTimestamp() - $start->getTimestamp();

            if (!isset(DailyUserUptimeAnalyzer::$times[$client_id][$start_date]))
                DailyUserUptimeAnalyzer::$times[$client_id][$start_date] = 0;
            DailyUserUptimeAnalyzer::$times[$client_id][$start_date] += $time;

            $start = $next;
            $start_date = $next->format('Y-m-d');
        }

        $time = $end->getTimestamp() - $start->getTimestamp();
        if (!isset(DailyUserUptimeAnalyzer::$times[$client_id][$start_date]))
            DailyUserUptimeAnalyzer::$times[$client_id][$start_date] = 0;
        DailyUserUptimeAnalyzer::$times[$client_id][$start_date] += $time;
    }

    private static function saveTimes($times) {
        if (count($times) > 500) {
            $chunkes = array_chunk($times, 500);
            foreach ($chunkes as $chunk)
                DailyUserUptimeAnalyzer::saveTimes($chunk);
        } else {
            $sql = "INSERT INTO daily_user_result (client_id, date, time) VALUES ";
            $chunkes = array();
            foreach ($times as $user => $list)
                foreach ($list as $date => $time)
                    $chunkes[] = "($user, '$date', $time)";
            $sql .= implode(', ', $chunkes);
            $sql .= " ON DUPLICATE KEY UPDATE time=VALUES(time)";
            DB::$DB->query($sql);
        }
    }
}
