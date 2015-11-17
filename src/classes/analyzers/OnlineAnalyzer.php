<?php


class OnlineAnalyzer extends BaseAnalyzer {

    public static function runAnalysis() {
        $ranges = OnlineRange::getRanges();
        usort($ranges, "OnlineRange::cmpByStart");

        $onlineCount = 0;
        $queue = new OnlineRangePriorityQueue();

        $maxOnline = 0;
        $maxOnlineTime = null;

        $timeSlicer = new TimeSlicer();

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

            if ($onlineCount > $maxOnline) {
                $maxOnline = $onlineCount;
                $maxOnlineTime = $range->start;
            }
        }
        // svuota la coda
        while ($queue->valid()) {
            $timeSlicer->addTimePerNumUser($onlineCount, $queue->current()->end);
            $onlineCount--;
            $queue->extract();
        }

        Logger::log("    Picco massimo di utenti", $maxOnline, "il", $maxOnlineTime->format('Y-m-d H:i:s'));

        OnlineAnalyzer::saveMaxPeak($maxOnline, $maxOnlineTime);
        OnlineAnalyzer::saveTimePerNum($timeSlicer->getTimePerNumUser());
    }

    private static function saveMaxPeak($count, $time) {
        Utils::saveMiscResult("maxOnline", $count);
        Utils::saveMiscResult("maxOnlineTime", $time->format('Y-m-d H:i:s.u'));
    }

    private static function saveTimePerNum($timePerNumUser) {
        foreach ($timePerNumUser as $num_users => $seconds) {
            $sql = "INSERT INTO online_results (num_users, seconds) VALUES (:num_users, :seconds) ON DUPLICATE KEY UPDATE seconds = VALUES(seconds)";
            $query = DB::$DB->prepare($sql);

            $query->bindParam("num_users", $num_users);
            $query->bindParam("seconds", $seconds);

            $query->execute();
        }
    }
}
