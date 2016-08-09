<?php

class POS_SimplePrice_Model_Observer
{
    /**
     * path to price html template (getPriceHtml) for configurable products.
     */
    protected $_priceBlockDefaultTemplate = 'simpleprice/price.phtml';

    /**
     * replace price html template for configurable products exluding tierprices.
     */
    public function replacePriceHtml($observer = null)
    {
        if ('POS_SimplePrice_Catalog_Block_Product_Price' == get_class($observer->getEvent()->getBlock())) {
            $template = $observer->getEvent()->getBlock()->getTemplate();
            if (strpos($template, 'tierprices.phtml') === false) {
                if ($observer->getEvent()->getBlock()->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $observer->getEvent()->getBlock()->setTemplate($this->_priceBlockDefaultTemplate);
                }
            }
        }
    }
}
