<?php
/**
 * Magento Booster 1.4+
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpagecache
 * @version      4.0.5
 * @license:     AACcewAJ3nZYMUsItZcwugZ3g4HsbQPMHWb0Pv6oyc
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpagecache_Adminhtml_AitpagecacheController extends Mage_Adminhtml_Controller_Action
{
    public function pendingAction()
    {
        #Mage::getModel('aitpagecache/observer_emails')->send();
        $this->loadLayout()
            ->_setActiveMenu('newsletter/aitpagecache')
            ->_addBreadcrumb(Mage::helper('aitpagecache')->__('Magento Booster Pending Emails'), Mage::helper('aitpagecache')->__('Magento Booster Pending Emails'));
        $this->_title(Mage::helper('aitpagecache')->__('Magento Booster'))->_title(Mage::helper('aitpagecache')->__('Pending Emails'));
        $this->_addContent($this->getLayout()->createBlock('aitpagecache/adminhtml_emailsPending'));
        $this->renderLayout();
    }

    public function sentAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('newsletter/aitpagecache')
            ->_addBreadcrumb(Mage::helper('aitpagecache')->__('Magento Booster Sent Emails'), Mage::helper('aitpagecache')->__('Magento Booster Sent Emails'));
        $this->_title(Mage::helper('aitpagecache')->__('Magento Booster'))->_title(Mage::helper('aitpagecache')->__('Sent Emails'));
        $this->_addContent($this->getLayout()->createBlock('aitpagecache/adminhtml_emailsSent'));
        $this->renderLayout();
    }
}