<?php

class POS_System_Block_Adminhtml_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_history';
        $this->_blockGroup = 'retailexpress';
        $this->_headerText = Mage::helper('retailexpress/data')->__('Synchronisation History Manager');
		$this->_addButton('products', array(
            'label'     => 'Synchronise Products',
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/syncProductBulk') .'\')',
        ));
		$this->_addButton('customers', array(
            'label'     => 'Synchronise Customers',
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/syncCustomers') .'\')',
        ));
		$this->_addButton('orders', array(
            'label'     => 'Synchronise Orders',
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/syncOrders') .'\')',
        ));
		$this->_addButton('cancel', array(
            'label'     => 'Cancel Synchronise',
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/cancel') .'\')',
        ));
        parent::__construct();
        $this->setTemplate('retailexpress/history.phtml');
        $this->removeButton('add');
    }

    /**
     * Show or not waiting message for product import
     *
     * @return bool
     */
    public function showProductWait()
    {
        return (Mage::helper('retailexpress')->getProductBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_WAIT);
    }

    /**
     * Show or not schedule message for product import
     * 
     * @return bool
     */
    public function showProductSchedule()
    {
        return (Mage::helper('retailexpress')->getProductBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_SCHEDULE);
    }

    /**
     * Show or not in progress message for product import
     *
     * @return bool
     */
    public function showProductInProgress()
    {
        return in_array(Mage::helper('retailexpress')->getProductBulkSynchronizationStatus(), array(POS_System_Model_Task_Status::STATUS_INPROGRESS, POS_System_Model_Task_Status::STATUS_INPROSHE));
    }

    /**
     * Show or not in done message for product import
     *
     * @return bool
     */
    public function showProductDone()
    {
        return (Mage::helper('retailexpress')->getProductBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_DONE);
    }

    /**
     * Show or not cancel message for product import
     *
     * @return bool
     */
    public function showProductCancel()
    {
        return (Mage::helper('retailexpress')->getProductBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_CANCEL);
    }

    /**
     * get number of imported products
     *
     * @return int
     */
    public function getProductImported()
    {
        return Mage::getModel('retailexpress/import_product')->getPosition();
    }

    /**
     * get number of total products
     *
     * @return int
     */
    public function getProductTotal()
    {
        return Mage::getModel('retailexpress/import_product')->getTotal();
    }

    /**
     * get products last imported time
     *
     * @return string - date time
     */
    public function getProductLastTime()
    {
        return Mage::getModel('retailexpress/import_product')->getLastTime();
    }

    /**
     * Show or not waiting message for product import
     *
     * @return bool
     */
    public function showCustomerWait()
    {
        return (Mage::helper('retailexpress')->getCustomerBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_WAIT);
    }

    /**
     * Show or not schedule message for customer import
     * 
     * @return bool
     */
    public function showCustomerSchedule()
    {
        return (Mage::helper('retailexpress')->getCustomerBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_SCHEDULE);
    }

    /**
     * Show or not in progress message for customer import
     *
     * @return bool
     */
    public function showCustomerInProgress()
    {
        return in_array(Mage::helper('retailexpress')->getCustomerBulkSynchronizationStatus(), array(POS_System_Model_Task_Status::STATUS_INPROGRESS, POS_System_Model_Task_Status::STATUS_INPROSHE));
    }

    /**
     * Show or not in done message for customer import
     *
     * @return bool
     */
    public function showCustomerDone()
    {
        return (Mage::helper('retailexpress')->getCustomerBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_DONE);
    }

    /**
     * Show or not cancel message for customer import
     *
     * @return bool
     */
    public function showCustomerCancel()
    {
        return (Mage::helper('retailexpress')->getCustomerBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_CANCEL);
    }

    /**
     * get number of imported customers
     *
     * @return int
     */
    public function getCustomerImported()
    {
        return Mage::getModel('retailexpress/import_customer')->getPosition();
    }

    /**
     * get number of total customers
     *
     * @return int
     */
    public function getCustomerTotal()
    {
        return Mage::getModel('retailexpress/import_customer')->getTotal();
    }

    /**
     * get customers last imported time
     *
     * @return string - date time
     */
    public function getCustomerLastTime()
    {
        return Mage::getModel('retailexpress/import_customer')->getLastTime();
    }

    /**
     * Get type of import
     *
     * @return string - Import type
     */
    public function getImportType()
    {
        return $this->__(Mage::getModel('retailexpress/system_method')->getValueById(Mage::getStoreConfig('retailexpress/main/sync_type')));
    }

}
