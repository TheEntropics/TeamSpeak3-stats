<?php

class DailyVisualizer {

    public static function getGrid() {
        $grid = array();
        for ($i = 0; $i < 7; $i++) $grid[$i] = array();

        $gridSql = "SELECT * FROM daily_results ORDER BY timestamp";
        $gridQuery = DB::$DB->query($gridSql)->fetchAll(PDO::FETCH_ASSOC);

        return $gridQuery;
    }
}
