<?php

class POS_System_Adminhtml_Block_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected $_added = false;

    public function _beforeToHtml()
    {
        parent::_beforeToHtml();
    }

    public function addTab($tabId, $tab)
    {
        parent::addTab($tabId, $tab);
        if (!$this->_added) {
            $this->_added = true;
            if (Mage::registry('current_customer')->getId()) {
                $this->addTab('customer_edit_tab_rexorders', [
                    'name' => 'customer_edit_tab_rexorders',
                    'block' => 'retailexpress/adminhtml_customer_orders',
                ]);
            }
        }
    }
}
