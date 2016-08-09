<?php

class POS_System_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{

    protected function _prepareForm()
    {
        $return = parent::_prepareForm();
        $form = $this->getForm();
        if ($posPrice = $form->getElement('pos_prices')) {
            $posPrice->setRenderer(
                $this->getLayout()->createBlock('retailexpress/adminhtml_price')
            );
        }

        return $return;
    }
}
 
