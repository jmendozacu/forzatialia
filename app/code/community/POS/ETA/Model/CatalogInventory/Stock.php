<?php

/**
 */

/**
 */
class POS_ETA_Model_CatalogInventory_Stock extends Mage_CatalogInventory_Model_Stock
{
    //    const XML_PATH_ALLOW_CHECK_AVAILABILITY_STATUS = 'cataloginventory/item_options/allow_check_availability_status';

    const GLOBAL_ALLOW_CHECK_AVAILABILITY_STATUS_NO = 0;
    const GLOBAL_ALLOW_CHECK_AVAILABILITY_STATUS_YES = 1;

    protected function _construct()
    {
        return parent::_construct();
    }

    // @move to helper
//    public function isGlobalAllowCheckAvailabilityStatus()
//    {
//        return Mage::getStoreConfig(self::XML_PATH_ALLOW_CHECK_AVAILABILITY_STATUS) ? true : false;
//    }
}
