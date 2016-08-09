<?php

class REX_Singlethreader
{
    public static function isLocked()
    {
        return file_exists(self::getLockFile());
    }

    public static function locked(callable $callback)
    {
        if (self::isLocked()) {
            throw new Exception('Cannot execute callback while locked.');
        }

        self::lock();

        try {
            $response = $callback();
            self::unlock();
        } catch (Exception $e) {
            self::unlock();
            throw $e;
        }

        return $response;
    }

    public static function hasStaleLockFile()
    {
        $file = self::getLockFile();

        if (!file_exists($file)) {
            return false;
        }

        $modified = @filemtime($file);

        if ($modified === false) {
            throw new Exception("Cannot read lock file {$file} to check if it's stale.");
        }

        // If the time time difference between now and when the file was modified, we'll remove the file altogether.
        return time() - $modified > self::getStaleTimeInSeconds();
    }

    /**
     * Public method to force the single threader to unlock, with an obvious method name
     * so that any developer doesn't just add this to their code.
     */
    public static function forceUnlock()
    {
        self::unlock();
    }

    protected static function lock()
    {
        $file = self::getLockFile();

        if (file_exists($file)) return;

        if (!@touch($file)) {
            throw new Exception("Failed to create lock file {$file} for single threading. Check permissions?");
        }
    }

    protected static function unlock()
    {
        $file = self::getLockFile();

        if (!file_exists($file)) return;

        if (!@unlink($file)) {
            throw new Exception("Failed to remove lock file {$file} for single threading. Check permissions?");
        }
    }

    protected static function getLockFile()
    {
        return Mage::getBaseDir('var').DS.'retail'.DS.'.lock';
    }

    protected static function getStaleTimeInSeconds()
    {
        return 86400; // 1 day
    }
}
