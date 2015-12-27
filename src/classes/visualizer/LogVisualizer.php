<?php


class LogVisualizer {
    public static function getLastLog($n = 10) {
        $sql = "SELECT * FROM (
                    SELECT *, 'Connesso' as type FROM client_connected_events
                    UNION
                    SELECT *, 'Disconnesso' as type FROM client_disconnected_events
                ) as timur
                JOIN users ON timur.user_id = users.id
                JOIN user_collapser_results ON user_collapser_results.client_id1 = users.client_id
                ORDER BY date DESC, type
                LIMIT :limit";
        $query = DB::$DB->prepare($sql);
        $query->bindValue("limit", $n);

        $query->execute();
        return $query->fetchAll();
    }
}
