<?php

class Atomic {
    private static $handle = null;

    /**
     * Lock the file, wait until the lock is acquired
     */
    public static function lock() {
        Atomic::createLockFile();
        return flock(Atomic::$handle, LOCK_EX);
    }

    /**
     * Remove the lock from the file
     */
    public static function unlock() {
        Atomic::createLockFile();
        return flock(Atomic::$handle, LOCK_UN);
    }

    /**
     * Check if the file is locked. It doesn't wait for the lock
     * @return bool True if the file is locked, false otherwise
     */
    public static function isLocked() {
        Atomic::createLockFile();
        return !flock(Atomic::$handle, LOCK_EX|LOCK_NB);
    }

    /**
     * Wait for the lock
     * @param float $timeout The timeout to wait before stop trying
     * @return bool False on error
     */
    public static function waitForLock($timeout = 60.0) {
        $start = microtime(true);
        while (Atomic::isLocked() && microtime(true) - $start < $timeout)
            usleep(500000);
        if (microtime(true) - $start >= $timeout)
            return false;
        return Atomic::lock();
    }

    private static function createLockFile() {
        $LOCK_FILE_NAME = __DIR__ . "/../../" . Config::get("log");
        if (!file_exists($LOCK_FILE_NAME))
            fclose(fopen($LOCK_FILE_NAME, 'w'));
        Atomic::$handle = fopen($LOCK_FILE_NAME, 'r');
    }
}
