<?php

$POS_ClickAndCollect_Rewrite = !Mage::getStoreConfig('advanced/modules_disable_output/POS_ClickAndCollect') &&
    @class_exists('POS_ClickAndCollect_Block_Adminhtml_Catalog_Product_Grid', true);
$POS_MassImageUpload_Rewrite = !Mage::getStoreConfig('advanced/modules_disable_output/POS_MassImageUpload') &&
    @class_exists('POS_MassImageUpload_Block_Adminhtml_Catalog_Product_Grid', true);

if ($POS_ClickAndCollect_Rewrite && $POS_MassImageUpload_Rewrite) {
    class POS_System_Adminhtml_Block_Catalog_Product_Grid_Tmp extends POS_MassImageUpload_Block_Adminhtml_Catalog_Product_Grid
    {
    }
} elseif ($POS_ClickAndCollect_Rewrite) {
    class POS_System_Adminhtml_Block_Catalog_Product_Grid_Tmp extends POS_ClickAndCollect_Block_Adminhtml_Catalog_Product_Grid
    {
    }
} elseif ($POS_MassImageUpload_Rewrite) {
    class POS_System_Adminhtml_Block_Catalog_Product_Grid_Tmp extends POS_MassImageUpload_Block_Adminhtml_Catalog_Product_Grid
    {
    }
} else {
    class POS_System_Adminhtml_Block_Catalog_Product_Grid_Tmp extends Mage_Adminhtml_Block_Catalog_Product_Grid
    {
    }
}

class POS_System_Adminhtml_Block_Catalog_Product_Grid extends POS_System_Adminhtml_Block_Catalog_Product_Grid_Tmp
{
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        $this->getMassactionBlock()->addItem('retail_sync', [
            'label' => Mage::helper('retailexpress')->__('Synchronise POS Stock'),
            'url' => $this->getUrl('retailexpress/adminhtml_retailexpress/syncproduct', ['_current' => true]),
        ]);

        $this->getMassactionBlock()->addItem('Visibility', [
             'label' => Mage::helper('catalog')->__('Set Visibility'),
             'url' => $this->getUrl('retailexpress/adminhtml_retailexpress/massChangeVisibility', ['_current' => true]),
             'additional' => [
                    'visibility' => [
                         'name' => 'visibility',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Visibility'),
                         'values' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
                     ],
             ],
        ]);

        return $this;
    }
}
