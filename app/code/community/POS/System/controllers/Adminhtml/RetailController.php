<?php

/**
 * Retail controller.
 *
 *
 * Redirect controller just for the link problem on settings tab
 *
 * @author chris@retailexpress.com.au
 */
class POS_System_Adminhtml_RetailController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $url = Mage::helper('adminhtml')->getUrl('/system_config/edit/section/retailexpress');

        //replace "retailexpress word to admin on the url"

        $url = str_replace('/retailexpress/system_config', '/admin/system_config', $url);

        Mage::app()->getResponse()->setRedirect($url);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/history/retailexpress');
    }
}
