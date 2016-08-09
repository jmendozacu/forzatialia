<?php

class POS_SimplePrice_SalesRule_Model_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{

    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_qty'] = Mage::helper('salesrule')->__('Quantity in cart');
        $attributes['quote_item_price'] = Mage::helper('salesrule')->__('Price in cart');
        $attributes['quote_item_row_total'] = Mage::helper('salesrule')->__('Row total in cart');
    }

    /**
     * Validate Product Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $product = Mage::getModel('catalog/product')
            ->load($object->getProductId())
            ->setQuoteItemQty($object->getQty())
            ->setQuoteItemPrice($object->getPrice())
            ->setQuoteItemRowTotal($object->getRowTotal());
        $cor_product = $object->getProduct();
        $keys = $cor_product->getRewriteParams();
        foreach ($keys as $k) {
            $product->setData($k, $cor_product->getData($k));
        }
        return parent::validate($product);
    }
    
}
