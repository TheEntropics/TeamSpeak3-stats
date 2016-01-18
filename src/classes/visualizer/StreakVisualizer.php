<?php

class StreakVisualizer {
    public static function getStreak($client_id) {
        $sql = "SELECT * FROM streak_results WHERE client_id = :client_id";
        $query = DB::$DB->prepare($sql);
        $query->bindValue('client_id', $client_id);
        $query->execute();
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) == 0) return null;
        return $res[0];
    }
}
