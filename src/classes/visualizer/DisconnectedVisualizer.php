<?php


class DisconnectedVisualizer {
    public static function getDisconnected() {
        $sql = "SELECT * FROM misc_results WHERE `key` IN ('connectionLostCount', 'leavingCount', 'othersDisconnectCount')";
        $query = DB::$DB->query($sql);
        $rows = $query->fetchAll();

        $count = array();
        foreach ($rows as $row)
            $count[$row['key']] = $row['value'];

        $count['total'] = array_sum($count);

        return $count;
    }
}
