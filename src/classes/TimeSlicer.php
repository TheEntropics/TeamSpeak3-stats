<?php


class TimeSlicer {
    private $timePerNumUser = array();
    private $lastEventTime = null;

    /**
     * Add some time to a user key
     * @param $numUser The number of online users
     * @param $time The time to add
     */
    public function addTimePerNumUser($numUser, $time) {
        if (!isset($this->timePerNumUser[$numUser]))
            $this->timePerNumUser[$numUser] = 0;
        if ($this->lastEventTime == null)
            $this->lastEventTime = $time;
        $this->timePerNumUser[$numUser] += Utils::getTimestamp($time) - Utils::getTimestamp($this->lastEventTime);
        $this->lastEventTime = $time;
    }

    /**
     * Returns the raw information
     * @return array
     */
    public function getTimePerNumUser() {
        return $this->timePerNumUser;
    }

    /**
     * Compute the average number of users
     * @param int $denominator The denominator of the fraction. If it is less than zero it use an automatic value
     * @return float|int
     */
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
