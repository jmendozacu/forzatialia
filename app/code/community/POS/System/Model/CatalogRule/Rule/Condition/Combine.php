<?php

/**
 *
 */
class POS_System_Model_CatalogRule_Rule_Condition_Combine extends Mage_CatalogRule_Model_Rule_Condition_Combine
{
    const XML_PATH_APPLY_TO_SPECIAL_PRICE = 'retailexpress/coupon_support/apply_catalog_price_rules_to_product_with_special_price';

    // cache data
    protected $_cache = [];

    /**
     * validate product attributes
     * return false if item has special price.
     */
    public function validate(Varien_Object $object)
    {
        if ($this->_applyToSpecialPrice()) {
            return parent::validate($object);
        }
        // do this for POS_SimplePrice_Catalog_Model_Product object type only
        if (get_class($object) == 'POS_SimplePrice_Catalog_Model_Product') {
            // load product
            $productId = $object->getId();
            // return cached value if set
            if (isset($this->_cache[$productId])) {
                return $this->_cache[$productId];
            }
            $product = Mage::getModel('catalog/product')->load($object->getId());
            $price = $product->getPrice();
            $specialPrice = $product->getSpecialPrice();
            if ($specialPrice && $specialPrice != $price) {
                // filter products with special price
                $this->_cache[$productId] = false;

                return $this->_cache[$productId];
            }
        }

        // return parent
        return parent::validate($object);
    }

    protected function _applyToSpecialPrice()
    {
        if (Mage::registry('apply_catalog_rules_to_product_with_special_price') && Mage::registry('apply_catalog_rules_to_product_with_special_price') == 1) {
            return true;
        }
        if (Mage::registry('apply_catalog_rules_to_product_with_special_price') == 0) {
            return false;
        }

        return Mage::getStoreConfig(self::XML_PATH_APPLY_TO_SPECIAL_PRICE) ? Mage::getStoreConfig(self::XML_PATH_APPLY_TO_SPECIAL_PRICE) : false;
    }
}
