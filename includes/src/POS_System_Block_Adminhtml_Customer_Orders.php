<?php

class POS_System_Block_Adminhtml_Customer_Orders
 extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_rex_orders_grid');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('In Store Orders');
    }


    public function getAfter()
    {
        return 'orders';
    }


    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('In Store Orders');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/orders', array('_current' => true));
    }

    public function getTabUrl()
    {
        return $this->getUrl('retailexpress/adminhtml_retailexpress/customerorders', array('_current' => true));
    }

    public function getTabClass()
    {
        return "ajax only notloaded";
    }
}
