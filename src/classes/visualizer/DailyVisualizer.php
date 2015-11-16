<?php


class DailyVisualizer {

    public static function getGrid() {
        $grid = array();
        for ($i = 0; $i < 7; $i++) $grid[$i] = array();

        $gridSql = "SELECT * FROM daily_results";
        $gridQuery = DB::$DB->query($gridSql)->fetchAll();

        foreach ($gridQuery as $row)
            $grid[$row['cell_id']/24][$row['cell_id']%24] = array(
                "value" => round($row['average'], 2),
                "color" => DailyVisualizer::toColor($row['average'])
            );

        return $grid;
    }


    private static function toColor($n) {
        $r = 30;
        $g = (int)($n*256/4);
        $b = 200;
        $color = $r*256*256 + $g*256 + $b;
        return("#".substr("000000".dechex($color),-6));
    }
}
