<?php


class DailyAnalyzer3 extends BaseAnalyzer {

    /**
     * The scale of precision used in fenwick tree.
     * 1 => seconds
     * 60 => minutes
     *
     * The timestamp is divided by this value
     */
    const TIME_SCALE = 60;
    private static $TIME_SCALE;

    /**
     * @var null|DateTime
     */
    private static $lowerBound = null;
    /**
     * @var null|DateTime
     */
    private static $upperBound = null;
    /**
     * @var int
     */
    private static $timeOffset = 0;
    /**
     * @var int
     */
    private static $timeSpan = 0;
    /**
     * @var FenwickTree
     */
    private static $fenwickTree = null;

    public static function runAnalysis() {
        DailyAnalyzer3::$TIME_SCALE = Config::get("analyzers.DailyAnalyzer3.time_scale", DailyAnalyzer3::TIME_SCALE);

        DailyAnalyzer3::getBounds();
        DailyAnalyzer3::$fenwickTree = new FenwickTree(DailyAnalyzer3::$timeSpan + 10);

        $ranges = OnlineRange::getRanges();
        $startTime = microtime(true);
        foreach ($ranges as $range)
            DailyAnalyzer3::processRange($range);
        $endTime = microtime(true);
        Logger::log("    Process ranges -> ", $endTime-$startTime);

        $startTime = microtime(true);
        $averages = DailyAnalyzer3::getAverages();
        $endTime = microtime(true);
        Logger::log("    Get averages -> ", $endTime-$startTime);

        $startTime = microtime(true);
        DailyAnalyzer3::saveAverages($averages);
        $endTime = microtime(true);
        Logger::log("    Save averages -> ", $endTime-$startTime);
    }

    private static function getBounds() {
        $sql = "SELECT MIN(date) as lower_bound, MAX(date) as upper_bound FROM (
                    SELECT date FROM client_connected_events
                    UNION ALL
                    SELECT date FROM client_disconnected_events) as timur";
        $res = DB::$DB->query($sql)->fetch();
        DailyAnalyzer3::$lowerBound = new DateTime($res['lower_bound']);
        DailyAnalyzer3::$upperBound = new DateTime($res['upper_bound']);
        DailyAnalyzer3::$timeOffset = DailyAnalyzer3::getIndexFromDate(DailyAnalyzer3::$lowerBound);
        DailyAnalyzer3::$timeSpan = DailyAnalyzer3::getIndexFromDate(DailyAnalyzer3::$upperBound) - DailyAnalyzer3::getIndexFromDate(DailyAnalyzer3::$lowerBound);
    }

    /**
     * Return the index in the Fenwick tree of a DateTime
     * @param DateTime $date
     * @return int
     */
    private static function getIndexFromDate($date) {
        return intval($date->getTimestamp() / DailyAnalyzer3::$TIME_SCALE) + 1;
    }

    /**
     * Process a range
     * @param OnlineRange $range
     */
    private static function processRange($range) {
        $start = DailyAnalyzer3::getIndexFromDate($range->start) - DailyAnalyzer3::$timeOffset;
        $end = DailyAnalyzer3::getIndexFromDate($range->end) - DailyAnalyzer3::$timeOffset;

        DailyAnalyzer3::$fenwickTree->rangeUpdate($start, $end, 1);
    }

    /**
     * Compute the averages using the fenwick tree and returns an array indexed with the
     * timestamps of the hours
     * @return array
     */
    private static function getAverages() {
        $averages = array();

        $base = DailyAnalyzer3::$lowerBound->getTimestamp();
        $base -= $base % 3600;

        for ($i = 0; $i < DailyAnalyzer3::$timeSpan; $i += DailyAnalyzer3::$TIME_SCALE) {
            $start = $i;
            $end = min($i + DailyAnalyzer3::$TIME_SCALE - 1, DailyAnalyzer3::$timeSpan);

            $sum = DailyAnalyzer3::$fenwickTree->rangeQuery($start, $end);
            $average = $sum * 1.0 / ($end - $start);
            if ($average > 0)
                $averages[$base + $i*DailyAnalyzer3::$TIME_SCALE] = $average;
        }

        return $averages;
    }

    private static function saveAverages($averages) {
        if (count($averages) > Config::get("max_per_insert", 500)) {
            $chunks = array_chunk($averages, Config::get("max_per_insert", 500), true);
            foreach ($chunks as $chunk)
                DailyAnalyzer3::saveAverages($chunk);
        } else {
            $sql = "INSERT INTO daily_results (timestamp, average) VALUES ";
            $chunks = array();
            foreach ($averages as $timestamp => $average)
                $chunks[] = "($timestamp, $average)";
            $sql .= implode(", ", $chunks);
            $sql .= " ON DUPLICATE KEY UPDATE average = VALUES(average)";

            DB::$DB->query($sql);
        }
    }
}
