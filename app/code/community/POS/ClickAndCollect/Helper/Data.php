<?php

class POS_ClickAndCollect_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ALLOW_CHECK_AVAILABILITY_CHECK = 'cataloginventory/item_options/allow_check_availability_status';
    const XML_PATH_UNAVAILABLE_MESSAGE = 'cataloginventory/item_options/unavailable_message';

    protected $_code = 'clickandcollect';

    /**
     * check if ClickAndCollect Shipping Method active.
     *
     * @return bool
     */
    public function isMethodActive()
    {
        return $this->getConfigFlag('active');
    }

    /**
     * Retrieve store object.
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * retrieve ClickAndCollect related method config option.
     *
     * @return string
     */
    public function getConfigFlag($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'carriers/'.$this->_code.'/'.$field;

        return Mage::getStoreConfigFlag($path, $this->getStore());
    }

    /**
     * check Global Option "Allow Check Availability Status" status.
     *
     * @return bool
     */
    public function getIsGlobalAllowCheckAvailabilityEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_ALLOW_CHECK_AVAILABILITY_CHECK) ? true : false;
    }
}
