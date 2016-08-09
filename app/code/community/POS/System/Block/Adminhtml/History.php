<?php

class POS_System_Block_Adminhtml_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $customerImport = null;
    protected $productImport = null;
    protected $helper = null;

    public function __construct()
    {
        $this->_controller = 'adminhtml_history';
        $this->_blockGroup = 'retailexpress';
        $this->_headerText = Mage::helper('retailexpress/data')->__('Synchronisation History Manager');
        $this->_addButton('outlets', [
            'label' => 'Synchronise Outlets',
            'onclick' => 'setLocation(\''.$this->getUrl('*/*/syncOutlet').'\')',
        ]);
        $this->_addButton('products', [
            'label' => 'Synchronise Products',
            'onclick' => 'setLocation(\''.$this->getUrl('*/*/syncProductBulk').'\')',
        ]);
        $this->_addButton('customers', [
            'label' => 'Synchronise Customers',
            'onclick' => 'setLocation(\''.$this->getUrl('*/*/syncCustomers').'\')',
        ]);
        $this->_addButton('orders', [
            'label' => 'Synchronise Orders',
            'onclick' => 'setLocation(\''.$this->getUrl('*/*/syncOrders').'\')',
        ]);
        $this->_addButton('cancel', [
            'label' => 'Cancel Synchronise',
//            'onclick'   => 'confirmSetLocation(\'Cancel the current scheduled or ran synchronisation. Are you sure?\',\'' . $this->getUrl('*/*/cancel') .'\')',
            'onclick' => 'setLocation(\''.$this->getUrl('*/*/cancel').'\')',
//            'class'     => 'cancel'
        ]);

        $this->customerImport = Mage::getSingleton('retailexpress/import_customer');
        $this->productImport = Mage::getSingleton('retailexpress/import_product');
        $this->helper = Mage::helper('retailexpress');

        parent::__construct();
        $this->setTemplate('retailexpress/history.phtml');
        $this->removeButton('add');
    }

    /**
     * Show or not waiting message for product import.
     *
     * @return bool
     */
    public function showProductWait()
    {
        return ($this->helper->getProductBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_WAIT);
    }

    /**
     * Show or not schedule message for product import.
     *
     * @return bool
     */
    public function showProductSchedule()
    {
        return ($this->helper->getProductBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_SCHEDULE);
    }

    /**
     * Show or not in progress message for product import.
     *
     * @return bool
     */
    public function showProductInProgress()
    {
        return in_array($this->helper->getProductBulkSynchronizationStatus(), [POS_System_Model_Task_Status::STATUS_INPROGRESS, POS_System_Model_Task_Status::STATUS_INPROSHE]);
    }

    /**
     * Show or not in done message for product import.
     *
     * @return bool
     */
    public function showProductDone()
    {
        return ($this->helper->getProductBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_DONE);
    }

    /**
     * Show or not cancel message for product import.
     *
     * @return bool
     */
    public function showProductCancel()
    {
        return ($this->helper->getProductBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_CANCEL);
    }

    /**
     * get number of imported products.
     *
     * @return int
     */
    public function getProductImported()
    {
        return $this->productImport->getPosition();
    }

    /**
     * get number of total products.
     *
     * @return int
     */
    public function getProductTotal()
    {
        return $this->productImport->getTotal();
    }

    /**
     * get products last imported time.
     *
     * @return string - date time
     */
    public function getProductLastTime()
    {
        return $this->productImport->getLastTime();
    }

    protected function _getHumanTime($minutes = 0)
    {
        $text = [];

        if ($minutes == 0) {
            return ' few minutes';
        }

        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $text[] = $hours.($hours > 1 ? ' hours' : ' hour');
            $minutes = $minutes - ($hours * 60);
        }

        if ($minutes > 0) {
            $text[] = $minutes.($minutes > 1 ? ' minutes' : ' minute');
        }

        return implode(' ', $text);
    }

    public function getProductWait()
    {
        return $this->_getHumanTime($this->productImport->getWait() * 5);
    }

    /**
     * Show or not waiting message for product import.
     *
     * @return bool
     */
    public function showCustomerWait()
    {
        return ($this->helper->getCustomerBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_WAIT);
    }

    /**
     * Show or not schedule message for customer import.
     *
     * @return bool
     */
    public function showCustomerSchedule()
    {
        return ($this->helper->getCustomerBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_SCHEDULE);
    }

    /**
     * Show or not in progress message for customer import.
     *
     * @return bool
     */
    public function showCustomerInProgress()
    {
        return in_array($this->helper->getCustomerBulkSynchronizationStatus(), [POS_System_Model_Task_Status::STATUS_INPROGRESS, POS_System_Model_Task_Status::STATUS_INPROSHE]);
    }

    /**
     * Show or not in done message for customer import.
     *
     * @return bool
     */
    public function showCustomerDone()
    {
        return ($this->helper->getCustomerBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_DONE);
    }

    /**
     * Show or not cancel message for customer import.
     *
     * @return bool
     */
    public function showCustomerCancel()
    {
        return ($this->helper->getCustomerBulkSynchronizationStatus() == POS_System_Model_Task_Status::STATUS_CANCEL);
    }

    /**
     * get number of imported customers.
     *
     * @return int
     */
    public function getCustomerImported()
    {
        return $this->customerImport->getPosition();
    }

    /**
     * get number of total customers.
     *
     * @return int
     */
    public function getCustomerTotal()
    {
        return $this->customerImport->getTotal();
    }

    public function getCustomerWait()
    {
        return $this->_getHumanTime($this->customerImport->getWait() * 5);
    }

    public function getCustomersImportStatus()
    {
        if ($this->getCustomerImported()) {
            return $this->__(' (Imported: %s, Total: %s)', $this->getCustomerImported(), $this->getCustomerTotal());
        }
    }

    protected function getReindexingStatus()
    {
        foreach (Mage::getResourceModel('index/process_collection')->getItems() as $p) {
            if ($p->getStatus() == Mage_Index_Model_Process::STATUS_RUNNING) {
                return $p->getIndexer()->getName();
            }
        }

        return false;
    }

    public function getProductsImportStatus()
    {
        if ($this->getProductImported()) {
            if ($this->getProductImported() == $this->getProductTotal() && $this->productImport->getStatus() == POS_System_Model_Task_Status::STATUS_INPROGRESS) {
                $reindexStatus = $this->getReindexingStatus();

                return $this->__(' (Reindexing '.($reindexStatus ? $reindexStatus : 'scheduled').')');
            } else {
                return $this->__(' (Imported: %s, Total: %s)', $this->getProductImported(), $this->getProductTotal());
            }
        }
    }
    /**
     * get customers last imported time.
     *
     * @return string - date time
     */
    public function getCustomerLastTime()
    {
        return $this->customerImport->getLastTime();
    }

    /**
     * Get type of import.
     *
     * @return string - Import type
     */
    public function getImportType()
    {
        return $this->__(Mage::getModel('retailexpress/system_method')->getValueById(Mage::getStoreConfig('retailexpress/main/sync_type')));
    }
}
