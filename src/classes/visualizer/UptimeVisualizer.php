<?php


class UptimeVisualizer {
    public static function getUptimeScoreboard() {
        $sql = "SELECT user_collapser_results.client_id2, probable_username.username, SUM(uptime) as total_uptime
                FROM uptime_results
                JOIN user_collapser_results ON uptime_results.client_id = user_collapser_results.client_id1
                JOIN probable_username ON user_collapser_results.client_id2 = probable_username.client_id
                GROUP BY user_collapser_results.client_id2
                ORDER BY total_uptime DESC
                LIMIT 50";
        $online = OnlineRange::getOnlineRanges();
        $scores = DB::$DB->query($sql)->fetchAll();
        foreach ($scores as $i => $score) {
            $client_id = $score['client_id2'];
            $scores[$i]['uptime'] = $score['total_uptime'];
            $scores[$i]['score'] = Utils::formatTime($score['total_uptime']);
            $scores[$i]['online'] = isset($online[$client_id]);
            if ($scores[$i]['online']) $scores[$i]['onlineSince'] = $online[$client_id]->start;
            else                       $scores[$i]['onlineSince'] = "";
        }

        return $scores;
    }
}
