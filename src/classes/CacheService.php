<?php


class CacheService {
    public static function updateCache() {
        $lastDate = CacheService::fetchLastDate();
        $events = CacheService::getEvents($lastDate);
        CacheService::putEvents($events);
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
        $query = DB::$DB->query($sql)->fetchAll();
        if (count($query) == 0) return new DateTime("@0");

        return new DateTime($query[0][0]);
    }

    private static function getEvents($lastDate) {
        $events = array();
        $fileReader = new FileReader();

        while (($line = $fileReader->getLine()) != null) {
            $event = Parser::parseLine($line);
            if ($event && $event->date > $lastDate)
                $events[] = $event;
        }

        return $events;
    }

    private static function putEvents($newEvents) {
        foreach ($newEvents as $event)
            $event->saveEvent();
    }
}
