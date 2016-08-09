<?php

class POS_SimplePrice_Catalog_Block_Product_View_Type_Configurable
    extends Mage_Catalog_Block_Product_View_Type_Configurable
{

    /**
     * used to check if product is a configurable product
     *
     */
	var $has_variant = true;

    /**
     * path to price html template (getPriceHtml) for configurable products
     *
     */
	protected $_priceBlockDefaultTemplate = 'simpleprice/price.phtml';

    /**
     * Initialize
     *
     */
    public function _construct()
    {
    	parent::_construct();
    }

    protected function _preparePrice($price, $isPercent=false)
    {
        return $this->_registerJsPrice($this->_convertPrice(0, true));
    }

    /**
     * Product variants json formatted data
     *
     * @return string html
     */
    protected function showJSVariants()
    {
    	$store = Mage::app()->getStore();
    	$product = $this->getProduct();
    	$info = array();
    	foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info[] = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
            );
    	}

    	$variants = array();
    	foreach($product->getTypeInstance()->getUsedProducts() as $childProduct) {
            $data = array();
            $data['attr'] = array();
            foreach ($info as $attr) {
            	$data['attr'][$attr['id']] = $childProduct->getData($attr['code']);
            }

            $data['price'] = $this->_registerJsPrice($this->_convertPrice($childProduct->getFinalPrice()));
            $data['oldprice'] = $this->_registerJsPrice($this->_convertPrice($childProduct->getPrice()));
            $variants[] = $data;
        }

    	return 'var variantsProduct = ' . Zend_Json::encode($variants) /* Mage::helper('core')->jsonEncode($variants)*/ . ';';
    }

}
