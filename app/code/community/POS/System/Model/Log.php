<?php

class POS_System_Model_Log extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('retailexpress/log');
    }

    /**
     * saveSyncLogs.
     *
     * This method checks for configuration of logging
     * if logging is enabled this method will save the sync log to sync_log table/model
     *
     * @param $importData xml
     * @param $lastRequest xml
     * @param $type string
     * @param $requestType string
     */
    public function saveSyncLogs($importData, $lastRequest, $type, $requestType)
    {
        $configData = 0;

        if ($requestType == 'BULK') {
            $configData = Mage::getStoreConfig('retailexpress/log_main/logging_bulk_request');
        } else {
            $configData = Mage::getStoreConfig('retailexpress/log_main/logging_enabled');
        }

        if ($configData == 1) {
            $log = Mage::getModel('retailexpress/log');

            $log->setData(
                [
                      'method' => $type,
                      'sync_response' => print_r($importData, true),
                      'sync_request' => $lastRequest,
                ]
            );

            try {
                $log->save();
            } catch (Exception $e) {
                mage::log($e->getMessage(), 'possystem.log');
            }
        }
    }

    /**
     * deleteLogs.
     *
     * Method that clears all data on sync_log table
     */
    public function deleteLogs()
    {
        $coreResource = Mage::getSingleton('core/resource');

        $write = $coreResource->getConnection('core_write');

        $query = 'TRUNCATE TABLE sync_log';

        $write->query($query);
    }
}
