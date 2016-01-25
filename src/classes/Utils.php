<?php


class Utils {
    /**
     * Check if a string ends with a specific end
     * @param $string Long string to check
     * @param $end Suffix to check if it is in $string
     * @return bool True is $string ends with $end
     */
    public static function endWith($string, $end) {
        $l = strlen($end);
        return substr($string, -$l) == $end;
    }

    /**
     * Check if a string starts with a specific prefix
     * @param $string Long string to check
     * @param $start Prefix to check if it is in $string
     * @return bool True is $string starts with $start
     */
    public static function startsWith($string, $start) {
        $l = strlen($start);
        return substr($string, 0, $l) == $start;
    }

    /**
     * Save into the database a value with a key
     */
    public static function saveMiscResult($key, $value) {
        $sql = "INSERT INTO misc_results (`key`, `value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";

        $qry = DB::$DB->prepare($sql);
        $qry->bindValue("key", $key);
        $qry->bindValue("value", $value);
        $qry->execute();
    }

    /**
     * Return a value stored in the database, null if it isn't present
     * @param $key
     * @return null|string
     */
    public static function getMiscResult($key) {
        $sql = "SELECT `value` FROM misc_results WHERE `key` = :key";

        $qry = DB::$DB->prepare($sql);
        $qry->bindValue("key", $key);
        $qry->execute();
        $res = $qry->fetchAll();
        if (count($res) == 0) return null;
        return $res[0]['value'];
    }

    /**
     * Format a DateTime using a specific format
     * @param DateTime $date Date to format
     * @param string $format Format to use
     * @return string The formatted date
     */
    public static function formatDate($date, $format = 'd/m/Y \a\l\l\e H:i:s') {
        if (!is_a($date, 'DateTime'))
            $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone('Europe/Rome'));
        return $date->format($format);
    }

    /**
     * Format a timespan
     * @param $time Number of seconds to format
     * @return string
     */
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
