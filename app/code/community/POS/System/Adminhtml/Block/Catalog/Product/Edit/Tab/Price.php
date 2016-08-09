<?php

class POS_System_Adminhtml_Block_Catalog_Product_Edit_Tab_Price extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price
{
    protected function _prepareForm()
    {
        $return = parent::_prepareForm();
        $fieldset = $this->getForm()->addFieldset('pos_price', ['legend' => Mage::helper('catalog')->__('POS Pricing')]);

        $fieldset->addField('pos_price', 'text', [
                'name' => 'pos_price',
                'class' => 'requried-entry',
                'value' => '',
        ]);

        $this->getForm()->getElement('pos_price')->setRenderer(
            $this->getLayout()->createBlock('retailexpress/adminhtml_price')
        );

        return $return;
    }
}
