<?php

class Atomic {
    const LOCK_FILE_NAME = Config::APP_LOG_FILE;
    private static $handle = null;

    public static function lock() {
        Atomic::createLockFile();
        return flock(Atomic::$handle, LOCK_EX);
    }

    public static function unlock() {
        Atomic::createLockFile();
        return flock(Atomic::$handle, LOCK_UN);
    }

    public static function isLocked() {
        Atomic::createLockFile();
        return !flock(Atomic::$handle, LOCK_EX|LOCK_NB);
    }

    public static function waitForLock($timeout = 60.0) {
        $start = microtime(true);
        while (Atomic::isLocked() && microtime(true) - $start < $timeout)
            usleep(500000);
        if (microtime(true) - $start >= $timeout)
            return false;
        return Atomic::lock();
    }

    private static function createLockFile() {
        if (!file_exists(Atomic::LOCK_FILE_NAME))
            fclose(fopen(Atomic::LOCK_FILE_NAME, 'w'));
        Atomic::$handle = fopen(Atomic::LOCK_FILE_NAME, 'r');
    }
}
