<?php


class CacheService {
    public static function updateCache() {
        $startTime = microtime(true);
        $lastDate = CacheService::fetchLastDate();
        $events = CacheService::getEvents($lastDate);
        CacheService::putEvents($events);
        $endTime = microtime(true);

        Logger::log("Tempo di fetch dei log:", $endTime-$startTime);

        return count($events);
    }

    private static function fetchLastDate() {
        $sql = "SELECT MAX(max_date) FROM (
                    SELECT MAX(date) as max_date FROM client_connected_events
                    UNION ALL
                    SELECT MAX(date) as max_date FROM client_disconnected_events
                    UNION ALL
                    SELECT MAX(date) as max_date FROM file_manager_events
                    UNION ALL
                    SELECT MAX(date) as max_date FROM channel_events
                ) AS temp";
        $query = DB::$DB->query($sql)->fetch();
        if ($query[0] == null)
            return new DateTime("@0");

        $lastDate = $query[0];
        return new DateTime($lastDate);
    }

    private static function getEvents($lastDate) {
        $events = array();
        $fileReader = new FileReader($lastDate);

        while (($line = $fileReader->getLine()) != null) {
            $event = Parser::parseLine($line);
            if ($event && $event->date > $lastDate)
                $events[] = $event;
        }

        return $events;
    }

    private static function putEvents($newEvents) {
        $pool = array();
        foreach($newEvents as $event) {
            $class = get_class($event);
            if (!isset($pool[$class])) $pool[$class] = array();

            $pool[$class][] = $event;
        }
        foreach ($pool as $class => $list)
            $class::saveEvents($class, $list);
    }
}
