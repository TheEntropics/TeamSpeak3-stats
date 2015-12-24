<?php


class DailyUserUptimeVisualizer {
    public static function getDailyUserUptime($client_id) {
        $sql = "SELECT date, SUM(time) as day_time FROM user_collapser_results
                  JOIN daily_user_result ON daily_user_result.client_id = user_collapser_results.client_id1
                  WHERE user_collapser_results.client_id2 = :client_id
                  GROUP BY user_collapser_results.client_id2, daily_user_result.date";
        $query = DB::$DB->prepare($sql);
        $query->bindValue('client_id', $client_id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_NAMED);
    }
}
