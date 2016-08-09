<?php

class POS_ETA_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ETA_UNAVAILABLE_MESSAGE = 'cataloginventory/item_options/unavailable_message';
    const XML_PATH_ETA_ALLOW_CHECK_AVAILABILITY_STATUS = 'cataloginventory/item_options/allow_check_availability_status';
    const XML_PATH_ETA_CALCULATION_ENABLED = 'retailexpress/eta/enabled';
    const XML_PATH_ETA_MESSAGE = 'retailexpress/eta/eta_message';
    const XML_PATH_SALES_CHANNEL_ID = 'retailexpress/main/sales_channel_id';

    protected $_dateFormat = 'd/m/Y';

    /**
     * Retrieve store object.
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    public function getUnavailableMessage()
    {
        return Mage::getStoreConfig(self::XML_PATH_ETA_UNAVAILABLE_MESSAGE);
    }

    /**
     * returns global configuration flag "Allow Check Availability" value.
     *
     * @return bool
     */
    public function getGlobalAllowCheckAvailabilityStatus()
    {
        return Mage::getStoreConfig(self::XML_PATH_ETA_ALLOW_CHECK_AVAILABILITY_STATUS) ? true : false;
    }

    /**
     * returns global configuration flag "Enable ETA Calculation" value.
     *
     * @return bool
     */
    public function getEtaCalculationEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_ETA_CALCULATION_ENABLED) ? true : false;
    }

    public function getEtaMessage()
    {
        return Mage::getStoreConfig(self::XML_PATH_ETA_MESSAGE);
    }

    public function getChannelId()
    {
        return Mage::getStoreConfig(self::XML_PATH_SALES_CHANNEL_ID);
    }

    public function getMinStockLevel($productId)
    {
        $model = Mage::getModel('cataloginventory/stock_item');
        $stockItem = $model->loadByProduct($productId);

        return $stockItem->getMinQty();
    }

    public function formatEtaDate($date)
    {
        //remove the time from the date.
        $date = explode('T', $date);
        date_default_timezone_set('Australia/ACT');

        return Date($this->_dateFormat, strtotime($date[0]));
    }

    /**
     * checkAvailabilityInStockStatus.
     *
     * This checks if the current qty is available for purchase even if
     * check availability status is enabled
     *
     * @return true  if stock status is ready for purchase (current qty > minqty)
     * @return false if current qty + stockonorder > minqty
     */
    public function checkAvailabilityInStockStatus($productId)
    {
        $model = Mage::getModel('cataloginventory/stock_item');
        $stockItem = $model->loadByProduct($productId);

        switch ($stockItem->getAutoStockStatus()) {
            case 0:
                return 'outofstock';
            break;
            case 1:
                return 'instock';
            break;
            case 2:
                return 'checkavailability';
            break;
        }
    }
}
