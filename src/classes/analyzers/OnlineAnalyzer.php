<?php

class OnlineRangePriorityQueue extends SplPriorityQueue {
    public function compare($p1, $p2) {
        if ($p1 === $p2) return 0;
        return ($p1 < $p2) ? 1 : -1;
    }
}

class OnlineAnalyzer extends BaseAnalyzer {

    public static function runAnalysis() {
        $ranges = OnlineRange::getRanges();
        usort($ranges, "OnlineRange::cmpByStart");

        $onlineCount = 0;
        $queue = new OnlineRangePriorityQueue();

        $maxOnline = 0;
        $maxOnlineTime = null;

        foreach ($ranges as $range) {
            while ($queue->valid() && $queue->current()->end < $range->start) {
                OnlineAnalyzer::addTimePerNumUser($onlineCount, $queue->current()->end);
                $onlineCount--;
                $queue->extract();
            }

            OnlineAnalyzer::addTimePerNumUser($onlineCount, $range->start);
            $queue->insert($range, $range->end);
            $onlineCount++;

            if ($onlineCount > $maxOnline) {
                $maxOnline = $onlineCount;
                $maxOnlineTime = $range->start;
            }
        }

        OnlineAnalyzer::saveMaxPeak($maxOnline, $maxOnlineTime);
        OnlineAnalyzer::saveTimePerNum();
    }

    private static $timePerNumUser = array();
    private static $lastEventTime = null;

    private static function addTimePerNumUser($numUser, $time) {
        if (!isset(OnlineAnalyzer::$timePerNumUser[$numUser]))
            OnlineAnalyzer::$timePerNumUser[$numUser] = 0;
        if (OnlineAnalyzer::$lastEventTime == null)
            OnlineAnalyzer::$lastEventTime = $time;
        OnlineAnalyzer::$timePerNumUser[$numUser] += $time->getTimestamp() - OnlineAnalyzer::$lastEventTime->getTimestamp();
        OnlineAnalyzer::$lastEventTime = $time;
    }

    private static function saveMaxPeak($count, $time) {
        $sql = "INSERT INTO misc_results (`key`, `value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";

        $qry = DB::$DB->prepare($sql);
        $qry->bindValue("key", "maxOnline");
        $qry->bindValue("value", $count);
        $qry->execute();

        $qry = DB::$DB->prepare($sql);
        $qry->bindValue("key", "maxOnlineTime");
        $qry->bindValue("value", $time->format('Y-m-d H:i:s.u'));
        $qry->execute();
    }

    private static function saveTimePerNum() {
        foreach (OnlineAnalyzer::$timePerNumUser as $num_users => $seconds) {
            $sql = "INSERT INTO online_results (num_users, seconds) VALUES (:num_users, :seconds) ON DUPLICATE KEY UPDATE seconds = VALUES(seconds)";
            $query = DB::$DB->prepare($sql);

            $query->bindParam("num_users", $num_users);
            $query->bindParam("seconds", $seconds);

            $query->execute();
        }
        print_r(OnlineAnalyzer::$timePerNumUser);
    }
}
