<?php

require_once __DIR__ . '/../FenwikTree.php';

class DailyAnalyzer2 extends BaseAnalyzer {

    const NUM_SEC = 60*60*24*7+10;
    const NUM_CELLS = 24*7;

    private static $fenwikTree;

    public static function runAnalysis() {
        DailyAnalyzer2::$fenwikTree = new FenwikTree(DailyAnalyzer2::NUM_SEC);

        $ranges = OnlineRange::getRanges();

        $startTime = microtime(true);
        foreach ($ranges as $range)
            DailyAnalyzer2::processRange($range);
        $endTime = microtime(true);
        Logger::log("    Process ranges -> ", $endTime-$startTime);

        $startTime = microtime(true);
        $averages = DailyAnalyzer2::getAverages();
        $endTime = microtime(true);
        Logger::log("    Get averages -> ", $endTime-$startTime);

        DailyAnalyzer2::saveAverages($averages);
    }

    private static function processRange($range) {
        $start = $range->start;
        $end = $range->end;

        $startWeek = intval($start->format("N"))-1;
        $startHour = intval($start->format("G"));
        $startMin = intval($start->format("i"));
        $startSec = intval($start->format("s"));
        $startTime = $startSec + $startMin * 60 + $startHour * 60*60 + $startWeek * 60*60*24;

        $endWeek = intval($end->format("N"))-1;
        $endHour = intval($end->format("G"));
        $endMin = intval($end->format("i"));
        $endSec = intval($end->format("s"));
        $endTime = $endSec + $endMin * 60 + $endHour * 60*60 + $endWeek * 60*60*24;

        if ($startWeek <= $endWeek) {
            DailyAnalyzer2::$fenwikTree->rangeUpdate($startTime, $endTime, 1);
        } else {
            // if the range ends in a day before the start (starts in Sunday and ends in Monday)
            // ranges longer than a week are not supported
            DailyAnalyzer2::$fenwikTree->rangeUpdate($startTime, DailyAnalyzer2::NUM_SEC-1, 1);
            DailyAnalyzer2::$fenwikTree->rangeUpdate(0, $endTime, 1);
        }
    }

    private static function getAverages() {
        $averages = array();
        $numWeeks = DailyAnalyzer2::getNumOfWeek();

        for ($i = 0; $i < DailyAnalyzer2::NUM_CELLS; $i++) {
            $sum = DailyAnalyzer2::$fenwikTree->rangeQuery($i*60*60, ($i+1)*60*60 - 1);
            $averages[$i] = $sum / $numWeeks / 3600;
        }

        return $averages;
    }

    private static function getNumOfWeek() {
        $sql = "SELECT DATEDIFF(MAX(date), MIN(date))/7 AS weeks FROM (
                    SELECT date FROM client_connected_events
                    UNION ALL
                    SELECT date FROM client_disconnected_events
                ) AS timur";
        $query = DB::$DB->query($sql);
        return $query->fetch()['weeks'];
    }

    private static function saveAverages($averages) {
        foreach ($averages as $cell_id => $average) {
            $sql = "INSERT INTO daily_results (cell_id, average) VALUES (:cell_id, :average) ON DUPLICATE KEY UPDATE average = VALUES(average)";
            $query = DB::$DB->prepare($sql);

            $query->bindParam("cell_id", $cell_id);
            $query->bindParam("average", $average);

            $query->execute();
        }
    }
}
