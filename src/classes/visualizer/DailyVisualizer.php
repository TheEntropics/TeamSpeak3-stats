<?php

require_once __DIR__ . "/../../vendor/rgb_hsl_converter.inc.php";

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
        $startColor = array(208/360, 1.0, 0.64);
        $endColor = array(0, 1.0, 0.64);

        $t = $n / 3;

        $color = array(
            DailyVisualizer::interpolate($startColor[0], $endColor[0], $t),
            DailyVisualizer::interpolate($startColor[1], $endColor[1], $t),
            DailyVisualizer::interpolate($startColor[2], $endColor[2], $t)
        );

        return hsl2hex($color);
    }

    private static function interpolate($from, $to, $t) {
        return $from * (1 - $t) + $to * $t;
    }
}