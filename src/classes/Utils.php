<?php


class Utils {
    public static function endWith($string, $end) {
        $l = strlen($end);
        return substr($string, -$l) == $end;
    }

    public static function startsWith($string, $start) {
        $l = strlen($start);
        return substr($string, 0, $l) == $start;
    }
}
