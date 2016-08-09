<?php

class POS_ClickAndCollect_Model_ClickAndCollect extends Mage_Core_Model_Abstract
{
    /**
     * does cart have items with Delivery Allowed.
     *
     * @return bool
     */
    public function cartAllowsDelivery()
    {
        if (!is_null(Mage::registry('deliveryrule'))) {
            return Mage::registry('deliveryrule');
        }
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            if ($item->getProductId()) {
                $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($_product->getStoreDeliveryRule() == '' || $_product->getStoreDeliveryRule() == 1) {
                    Mage::register('deliveryrule', true, true);

                    return true;
                }
            }
        }
        Mage::register('deliveryrule', false, true);

        return false;
    }
}
