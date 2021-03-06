<?php

class OnlineRangePriorityQueue extends SplPriorityQueue {
    public function compare($p1, $p2) {
        if ($p1 === $p2) return 0;
        return ($p1 < $p2) ? 1 : -1;
    }
}

class OnlineRange {

    const MAX_ONLINE_TIME = 60*60*24;

    /**
     * @var DateTime
     */
    public $start;
    /**
     * @var DateTime
     */
    public $end;

    /**
     * @var int
     */
    public $start_id;
    /**
     * @var int
     */
    public $end_id;

    /**
     * @var User
     */
    public $user;
    /**
     * @var string
     */
    public $ip;

    public function __construct($start, $end, $user, $ip) {
        $this->start = $start;
        $this->end = $end;
        $this->user = $user;
        $this->ip = $ip;
    }

    /**
     * Return the number of seconds from the start to now
     * @param null|DateTime $now It is possible to "change" the now DateTime
     * @return int
     */
    public function getTimeFromStart($now = null) {
        if ($now == null) $now = new DateTime();

        return Utils::getTimestamp($now) - Utils::getTimestamp($this->start);
    }

    public static function cmpByStart($a, $b) {
        if ($a->start == $b->start)
            if ($a->end == $b->end) return 0;
            else return ($a->end < $b->end) ? -1 : 1;
        return ($a->start < $b->start) ? -1 : 1;
    }

    private static $users_cache = array();
    private static $ranges_cache = null;
    private static $online_ranges = array();
    private static $last_online = array();

    /**
     * Get from the database a list of the ranges of online users
     * @return array
     */
    public static function getRanges() {
        if (OnlineRange::$ranges_cache)
            return OnlineRange::$ranges_cache;

        $startTime = microtime(true);
        $rows = OnlineRange::fetchAllRows();
        OnlineRange::$users_cache = User::getAll();
        $result = OnlineRange::$ranges_cache = OnlineRange::buildRanges($rows);
        $endTime = microtime(true);

        Logger::log("    OnlineRange::getRanges() -> ", $endTime-$startTime);

        $startTime = microtime(true);
        OnlineRange::saveRanges($result);
        $endTime = microtime(true);
        Logger::log("    OnlineRange::saveRanges() -> ", $endTime-$startTime);

        return $result;
    }

    /**
     * The list of online users
     * @return array
     */
    public static function getOnlineRanges() {
        OnlineRange::getRanges();
        return OnlineRange::$online_ranges;
    }

    /**
     * The last connect time of the users
     * @return array
     */
    public static function getLastOnline() {
        OnlineRange::getRanges();
        return OnlineRange::$last_online;
    }

    private static function fetchAllRows() {
        $sql = "SELECT *, x.id as event_id FROM (
                  SELECT id, ip, user_id, DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s.%f') as date, 'c' as type FROM client_connected_events
                  UNION
                  SELECT id, reason, user_id, DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s.%f') as date, 'd' as type FROM client_disconnected_events
                  UNION
                  SELECT server_started_events.id, 'SERVER_STARTED' as x, users.id AS user_id, DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s.%f') as date, 'k' as type FROM server_started_events, users
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
                if (count($sessions) > 0) {
                    foreach ($sessions as $range) {
                        $onlineTime = $range->getTimeFromStart();
                        if ($onlineTime < Config::get("max_online_time", OnlineRange::MAX_ONLINE_TIME)) {
                            // FIXME if a user is connected multiple times what happens?
                            OnlineRange::$online_ranges[$range->user->master_client_id] = $range;
                            Logger::log("    online user $currentClientId [{$range->user->master_client_id}] for $onlineTime seconds");
                        } else
                            Logger::log("    " . count($sessions) . " pending sessions of client_id = $currentClientId");
                    }
                }
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
            } else if ($row['type'] == 'd') {
                if (count($sessions) == 0) {
                    Logger::log("    No sessions found in stack for client_id = $currentClientId");
                    continue;
                }

                $end = new DateTime($row['date']);
                $end_id = $rows[$i]['event_id'];

                $range = array_pop($sessions);
                $range->end = $end;
                $range->end_id = $end_id;

                if (!isset(OnlineRange::$last_online[$range->user->master_client_id]) || Utils::getTimestamp($end) > Utils::getTimestamp(OnlineRange::$last_online[$range->user->master_client_id]))
                    OnlineRange::$last_online[$range->user->master_client_id] = $end;

                if (Utils::getTimestamp($range->end) - Utils::getTimestamp($range->start) <= Config::get("max_online_time", OnlineRange::MAX_ONLINE_TIME))
                    $ranges[] = $range;
            } else {
                foreach ($sessions as $session) {
                    $end = new DateTime($row['date']);
                    $end_id = -1;

                    $range = $session;
                    $range->end = $end;
                    $range->end_id = $end_id;

                    Logger::log("Removed session " . Utils::formatDate($range->start) . " crash on " . Utils::formatDate($end));

                    if (!isset(OnlineRange::$last_online[$range->user->master_client_id]) || Utils::getTimestamp($end) > Utils::getTimestamp(OnlineRange::$last_online[$range->user->master_client_id]))
                        OnlineRange::$last_online[$range->user->master_client_id] = $end;

                    if (Utils::getTimestamp($range->end) - Utils::getTimestamp($range->start) <= Config::get("max_online_time", OnlineRange::MAX_ONLINE_TIME))
                        $ranges[] = $range;
                }
                $sessions = [];
            }
        }

        return $ranges;
    }

    private static function getUser($user_id) {
        return OnlineRange::$users_cache[$user_id];
    }

    private static function saveRanges($ranges) {
        if (count($ranges) > Config::get("max_per_insert", 500)) {
            $chunks = array_chunk($ranges, Config::get("max_per_insert", 500));
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
