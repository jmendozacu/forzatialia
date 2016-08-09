<?php

class POS_System_Block_Order extends Mage_Core_Block_Template
{


    public function __construct()
    {
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('In Store Orders'));
        Mage::register('current_customer', Mage::getSingleton('customer/session')->getCustomer());
        $this->setOrder(Mage::helper('retailexpress')->getOrderHistory($this->getRequest()->getParam('order_id')));
    }


    public function getShippingAddress()
    {
        $b_data = array();
        foreach ($this->getOrder()->getData() as $k => $v) {
            $_t = explode('_', $k, 2);
            if ($_t[0] == 'b') {
                $b_data[$_t[1]] = $v;
            }
        }

        return Mage::getModel('customer/address')->addData($b_data);
    }


    public function getBillingAddress()
    {
        $b_data = array();
        foreach ($this->getOrder()->getData() as $k => $v) {
            $_t = explode('_', $k, 2);
            if ($_t[0] == 'b') {
                $b_data[$_t[1]] = $v;
            }
        }

        return Mage::getModel('customer/address')->addData($b_data);
    }

    public function getPaymentInfoHtml()
    {
        return Mage::getModel('retailexpress/payment')
            ->getCollection()
            ->addFieldToFilter('rex_id', $this->getOrder()->getPay())->getFirstItem()->getName();
    }

}
 
