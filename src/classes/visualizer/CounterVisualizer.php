<?php


class CounterVisualizer {
    public static function getCounters() {
        $sql = "SELECT * FROM misc_results WHERE `key` IN (
                  'connectionLostCount', 'leavingCount', 'othersDisconnectCount', 'connectionCount',
                  'fileUploadCount', 'fileDownloadCount', 'fileDeletedCount',
                  'maxOnline', 'maxOnlineTime')";
        $query = DB::$DB->query($sql);
        $rows = $query->fetchAll();

        $count = array();
        foreach ($rows as $row)
            $count[$row['key']] = $row['value'];

        $count['total'] = $count['connectionLostCount'] + $count['leavingCount'] + $count['othersDisconnectCount'];

        return $count;
    }
}
