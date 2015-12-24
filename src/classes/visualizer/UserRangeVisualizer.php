<?php


class UserRangeVisualizer {
    public static function getUserRanges($client_id, $limit = -1) {
        $sql = "SELECT client_connected_events.date as connect_date, client_disconnected_events.date as disconnect_date,
                    users.username as username, client_disconnected_events.date - client_connected_events.date as duration
                    FROM ranges
                    JOIN client_connected_events ON client_connected_events.id = ranges.connected_id
                    JOIN client_disconnected_events ON client_disconnected_events.id = ranges.disconnected_id
                    JOIN users ON users.id = client_connected_events.user_id
                    JOIN user_collapser_results ON users.client_id = user_collapser_results.client_id1
                    WHERE user_collapser_results.client_id2 = :client_id
                    ORDER BY client_connected_events.date DESC ";
        if ($limit > 0)
            $sql .= "LIMIT :limit";

        $query = DB::$DB->prepare($sql);
        $query->bindValue('client_id', $client_id);
        if ($limit > 0) $query->bindValue('limit', $limit);
        $query->execute();
        return $query->fetchAll();
    }
}