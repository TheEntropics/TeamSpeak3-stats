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

    public static function getMiscResult($key) {
        $sql = "SELECT `value` FROM misc_results WHERE `key` = :key";

        $qry = DB::$DB->prepare($sql);
        $qry->bindValue("key", $key);
        $qry->execute();
        if ($qry->rowCount == 0) return null;
        return $qry->fetch()['value'];
    }

    public static function formatDate($date) {
        if (!is_a($date, 'DateTime'))
            $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone('Europe/Rome'));
        return $date->format('d/m/Y \a\l\l\e H:i:s');
    }

    public static function formatTime($time) {
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
