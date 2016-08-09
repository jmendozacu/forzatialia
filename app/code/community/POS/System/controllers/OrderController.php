<?php

class POS_System_OrderController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->getLayout()->getBlock('head')->setTitle($this->__('In Store Orders'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Order #'.$this->getRequest()->getParam('order_id')));
        $this->renderLayout();
    }
}
