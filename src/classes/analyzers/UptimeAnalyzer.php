<?php


class UptimeAnalyzer extends BaseAnalyzer {

    public static function runAnalysis() {
        $rows = UptimeAnalyzer::fetchAllRows();
        $times = UptimeAnalyzer::buildTimes($rows);
        UptimeAnalyzer::saveTimes($times);
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

    private static function buildTimes($rows) {
        $times = array();

        $currentClientId = -1;
        $timer = 0;
        $user = null;
        for ($i = 0, $count = count($rows); $i < $count; $i++) {
            if ($currentClientId != $rows[$i]['client_id']) {
                if ($currentClientId != -1) {
                    if (!isset($times[$currentClientId]))
                        $times[$currentClientId] = $timer;
                    else
                        $times[$currentClientId] += $timer;
                }
                $timer = 0;
                $currentClientId = $rows[$i]['client_id'];
            }

            // salta tutti i disconnected fino al primo connected
            while ($i < $count && $rows[$i]['type'] == 'd') $i++;

            $start = (new DateTime($rows[$i]['date']))->getTimestamp();

            // se non c'è il corrispondente 'disconnected' ignora tutto
            if ($rows[$i+1]['type'] == 'c') continue;
            if ($rows[$i+1]['client_id'] != $currentClientId) continue;

            $i++;
            $end = (new DateTime($rows[$i]['date']))->getTimestamp();

            $timer += $end - $start;
        }

        return $times;
    }

    private static function saveTimes($times) {
        foreach ($times as $client_id => $uptime) {
            $sql = "INSERT INTO uptime_results (client_id, uptime) VALUES (:client_id, :uptime) ON DUPLICATE KEY UPDATE uptime = :uptime";
            $query = DB::$DB->prepare($sql);

            $query->bindParam("client_id", $client_id);
            $query->bindParam("uptime", $uptime);

            $query->execute();
        }
    }
}
