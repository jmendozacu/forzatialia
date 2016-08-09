<?php

/**
 */
if (class_exists('POS_SimplePrice_Catalog_Block_Product_View_Type_Configurable', true)) {
    class POS_ETA_Block_Catalog_Product_View_Type_ConfigurableTmp extends POS_SimplePrice_Catalog_Block_Product_View_Type_Configurable
    {
    }
} else {
    class POS_ETA_Block_Catalog_Product_View_Type_ConfigurableTmp extends Mage_Catalog_Block_Product_View_Type_Configurable
    {
    }
}

/**
 */
class POS_ETA_Block_Catalog_Product_View_Type_Configurable extends POS_ETA_Block_Catalog_Product_View_Type_ConfigurableTmp
{
    public function _construct()
    {
        parent::_construct();
    }

    public function getEtaUrl()
    {
        return $this->getBaseUrl().'poseta/ajax/geteta/';
    }

    public function getEtaMessage()
    {
        return Mage::helper('eta')->getEtaMessage();
    }

    public function getAllowCheckAvailability()
    {
        $stockItemModel = Mage::getModel('cataloginventory/stock_item');
        $stockStatus = $stockItemModel->getAllowCheckAvailabilityStatus();
        //$product = $this->getProduct()->getStockItem();//->getStockData();
        return $stockItemModel->getProductId();
    }

    /**
     * Composes configuration for js.
     *
     * @return string
     */
    public function getJsonPosEtaData()
    {
        $options = [];
        $store = $this->getCurrentStore();

        foreach ($this->getAllowProducts() as $product) {
            $productId = $product->getId();
            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                $options[$productId][$productAttributeId] = $attributeValue;
            }
        }

        foreach ($options as $key => $option) {
            $sKey = [];
            $attribute = [];
            $attributeConfig = [];
            foreach ($option as $attr => $value) {
                $sKey[] .= $attr.'_'.$value;
                $attribute[] = $attr;
                $attributeConfig['attribute'.$attr] = $attr;
            }
            $_config['attr_'.implode('_', $sKey).'_'] = $key;
        }

        $config = [
            'config' => $_config,
            'attribute' => $attribute,
            'attributeConfig' => $attributeConfig,
        ];

        return Mage::helper('core')->jsonEncode($config);
    }
}
