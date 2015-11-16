<?php


class UptimeVisualizer {
    public static function getUptimeScoreboard() {
        $sql = "SELECT user_collapser_results.client_id2, probable_username.username, SUM(uptime) as total_uptime
                FROM uptime_results
                JOIN user_collapser_results ON uptime_results.client_id = user_collapser_results.client_id1
                JOIN probable_username ON user_collapser_results.client_id2 = probable_username.client_id
                GROUP BY user_collapser_results.client_id2
                ORDER BY total_uptime DESC";
        $scores = DB::$DB->query($sql)->fetchAll();
        foreach ($scores as $i => $score) {
            $scores[$i]['score'] = UptimeVisualizer::formatTime($score['total_uptime']);
        }

        return $scores;
    }

    private static function formatTime($time) {
        $seconds = intval($time % 60);
        $minutes = intval($time / 60 % 60);
        $hours = intval($time / 60 / 60 % 24);
        $days = intval($time / 60 / 60 / 24);

        $str = "";

        if ($days > 0)
            if ($days == 1) $str .= "1 giorno";
            else            $str .= "$days giorni";

        if ($hours > 0)
            if ($hours == 1) $str .= " 1 ora";
            else             $str .= " $hours ore";

        if ($minutes > 0)
            if ($minutes == 1) $str .= " 1 minuto";
            else               $str .= " $minutes minuti";

        if ($seconds > 0)
            if ($seconds == 1) $str .= " 1 secondo";
            else               $str .= " $seconds secondi";

        return $str;
    }
}
