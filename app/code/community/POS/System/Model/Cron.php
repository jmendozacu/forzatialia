<?php

class POS_System_Model_Cron extends Mage_Core_Model_Abstract
{
    /**
     * Daily update.
     */
    public function update()
    {
        if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
            return;
        }

        Mage::helper('retailexpress/data')->waitBulk();
    }

    /**
     * process sync for products bulk method.
     */
    public function synchronizeBulk()
    {
        if (!Mage::getStoreConfigFlag('retailexpress/main/enabled')) {
            return;
        }
        /** @var POS_System_Helper_Data $helper */
        $helper = Mage::helper('retailexpress/data');

        if ($helper->isPcntl()) {
            pcntl_signal(SIGTERM, [$this, 'signalHandler']);
            pcntl_signal(SIGINT, [$this, 'signalHandler']);
        }
        if (Mage::getStoreConfigFlag('retailexpress/log_main/logging_enabled')) {
            Varien_Profiler::enable();
        }

        $helper->synchronizeBulk();
    }

    /**
     * dump Cron Log.
     *
     * Checks and creates cron logs foreach run
     */
    public function dumpCronLog()
    {
        $logDir = Mage::getBaseDir('var').DS.'log'.DS;

        if (!file_exists($logDir)) {
            mkdir($logDir);
            if (!is_writeable($logDir)) {
                chmod($logDir, 0777);
            }
        }
        file_put_contents($logDir.'cron.log', date('M-d-Y H:i:s'), LOCK_EX);
    }

    public function signalHandler()
    {
        REX_Logger::log('Was caught a signal ', REX_Logger::TYPE_SYNC);
    }

    public function refreshUrlKeys()
    {
        if (!Mage::getStoreConfigFlag('retailexpress/main/enabled')) {
            return;
        }

        Mage::getModel('retailexpress/urlkeyrefresher')
            ->run();
    }

    public function clearOldSinglethreadedLockFiles()
    {
        if (REX_Singlethreader::hasStaleLockFile()) {
            REX_Singlethreader::forceUnlock();
        }
    }
}
