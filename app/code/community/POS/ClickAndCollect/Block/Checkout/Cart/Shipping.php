<?php


class POS_ClickAndCollect_Block_Checkout_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
    /**
     * This method checks if at least one product has a value of "No Delivery"
     * for Store Delivery Rule.
     *
     * @return bool
     */
    public function hideDeliveryRuleQuote()
    {
        return !Mage::getModel('clickandcollect/clickAndCollect')->cartAllowsDelivery();
    }

    /**
     * Show State in Shipping Estimation.
     *
     * @return bool
     */
    public function getStateActive()
    {
        if ((bool) Mage::getStoreConfig('carriers/clickandcollect/active')) {
            return true;
        }

        return parent::getStateActive();
    }
}
