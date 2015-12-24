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
                ) as x JOIN users ON x.user_id = users.id ORDER BY client_id, date";
        $query = DB::$DB->query($sql);
        return $query->fetchAll();
    }

    private static function buildRanges($rows) {
        $ranges = array();

        $currentClientId = -1;
        for ($i = 0, $count = count($rows); $i < $count; $i++) {
            if ($currentClientId != $rows[$i]['client_id'])
                $currentClientId = $rows[$i]['client_id'];

            // salta tutti i disconnected fino al primo connected
            while ($i < $count && $rows[$i]['type'] == 'd') $i++;

            $user = OnlineRange::getUser($rows[$i]['user_id']);
            $ip = $rows[$i]['ip'];
            $start = (new DateTime($rows[$i]['date']));
            $start_id = $rows[$i]['event_id'];

            // se non c'è il corrispondente 'disconnected' ignora tutto
            if ($rows[$i+1]['type'] == 'c') continue;
            if ($rows[$i+1]['client_id'] != $currentClientId) continue;

            $i++;
            $end = (new DateTime($rows[$i]['date']));
            $end_id = $rows[$i]['event_id'];

            $range = new OnlineRange($start, $end, $user, $ip);
            $range->start_id = $start_id;
            $range->end_id = $end_id;
            $ranges[] = $range;
        }

        return $ranges;
    }

    private static function getUser($user_id) {
        if (isset(OnlineRange::$users_cache[$user_id]))
            return OnlineRange::$users_cache[$user_id];
        return OnlineRange::$users_cache[$user_id] = User::fromId($user_id);
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
