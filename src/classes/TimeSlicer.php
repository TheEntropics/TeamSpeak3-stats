<?php


class TimeSlicer {
    private $timePerNumUser = array();
    private $lastEventTime = null;

    public function addTimePerNumUser($numUser, $time) {
        if (!isset($this->timePerNumUser[$numUser]))
            $this->timePerNumUser[$numUser] = 0;
        if ($this->lastEventTime == null)
            $this->lastEventTime = $time;
        $this->timePerNumUser[$numUser] += $time->getTimestamp() - $this->lastEventTime->getTimestamp();
        $this->lastEventTime = $time;
    }

    public function getTimePerNumUser() {
        return $this->timePerNumUser;
    }

    public function getAverageUsers($denominator = -1) {
        $sum = 0;
        $numerator = 0;
        foreach ($this->timePerNumUser as $num_user => $seconds) {
            $sum += $seconds;
            $numerator += $num_user * $seconds;
        }
        if ($denominator > 0) $sum = $denominator;
        if ($sum == 0) return 0;
        return $numerator / $sum;
    }
}
