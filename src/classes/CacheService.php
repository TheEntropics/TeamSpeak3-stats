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

        $sql = "INSERT INTO misc_results (`key`, `value`) VALUES ('lastDate', ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
        $query = DB::$DB->prepare($sql);
        $query->execute(array($lastDate));

        return new DateTime($lastDate);
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
