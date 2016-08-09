<?php

class REX_Profiler
{
    public static function log($msg)
    {
        REX_Logger::log($msg, REX_Logger::TYPE_PERFORMANCE, REX_Logger::CAT_PROFILER);
    }

    public static function logAll()
    {
        foreach (Varien_Profiler::getTimers() as $timer => $results) {
            if (stripos($timer, 'REX') > -1) {
                REX_Logger::log($timer.': '.round($results['sum'], 4), REX_Logger::TYPE_PERFORMANCE, REX_Logger::CAT_PROFILER);
            }
        }
    }
}
