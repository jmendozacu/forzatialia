<?php

class POS_System_Block_Adminhtml_Diagnostic extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_diagnostic';
        $this->_blockGroup = 'retailexpress';
        $this->_headerText = Mage::helper('retailexpress/data')->__('Diagnostic Tool');

        parent::__construct();
        $this->setTemplate('retailexpress/diagnostic.phtml');
        $this->removeButton('add');

        $this->_addButton('diag', [
            'label' => Mage::helper('core')->__('Run Diagnostic'),
            'onclick' => 'initDiag(); return false;',
//            'class'     => 'save',
            'id' => 'runDiagButton',
        ]);
    }

    public function getClassNameByStatus($status)
    {
        switch ($status) {
            case 'fail':
                return 'major';
                break;
            case 'success':
                return 'notice';
                break;
            case 'warning':
                return 'minor';
                break;
            case 'error':
                return 'critical';
                break;
            default:
            case 'unknown':
                return 'unknown';
                break;
        }
    }
}
