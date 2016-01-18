<?php


class LogVisualizer {
    public static function getLastLog($limit = 10, $offset = 0) {
        $sql = "SELECT date,type,username,client_id2 as client_id FROM (
                    SELECT *, 'Connesso' as type FROM client_connected_events
                    UNION
                    SELECT *, 'Disconnesso' as type FROM client_disconnected_events
                ) as timur
                JOIN users ON timur.user_id = users.id
                JOIN user_collapser_results ON user_collapser_results.client_id1 = users.client_id
                ORDER BY date DESC, type
                LIMIT :limit OFFSET :offset";
        $query = DB::$DB->prepare($sql);
        $query->bindValue("limit", $limit);
        $query->bindValue("offset", $offset);

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
