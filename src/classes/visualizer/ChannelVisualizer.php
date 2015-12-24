<?php


class ChannelVisualizer {
    public static function getChannels() {
        $sql = "SELECT * FROM channel_events ORDER BY name, date";
        $query = DB::$DB->query($sql);
        $rows = $query->fetchAll();

        $result = array();

        for ($i = 0, $count = count($rows); $i < $count; $i++) {
            $a = $rows[$i];
            if ($i+1 >= $count) continue;

            $b = $rows[$i+1];
            if ($a['name'] != $b['name'])
                continue;

            $i++;

            $result[] = array($a, $b);
        }

        return $result;
    }
}
