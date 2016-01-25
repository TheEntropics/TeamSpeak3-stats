<?php


class DailyAnalyzer extends BaseAnalyzer {

    public static $enabled = false;

    private static $numOfWeeks;

    public static function runAnalysis() {
        DailyAnalyzer::$numOfWeeks = DailyAnalyzer::getNumOfWeek();

        $startTime = microtime(true);
        $ranges = DailyAnalyzer::expandRanges(OnlineRange::getRanges());
        $endTime = microtime(true);

        Logger::log("    Expand ranges -> ", $endTime-$startTime);

        usort($ranges, "OnlineRange::cmpByStart");

        $grid = DailyAnalyzer::divideRanges($ranges);

        $averages = array();
        foreach($grid as $i => $cell)
            $averages[$i] = DailyAnalyzer::getAvgNumber($cell);

        DailyAnalyzer::saveAverages($averages);
    }

    private static function expandRanges($ranges) {
        // TODO optimize this!.. SCIUAR!
        $result = array();
        foreach ($ranges as $range)
            DailyAnalyzer::expandRange($range, $result);
        Logger::log("    expandRanges: from", count($ranges), "to", count($result));
        return $result;
    }

    private static function expandRange($range, &$ranges) {
        $step = clone $range->start;
        $step->setTime(intval($step->format('H'))+1, 0, 0);

        $ONE_HOUR = new DateInterval("PT1H");

        $ranges[] = new OnlineRange($range->start, clone $step, $range->user, $range->ip);
        while ($step < $range->end) {
            $start = clone $step;
            $end = clone $step->add($ONE_HOUR);
            $ranges[] = new OnlineRange($start, $end, $range->user, $range->ip);
        }
        $ranges[count($ranges)-1]->end = $range->end;
    }

    private static function divideRanges($ranges) {
        $grid = array();
        for ($i = 0; $i < 24*7; $i++) $grid[$i] = array();

        foreach ($ranges as $range)
            $grid[DailyAnalyzer::getCellIndex($range)][] = $range;

        return $grid;
    }

    private static function getCellIndex($range) {
        $hour = intval($range->start->format('H'));
        $day = intval($range->start->format('N'))-1;

        return $hour + 24 * $day;
    }

    private static function getAvgNumber($ranges) {
        $timeSlicer = new TimeSlicer();
        $queue = new OnlineRangePriorityQueue();
        $onlineCount = 0;

        foreach ($ranges as $range) {
            // disconnette gli utenti
            while ($queue->valid() && $queue->current()->end < $range->start) {
                $timeSlicer->addTimePerNumUser($onlineCount, $queue->current()->end);
                $onlineCount--;
                $queue->extract();
            }

            // connette l'utente dell'intervallo
            $timeSlicer->addTimePerNumUser($onlineCount, $range->start);
            $queue->insert($range, $range->end);
            $onlineCount++;
        }
        // svuota la coda
        while ($queue->valid()) {
            $timeSlicer->addTimePerNumUser($onlineCount, $queue->current()->end);
            $onlineCount--;
            $queue->extract();
        }

        return $timeSlicer->getAverageUsers(3600*DailyAnalyzer::$numOfWeeks);
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
