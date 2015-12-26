<?php

class OnlineRangePriorityQueue extends SplPriorityQueue {
    public function compare($p1, $p2) {
        if ($p1 === $p2) return 0;
        return ($p1 < $p2) ? 1 : -1;
    }
}

class OnlineRange {

    const MAX_RANGES_PER_INSERT = 500;

    public $start;
    public $end;

    public $start_id;
    public $end_id;

    public $user;
    public $ip;

    public function __construct($start, $end, $user, $ip) {
        $this->start = $start;
        $this->end = $end;
        $this->user = $user;
        $this->ip = $ip;
    }

    public static function cmpByStart($a, $b) {
        if ($a->start == $b->start)
            if ($a->end == $b->end) return 0;
            else return ($a->end < $b->end) ? -1 : 1;
        return ($a->start < $b->start) ? -1 : 1;
    }

    private static $users_cache = array();
    private static $ranges_cache = null;

    public static function getRanges() {
        if (OnlineRange::$ranges_cache)
            return OnlineRange::$ranges_cache;

        $startTime = microtime(true);
        $rows = OnlineRange::fetchAllRows();
        OnlineRange::$users_cache = User::getAll();
        $result = OnlineRange::$ranges_cache = OnlineRange::buildRanges($rows);
        $endTime = microtime(true);

        Logger::log("      OnlineRange::getRanges() -> ", $endTime-$startTime);

        $startTime = microtime(true);
        OnlineRange::saveRanges($result);
        $endTime = microtime(true);
        Logger::log("      OnlineRange::saveRanges() -> ", $endTime-$startTime);

        return $result;
    }

    private static function fetchAllRows() {
        $sql = "SELECT *, x.id as event_id FROM (
                  SELECT *, 'c' as type FROM client_connected_events
                  UNION
                  SELECT *, 'd' as type FROM client_disconnected_events
                ) as x JOIN users ON x.user_id = users.id ORDER BY client_id, date, type";
        $query = DB::$DB->query($sql);
        return $query->fetchAll();
    }

    private static function buildRanges($rows) {
        $ranges = array();

        $currentClientId = -1;
        // stack with the open connections of the client
        $sessions = array();
        for ($i = 0, $count = count($rows); $i < $count; $i++) {
            $row = $rows[$i];
            if ($currentClientId != $row['client_id']) {
                // maybe online?
                if (count($sessions) > 0)
                    Logger::log("      " . count($sessions) . " pending sessions of client_id = $currentClientId");
                $sessions = array();
                $currentClientId = $row['client_id'];
            }

            if ($row['type'] == 'c') {
                $user = OnlineRange::getUser($row['user_id']);
                $ip = $row['ip'];
                $start = new DateTime($row['date']);
                $start_id = $row['event_id'];

                $range = new OnlineRange($start, null, $user, $ip);
                $range->start_id = $start_id;

                $sessions[] = $range;
            } else {
                if (count($sessions) == 0) {
                    Logger::log("      No sessions found in stack for client_id = $currentClientId");
                    continue;
                }

                $end = new DateTime($row['date']);
                $end_id = $rows[$i]['event_id'];

                $range = array_pop($sessions);
                $range->end = $end;
                $range->end_id = $end_id;

                $ranges[] = $range;
            }
        }

        return $ranges;
    }

    private static function getUser($user_id) {
        return OnlineRange::$users_cache[$user_id];
    }

    private static function saveRanges($ranges) {
        if (count($ranges) > OnlineRange::MAX_RANGES_PER_INSERT) {
            $chunks = array_chunk($ranges, OnlineRange::MAX_RANGES_PER_INSERT);
            foreach($chunks as $chunk)
                OnlineRange::saveRanges($chunk);
        } else {
            $sql = "INSERT IGNORE INTO ranges (connected_id, disconnected_id) VALUES ";
            $chunks = array_map("OnlineRange::getInsertString", $ranges);
            $sql .= implode(', ', $chunks);

            DB::$DB->query($sql);
        }
    }

    private static function getInsertString($range) {
        return "({$range->start_id}, {$range->end_id})";
    }
}
