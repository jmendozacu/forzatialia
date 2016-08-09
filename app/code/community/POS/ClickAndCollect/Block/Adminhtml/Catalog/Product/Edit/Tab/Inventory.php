<?php

class POS_ClickAndCollect_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    const ALLOWED_CHECK_AVAILABILITY_VALUE = 2;

    protected $_helper = null;

    public function __construct()
    {
        parent::__construct();
        $this->_helper = Mage::helper('clickandcollect');
        $this->setTemplate('clickandcollect/catalog/product/tab/inventory.phtml');
    }

    /**
     * store pickup rule option.
     *
     * @return array
     */
    public function getStorePickupRuleOptions()
    {
        return Mage::getModel('clickandcollect/product_attribute_source_pickuprule')->getAllOptions();
    }

    /**
     * store delivery rule option.
     *
     * @return array
     */
    public function getStoreDeliveryRuleOptions()
    {
        return Mage::getModel('clickandcollect/product_attribute_source_deliveryrule')->getAllOptions();
    }

    /**
     * alias of _getIsGlobalAllowCheckAvailabilityEnabled for extenal use.
     *
     * @return bool
     */
    protected function getIsGlobalAllowCheckAvailabilityEnabled()
    {
        return $this->_getIsGlobalAllowCheckAvailabilityEnabled();
    }

    /**
     * check if global config option "Allow Check Availability" enabled.
     *
     * @return bool
     */
    protected function _getIsGlobalAllowCheckAvailabilityEnabled()
    {
        return $this->_helper->getIsGlobalAllowCheckAvailabilityEnabled();
    }

    /**
     * Retrieve stock option array.
     *
     * @return array
     */
    public function getStockOption()
    {
        $stockOption = Mage::getSingleton('cataloginventory/source_stock')->toOptionArray();
        if ($this->_isCheckAvailabilityOptionVisible()) {
            $stockOption[] = [
                'value' => self::ALLOWED_CHECK_AVAILABILITY_VALUE,
                'label' => 'Check Availability',
            ];
        }

        return $stockOption;
    }

    /**
     * returns the visibility of "Check Availability" stock option.
     *
     * condition:
     * GLOBAL_ENABLE_ETA &&
     * (GLOBAL_ENABLE_CHECK_AVAILABILITY && PRODUCT_USE_CONFIG_CHECK_AVAILABILITY
     *     ||
     * !PRODUCT_USE_CONFIG_CHECK_AVAILABILITY && PRODUCT_CHECK_AVAILABILITY)
     *
     * @return bool
     */
    protected function _isCheckAvailabilityOptionVisible()
    {
        if (
            $this->_etaCalculationEnabled()
            &&
            (
                $this->_getIsGlobalAllowCheckAvailabilityEnabled() && $this->getFieldValue('use_config_allow_check_availability_status')
                ||
                !$this->getFieldValue('use_config_allow_check_availability_status') && $this->getFieldValue('allow_check_availability_status')
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Return is_in_stock_value.
     *
     * @return int
     */
    public function getIsInStockValue()
    {
        return $this->getFieldValue('is_in_stock');
    }

    /**
     * return is ETA calculation configuration option is enabled (System->POS System->Settings->Product ETA->ETA Calculation Enabled).
     *
     * @return bool
     */
    protected function _etaCalculationEnabled()
    {
        if ($etaHelper = Mage::helper('eta')) {
            return $etaHelper->getEtaCalculationEnabled();
        }

        return false;
    }
}
