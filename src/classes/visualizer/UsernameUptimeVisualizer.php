<?php


class UsernameUptimeVisualizer {
    public static function getUsernameUptime($client_id, $limit = 100000, $offset = 0) {
        $sql = "SELECT client_id2 as client_is, username, SUM(time) as total_time
                    FROM user_collapser_results
                    JOIN users ON users.client_id = user_collapser_results.client_id1
                    JOIN users_uptime ON users.id = users_uptime.user_id
                    WHERE client_id2 = :client_id
                    GROUP BY users.username, user_collapser_results.client_id2
                    ORDER BY total_time DESC
                    LIMIT :limit OFFSET :offset";
        $query = DB::$DB->prepare($sql);
        $query->bindValue('client_id', $client_id);
        $query->bindValue('limit', $limit);
        $query->bindValue('offset', $offset);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
