<?php


class UptimeVisualizer {
    public static function getUptimeScoreboard() {
        $sql = "SELECT user_collapser_results.client_id2, probable_username.username, SUM(uptime) as total_uptime
                FROM uptime_results
                JOIN user_collapser_results ON uptime_results.client_id = user_collapser_results.client_id1
                JOIN probable_username ON user_collapser_results.client_id2 = probable_username.client_id
                GROUP BY user_collapser_results.client_id2
                ORDER BY total_uptime DESC";
        return DB::$DB->query($sql)->fetchAll();
    }
}
