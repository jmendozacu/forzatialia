<?php

/**
 *
 */
class POS_System_Model_SalesRule_Rule_Condition_Product_Combine extends Mage_SalesRule_Model_Rule_Condition_Product_Combine
{
    const XML_PATH_APPLY_TO_SPECIAL_PRICE = 'retailexpress/coupon_support/apply_shopping_cart_price_rules_to_product_with_special_price';

    // cache data
    protected $_cache = [];

    /**
     * validate quote item rule attributes
     * return false if item has special price.
     */
    public function validate(Varien_Object $object)
    {
        if ($this->_applyToSpecialPrice()) {
            return parent::validate($object);
        }
        // do this for Mage_Sales_Model_Quote_Item object type only
        if (get_class($object) == 'Mage_Sales_Model_Quote_Item') {
            // check if configurable product in cart
            if ($childProduct = $object->getProduct()->getSelectedProduct()) {
                $product = $childProduct;
            } else {
                $product = $object->getProduct();
            }
            $productId = $product->getId();

            // return cached value if set
            if (isset($this->_cache[$productId])) {
                return $this->_cache[$productId];
            }
            $price = $product->getPrice();
            $specialPrice = $product->getSpecialPrice();
            if ($specialPrice && $specialPrice < $price) {
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
        return Mage::getStoreConfig(self::XML_PATH_APPLY_TO_SPECIAL_PRICE) ? Mage::getStoreConfig(self::XML_PATH_APPLY_TO_SPECIAL_PRICE) : false;
    }
}
