<?php


class OnlineRange {
    public $start;
    public $end;

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
        $rows = OnlineRange::fetchAllRows();
        return OnlineRange::$ranges_cache = OnlineRange::buildRanges($rows);
    }

    private static function fetchAllRows() {
        $sql = "SELECT * FROM (
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

            // se non c'è il corrispondente 'disconnected' ignora tutto
            if ($rows[$i+1]['type'] == 'c') continue;
            if ($rows[$i+1]['client_id'] != $currentClientId) continue;

            $i++;
            $end = (new DateTime($rows[$i]['date']));

            $ranges[] = new OnlineRange($start, $end, $user, $ip);
        }

        return $ranges;
    }

    private static function getUser($user_id) {
        if (isset(OnlineRange::$users_cache[$user_id]))
            return OnlineRange::$users_cache[$user_id];
        return OnlineRange::$users_cache[$user_id] = User::fromId($user_id);
    }
}
