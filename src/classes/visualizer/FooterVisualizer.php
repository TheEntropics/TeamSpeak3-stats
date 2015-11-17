<?php


class FooterVisualizer {
    public static function getLastDate() {
        $sql = "SELECT * FROM misc_results WHERE `key` = 'lastDate'";
        return new DateTime(DB::$DB->query($sql)->fetch()['value']);
    }
}
