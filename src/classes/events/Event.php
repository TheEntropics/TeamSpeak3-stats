<?php

abstract class Event {

    public $id;
    public $date;
    public $type;
    public $user_id;

    public abstract function saveEvent();
}
