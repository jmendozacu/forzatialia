<?php

class POS_System_Block_Orders extends Mage_Core_Block_Template
{
    public function __construct()
    {
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('In Store Orders'));
        Mage::register('current_customer', Mage::getSingleton('customer/session')->getCustomer());
        $collection = Mage::getModel('retailexpress/orders');
        $collection->loadREX();
        $this->setOrders($collection);
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', ['order_id' => $order->getOrderId()]);
    }
}
