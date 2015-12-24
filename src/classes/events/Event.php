<?php

abstract class Event {

    const MAX_EVENTS_IN_INSERT = 500;

    public $id;
    public $date;
    public $type;
    public $user_id;

    public abstract function saveEvent();

    public static function saveEvents($class, $list) {
        if (count($list) > Event::MAX_EVENTS_IN_INSERT) {
            $chunks = array_chunk($list, Event::MAX_EVENTS_IN_INSERT);
            foreach($chunks as $chunk)
                Event::saveEvents($class, $chunk);
        } else {
            $sql = $class::getInsertHeader();
            $chunks = array_map("$class::getInsertString", $list);
            $sql .= implode(', ', $chunks);

            DB::$DB->query($sql);
        }
    }
    protected static abstract function getInsertHeader();
    protected static abstract function getInsertString($event);
}
