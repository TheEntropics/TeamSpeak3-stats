<?php

abstract class Event {

    /**
     * Max number of events in an insert query
     */
    const MAX_EVENTS_IN_INSERT = 500;

    /**
     * @var int
     */
    public $id;
    /**
     * @var DateTime
     */
    public $date;
    /**
     * @var int|EventType
     */
    public $type;
    /**
     * @var int
     */
    public $user_id;

    /**
     * Save the event, create it only when new
     */
    public function saveEvent() {
        if ($this->id) $this->updateEvent();
        else $this->createEvent();
    }

    /**
     * Update the event using the stored id
     */
    protected abstract function updateEvent();
    /**
     * Create the new event
     */
    protected abstract function createEvent();

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
