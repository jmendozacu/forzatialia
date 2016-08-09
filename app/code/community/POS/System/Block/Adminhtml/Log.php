<?php

class POS_System_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_log';
        $this->_blockGroup = 'retailexpress';
        $this->_headerText = Mage::helper('retailexpress/data')->__('POS System Logs');

        $message = Mage::helper('core')->__('Are you sure you want to clear all the saved logs?');

        $this->_addButton('log', [
            'label' => Mage::helper('core')->__('Clear Logs'),
            'onclick' => 'confirmSetLocation(\''.$message.'\', \''.$this->getUrl('*/*/clearSyncLogs').'\')',
            'class' => 'delete',
        ]);

        parent::__construct();
        $this->setTemplate('retailexpress/log.phtml');
        $this->removeButton('add');
    }

    /**
     * isLogEnabled Function.
     *
     * Checks if the log config is enabled
     *
     * @return bool
     */
    public function isLogEnabled()
    {
        return (Mage::getStoreConfig('retailexpress/log_main/logging_enabled'));
    }
}
