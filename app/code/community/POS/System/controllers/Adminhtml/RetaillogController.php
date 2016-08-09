<?php

class POS_System_Adminhtml_RetaillogController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/log')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('POS System Logs'), Mage::helper('adminhtml')->__('POS System Logs'));

        return $this;
    }

    public function indexAction()
    {
        $action = $this->_initAction();

        $action->renderLayout();
    }

    /**
     * clearSyncLogsAction.
     *
     * This method calls the model method for deleting all data stored in sync_log table
     */
    public function clearSyncLogsAction()
    {
        $model = Mage::getModel('retailexpress/log');
        $model->deleteLogs();
        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Sync Logs has been cleared.'));
        $this->_redirect('*/*');
    }

    /**
     * viewSyncLogXmlAction.
     *
     * Gets and  renders the xml file on a new window
     */
    public function viewSyncLogXmlAction()
    {
        $this->loadLayout('popup');

        $this->renderLayout();

        $this->loadLayout(false);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/history/retaillog');
    }
}
