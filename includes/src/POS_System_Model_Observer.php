<?php

class POS_System_Model_Observer
{

	private static $check_load_product = false;
	
    public function hookIntoCatalogProductNewAction($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //Implement the "catalog_product_new_action" hook
        return $this;    	
    }
    
    public function hookIntoCatalogProductEditAction($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //Implement the "catalog_product_edit_action" hook
        return $this;    	
    }    
    
    public function hookIntoCatalogProductPrepareSave($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $event = $observer->getEvent();
        //Implement the "catalog_product_prepare_save" hook
        return $this;    	
    }

    public function hookIntoSalesOrderItemSaveAfter($observer)
    {
        //$event = $observer->getEvent();
        //Implement the "sales_order_item_save_after" hook
        return $this;    	
    }

    public function hookIntoSalesOrderSaveBefore($observer)
    {
        //$event = $observer->getEvent();
        //Implement the "sales_order_save_before" hook
        return $this;    	
    }     
    
    public function hookIntoSalesOrderSaveAfter($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //Implement the "sales_order_save_after" hook
        return $this;    	
    } 

    public function hookIntoCatalogProductDeleteBefore($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //Implement the "catalog_product_delete_before" hook
        return $this;    	
    }    
    
    public function hookIntoCatalogruleBeforeApply($observer)
    {
        //$event = $observer->getEvent();
        //Implement the "catalogrule_before_apply" hook
        return $this;    	
    }  

    public function hookIntoCatalogruleAfterApply($observer)
    {
        //$event = $observer->getEvent();
        //Implement the "catalogrule_after_apply" hook
        return $this;    	
    }    
    
    public function hookIntoCatalogProductSaveAfter($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $event = $observer->getEvent();
        if (!is_null(Mage::app()->getRequest()->getParam('imprint'))) {
        	$imprint = Mage::helper('adminhtml/js')->decodeGridSerializedInput(Mage::app()->getRequest()->getParam('imprint'));
        	Mage::getModel('brandingironstudios/imprint')->saveProduct($product->getId(), $imprint);
        }

        if (!is_null(Mage::app()->getRequest()->getParam('production'))) {
        	$production = Mage::helper('adminhtml/js')->decodeGridSerializedInput(Mage::app()->getRequest()->getParam('production'));
        	Mage::getModel('brandingironstudios/production')->saveProduct($product->getId(), $production);
        }
        
        Mage::helper('brandingironstudios')->prepareProductIdx($product->getId());
        //Implement the "catalog_product_save_after" hook
        return $this;
    }   
	
    public function hookIntoCatalogProductStatusUpdate($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $event = $observer->getEvent();
        //Implement the "catalog_product_status_update" hook
        return $this;    	
    }

    public function hookIntoCatalogEntityAttributeSaveAfter($observer)
    {
        //$event = $observer->getEvent();
        
        //Implement the "catalog_entity_attribute_save_after" hook
        return $this;    	
    }
    
    public function hookIntoCatalogProductDeleteAfterDone($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $event = $observer->getEvent();
        //Implement the "catalog_product_delete_after_done" hook
        return $this;    	
    }
    
    public function hookCatalogProductView($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $productId = $observer->getControllerAction()->getRequest()->getParam('id');
        if (Mage::helper('retailexpress')->needSyncPrice($productId)) {
        	Mage::helper('retailexpress')->syncProductStockById($productId);
        }
        //Implement the "catalog_product_delete_after_done" hook
    }

    public function hookCustomerLogin()
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}

        foreach (Mage::helper('checkout/cart')->getCart()->getQuoteProductIds() as $productId) {
            if (Mage::helper('retailexpress')->needSyncPrice($productId)) {
                Mage::helper('retailexpress')->syncProductStockById($productId);
            }
        }
    }

    public function hookCheckoutCartCoupon($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $couponCode = (string) $observer->getControllerAction()->getRequest()->getParam('coupon_code');
        if ($observer->getControllerAction()->getRequest()->getParam('remove') != 1) {
            Mage::helper('retailexpress')->checkVoucher($couponCode);
        }
    }

    public  function hookUpdateCustomerInfo($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        Mage::helper('retailexpress')->updateCustomerInfo(Mage::getSingleton('customer/session')->getCustomer()->getId());
    }

    public function hookMultiOrderSave($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $order = $observer->getOrder();
        Mage::helper('retailexpress')->createMultiOrder($order);
    }

    public function hookMultiOrderCancel($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $orders = $observer->getOrders();
        Mage::helper('retailexpress')->cancelMultiOrder($orders);
    }

    public function hookMultiOrderDone($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $orders = $observer->getOrders();
        Mage::helper('retailexpress')->doneMultiOrder($orders);
    }

    public function hookOrderSave($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $order = $observer->getOrder();
        Mage::helper('retailexpress')->createOrder($order);
    }

    public function hookOrderCancel($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $order = $observer->getOrder();
        Mage::helper('retailexpress')->cancelOrder($order);
    }

    public function hookOrderDone($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $order = $observer->getOrder();
        Mage::helper('retailexpress')->doneOrder($order);
    }

    public function hookOrderPay($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        $invoice = $observer->getInvoice();
        Mage::helper('retailexpress')->payOrder($invoice);
    }

    public function hookCustomerSave($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        Mage::helper('retailexpress')->putCustomer($observer->getCustomer());
    }

    public function hookCustomerAddressSave($observer)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}
        if ($observer->getCustomerAddress() && $observer->getCustomerAddress()->getCustomer()) {
            Mage::helper('retailexpress')->putCustomer($observer->getCustomerAddress()->getCustomer());
        }
    }

    public function hookIntoCustomerLogin($observer)
    {
        $event = $observer->getEvent();
        //Implement the "customer_login" hook
        return $this;    	
    }       

    public function hookIntoCustomerLogout($observer)
    {
        $event = $observer->getEvent();
        //Implement the "customer_logout" hook
        return $this;    	
    }

    public function hookIntoSalesQuoteSaveAfter($observer)
    {
        $event = $observer->getEvent();
        //Implement the "sales_quote_save_after" hook
        return $this;    	
    }

    public function hookIntoCatalogProductCollectionLoadAfter($observer)
    {
        $event = $observer->getEvent();
        //Implement the "catalog_product_collection_load_after" hook
        return $this;    	
    }

    public function hookIntoEditForm($observer)
    {
        $form = $observer->getForm();
        if ($posPrice = $form->getElement('pos_prices')) {
            $posPrice->setRenderer(
                $this->getLayout()->createBlock('retailexpress/adminhtml_price')
            );
        }
    }

    public function hookIntoCoreBlockAbstractPrepareLayoutAfter($observer)
    {
        $event = $observer->getEvent();
        $block = $event->getBlock();     

        return $this;    	
    }
    
	/** 
	 * Shortcut to getRequest 
	 */
	protected function _getRequest() 
	{ 
		return Mage::app()->getRequest(); 
	}
    
}