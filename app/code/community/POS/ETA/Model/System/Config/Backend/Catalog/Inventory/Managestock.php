<?php

/**
 */

/**
 * ETA Catalog Inventory Manage Stock Config Backend Model.
 */
class POS_ETA_Model_System_Config_Backend_Catalog_Inventory_Managestock
    extends Mage_Core_Model_Config_Data
{
    const XML_PATH_ALLOW_CHECK_AVAILABILITY_STATUS = 'cataloginventory/item_options/allow_check_availability_status';

    /**
     * Before change Catalog Inventory Allow "Check Availability" Status.
     *
     * @return POS_System_Model_System_Config_Backend_Catalog_Inventory_Managestock
     */
    protected function _beforeSave()
    {
        $currentValue = Mage::getStoreConfig(self::XML_PATH_ALLOW_CHECK_AVAILABILITY_STATUS);
        $requestValue = $this->getValue();
        // if value didn't changed than return
        if ($currentValue == $requestValue) {
            return $this;
        }

        // register global variable to check in observer handler
        if ($requestValue) {
            Mage::register('allow_check_availability_status', 'enabled');
        }
        if (!$requestValue) {
            Mage::register('allow_check_availability_status', 'disabled');
        }

        return $this;
    }
}
