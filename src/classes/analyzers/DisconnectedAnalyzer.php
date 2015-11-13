<?php


class DisconnectedAnalyzer extends BaseAnalyzer {

    public static function runAnalysis() {
        $sql = "SELECT * FROM
                    (SELECT COUNT(*) as total_count FROM `client_disconnected_events`) as a,
                    (SELECT COUNT(*) AS connection_lost FROM client_disconnected_events WHERE reason = 'connection lost') as b,
                    (SELECT COUNT(*) AS leaving FROM client_disconnected_events WHERE reason = 'leaving') as c";
        $query = DB::$DB->query($sql);
        $result = $query->fetch();

        Utils::saveMiscResult("connectionLostCount", $result['connection_lost']);
        Utils::saveMiscResult("leavingCount", $result['leaving']);
        Utils::saveMiscResult("othersDisconnectCount", $result['total_count'] - $result['connection_lost'] - $result['leaving']);
    }
}
