<?php

class POS_ClickAndCollect_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    /**
     * @return POS_ClickAndCollect_Carrier_ClickAndCollect
     */
    protected function _getCarrier()
    {
        return Mage::getModel('clickandcollect/carrier_clickandcollect');
    }

    public function getShippingRates()
    {
        return parent::getShippingRates();
    }

    /**
     * check by code if ClickAndCollect method used.
     *
     * @return bool
     */
    public function isClickAndCollectMethod($code = null)
    {
        if (!$code) {
            return false;
        }
        if ($code == 'clickandcollect') {
            return true;
        }
        $code = explode('_', $code);
        if ($code[0] == 'clickandcollect') {
            return true;
        }

        return false;
    }

    /**
     * get shipping method data.
     *
     * @return array
     */
    public function getShippingData()
    {
        return $this->_getCarrier()->getShippingData();
    }

    /**
     * returns true for ClickAndCollect method
     * returns true if ClickAndCollect method disabled or any product allows Delivery (Store Delivery Rule).
     *
     * @return bool
     */
    public function isMethodAllowed($code)
    {
        if ($code == 'clickandcollect') {
            return true;
        }
        if (!Mage::helper('clickandcollect')->isMethodActive()) {
            return true;
        } elseif (!is_null(Mage::registry('deliveryrule'))) {
            return Mage::registry('deliveryrule');
        } else {
            return $this->_cartAllowsDelivery();
        }
    }

    protected function _cartAllowsDelivery()
    {
        return Mage::getModel('clickandcollect/clickAndCollect')->cartAllowsDelivery();
    }
}
