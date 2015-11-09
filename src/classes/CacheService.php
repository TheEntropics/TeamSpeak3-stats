<?php


class CacheService {
    public static function updateCache() {
        $lastRecord = CacheService::fetchLastRecord();
        $events = CacheService::getEvents($lastRecord);
        CacheService::putEvents($events);
    }

    private static function fetchLastRecord() {
        // TODO trovare l'evento più nuovo nel database
    }

    private static function getEvents($lastRecord) {
        // TODO ottenere una lista con gli eventi successivi a quello specificato
    }

    private static function putEvents($newEvents) {
        // TODO inserire nel DB tutti gli eventi specificati
    }
}