<?php


class ChannelVisualizer {
    public static function getChannels() {
        $sql = "SELECT date,name FROM channel_events ORDER BY name, date";
        $query = DB::$DB->query($sql);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        $result = array();

        for ($i = 0, $count = count($rows); $i < $count; $i++) {
            $a = $rows[$i];
            if ($i+1 >= $count) continue;

            $b = $rows[$i+1];
            if ($a['name'] != $b['name'])
                continue;

            $i++;

            $result[] = array("name" => $a['name'], "created" => $a["date"], "deleted" => $b["date"]);
        }

        return $result;
    }
}
