<?php

class POS_System_Adminhtml_RetailexpressController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/history')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('POS system Synchronisation History'), Mage::helper('adminhtml')->__('POS system Synchronisation History'));
        return $this;
    }  
  
    public function indexAction()
    {
        $this->_initAction()
        	->renderLayout();
    }


	public function syncproductAction()
	{
		$productIds = $this->getRequest()->getParam('product');
		try {
			if (!is_array($productIds)) {
	            throw new Exception($this->__('Please select product(s).'));
	        }

			foreach ($productIds as $productId) {
				Mage::helper('retailexpress')->syncProductStockById($productId);
			}

			$this->_getSession()->addSuccess(
            	$this->__('Total of %d record(s) have been synchronised.', count($productIds))
            );
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_redirect('adminhtml/catalog_product/index');
	}


    /**
     * Scheduling product bulk synchronization
     *
     * @return void
     */
	public function syncProductBulkAction()
	{
		try {
            if (in_array(Mage::helper('retailexpress')->getProductBulkSynchronizationStatus(), array(POS_System_Model_Task_Status::STATUS_SCHEDULE, POS_System_Model_Task_Status::STATUS_INPROGRESS, POS_System_Model_Task_Status::STATUS_INPROSHE))) {
                throw new Exception($this->__('Product Synchronisation has already been scheduled. Please wait until the previous product synchronisation has completed before trying again.'));
            }

            Mage::getSingleton('retailexpress/import_product')->schedule();
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_redirect('*/*/');
	}


    /**
     * cancel bulk synchronization
     *
     * @return void
     */
	public function cancelAction()
	{
		try {
            if (Mage::getSingleton('retailexpress/import_product')->cancel()) {
                $this->_getSession()->addSuccess($this->__('Product synchronisation canceled'));
            } else {
                $this->_getSession()->addNotice($this->__('Product synchronisation already completed or canceled'));
            }

            if (Mage::getSingleton('retailexpress/import_customer')->cancel()) {
                $this->_getSession()->addSuccess($this->__('Customer synchronisation canceled'));
            } else {
                $this->_getSession()->addNotice($this->__('Customer synchronisation already completed or canceled'));
            }
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_redirect('*/*/');
	}


	public function syncOrdersAction()
	{
		try {
            $result = Mage::helper('retailexpress')->synhOrdersBulk();
            $comment = '';
            foreach ($result as $k => $v) {
                $comment .= $k . ":\n" . $v . "\n\n";
            }

    		$history = Mage::getModel('retailexpress/history');
    		$history->setData(
                array(
                      'type' => 'Manual'
                    , 'comment' => $comment
    		    )
            );
            $history->save();
			$this->_getSession()->addSuccess(
            	$this->__('Synchronised.')
            );
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_redirect('*/*/');
	}


	public function syncCustomersAction()
	{
		try {
            if (in_array(Mage::helper('retailexpress')->getCustomerBulkSynchronizationStatus(), array(POS_System_Model_Task_Status::STATUS_SCHEDULE, POS_System_Model_Task_Status::STATUS_INPROGRESS, POS_System_Model_Task_Status::STATUS_INPROSHE))) {
                throw new Exception($this->__('Customer Synchronisation has already been scheduled. Please wait until the previous customer synchronisation has completed before trying again.'));
            }

            Mage::getSingleton('retailexpress/import_customer')->schedule();
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		$this->_redirect('*/*/');
	}

    public function customerordersAction()
    {
        Mage::register('current_customer', Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id')));
        $this->getResponse()->setBody($this->getLayout()->createBlock('retailexpress/adminhtml_customer_orders_grid')->toHtml());
    }


}