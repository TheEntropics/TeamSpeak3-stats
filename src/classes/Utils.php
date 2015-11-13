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

    public static function saveMiscResult($key, $value) {
        $sql = "INSERT INTO misc_results (`key`, `value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";

        $qry = DB::$DB->prepare($sql);
        $qry->bindValue("key", $key);
        $qry->bindValue("value", $value);
        $qry->execute();
    }
}
