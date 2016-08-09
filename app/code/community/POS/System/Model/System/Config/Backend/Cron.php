<?php

class POS_System_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/sync_retail/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/jobs/sync_retail/run/model';

    const XML_PATH_RETAILEXPRESS_ENABLED = 'groups/main/fields/enabled/value';
    const XML_PATH_RETAILEXPRESS_CRON = 'groups/main/fields/cron/value';

    /**
     * Cron settings after save.
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Log_Cron
     */
    protected function _afterSave()
    {
        $enabled = $this->getData(self::XML_PATH_RETAILEXPRESS_ENABLED);
        $cronSelect = $this->getData(self::XML_PATH_RETAILEXPRESS_CRON);

        if ($enabled) {
            $cronArray = explode('_', $cronSelect);
            $cronExprString = $cronArray[1] * 1 .' '.$cronArray[0] * 1 .' * * *';
        } else {
            $cronExprString = '';
        }
        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (Exception $e) {
            Mage::throwException('Unable to save the cron expression.');
        }
    }
}
