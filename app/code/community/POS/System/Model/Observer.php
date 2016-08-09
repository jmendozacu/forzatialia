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
        // If the plugin is not enabled, or on-demand updates on the product
        // page are disabled, then we'll gracefully back out now.
        if (!$this->isEnabled() || !$this->shouldObserve('catalog_product_view')) {
            return;
        }

        $productId = $observer->getControllerAction()->getRequest()->getParam('id');

        if (Mage::helper('retailexpress')->needSyncPrice($productId)) {
            Mage::helper('retailexpress')->syncProductStockById($productId);
        }
        //Implement the "catalog_product_delete_after_done" hook
    }

    public function hookCartConfigure($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $quoteItemId = $observer->getControllerAction()->getRequest()->getParam('id');

        $productId = Mage::getModel('sales/quote_item')->load($quoteItemId)->getProductId();

        if (Mage::helper('retailexpress')->needSyncPrice($productId)) {
            Mage::helper('retailexpress')->syncProductStockById($productId);
        }
    }

    public function hookCustomerLogin()
    {
        if (!$this->isEnabled()) {
            return;
        }

        foreach (Mage::helper('checkout/cart')->getCart()->getQuoteProductIds() as $productId) {
            if (Mage::helper('retailexpress')->needSyncPrice($productId)) {
                Mage::helper('retailexpress')->syncProductStockById($productId);
            }
        }
    }

    public function hookUpdateCustomerInfo($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        Mage::helper('retailexpress')->updateCustomerInfo(Mage::getSingleton('customer/session')->getCustomer()->getId());
    }

    public function hookMultiOrderSave($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $order = $observer->getOrder();
        Mage::helper('retailexpress')->createMultiOrder($order);
    }

    public function hookMultiOrderCancel($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $orders = $observer->getOrders();
        Mage::helper('retailexpress')->cancelMultiOrder($orders);
    }

    public function hookMultiOrderDone($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $orders = $observer->getOrders();
        Mage::helper('retailexpress')->doneMultiOrder($orders);
    }

    public function hookOrderSave($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $order = $observer->getOrder();
        Mage::helper('retailexpress')->createOrder($order);
    }

    public function hookOrderCancel($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $order = $observer->getOrder();
        Mage::helper('retailexpress')->cancelOrder($order);
    }

    public function hookOrderDone($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $order = $observer->getOrder();
        Mage::helper('retailexpress')->doneOrder($order);
    }

    public function hookOrderPay($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $invoice = $observer->getInvoice();
        Mage::helper('retailexpress')->payOrder($invoice);
    }

    public function hookCustomerSave($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        Mage::helper('retailexpress')->putCustomer($observer->getCustomer());
    }

    public function hookCustomerAddressSave($observer)
    {
        if (!$this->isEnabled()) {
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
     * Shortcut to getRequest.
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Set Order Comment and FulfilmentOutletId.
     */
    public function setCustomerOrderData($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $shippingMethod = $order->getShippingMethod();
        $shippingMethod = explode('_', $shippingMethod);
        if (isset($shippingMethod[0]) && $shippingMethod[0] == 'clickandcollect' && isset($shippingMethod[4])) {
            $fulfilmentouletid = $shippingMethod[4];
        } else {
            $fulfilmentouletid = '';
        }
        $orderComment = $this->_getRequest()->getPost('retailexpress_order_comment');
        $orderComment = trim($orderComment);
        $order->setClickandcollectOrderFulfilmentouletid($fulfilmentouletid);
        $order->setClickandcollectOrderComment($orderComment);

        $orderComment = nl2br($orderComment);
        $order->addStatusHistoryComment($orderComment);
    }

    /**
     * Skipping all the rewrites of adminhtml_catalog_product_grid declared in other POS extensions.
     */
    public function pageLoadBefore($observer)
    {
        Mage::getConfig()->setNode('global/blocks/adminhtml/rewrite/catalog_product_grid', 'POS_System_Adminhtml_Block_Catalog_Product_Grid');
    }

    /**
     * update stock status of quote products before cart load.
     */
    public function hookCheckoutCartIndex($observer)
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (!$productIds = $this->_getQuoteProducts()) {
            return;
        }
        foreach ($productIds as $productId) {
            if (Mage::helper('retailexpress')->needSyncPrice($productId)) {
                Mage::helper('retailexpress')->syncProductStockById($productId);
            }
        }
    }

    /**
     * looks like strange way to get quote items, right?
     * doing this because $quote->getVisibleItems() loads product stock items and cache data
     * we not able to change stock of products after getVisibleItems is called
     * returns false or products array.
     *
     * @return mixed
     */
    protected function _getQuoteProducts()
    {
        // get session
        $session = Mage::getSingleton('checkout/session');
        // get quote id
        $quoteId = $session->getQuoteId();
        if (!$quoteId) {
            // no quote
            return false;
        }
        if ($quoteId) {
            $resource = Mage::getSingleton('core/resource');
            $read = $resource->getConnection('read');
            $tPrefix = (string) Mage::getConfig()->getTablePrefix();
            $quoteTable = $tPrefix.'sales_flat_quote_item';
            $query = "SELECT `product_id` FROM {$quoteTable} WHERE `product_type` = 'simple' AND `quote_id` = {$quoteId}";
            //fetch products
            $products = $read->fetchAll($query);
            if (!count($products)) {
                //no products
                return false;
            }
            $productIds = array();
            foreach ($products as $product) {
                $productIds[] = $product['product_id'];
            }
        }

        return $productIds;
    }

    /**
     * update last_date column of sync_product table.
     *
     * @params Mage_ImportExport_Model_Import_Entity_Product $observer
     */
    public function afterProductImport($observer)
    {
        if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
            return;
        }
        $adapter = $observer->getAdapter();
        if ('POS_System_Model_Import_Product_Adapter' != get_class($adapter)) {
            return;
        }
        $productIds = $adapter->getAffectedEntityIds();
        Mage::helper('retailexpress')->updateProductSynchronizationTime($productIds);
    }

    protected function isEnabled()
    {
        return (bool) Mage::getStoreConfig('retailexpress/main/enabled');
    }

    protected function shouldObserve($flag)
    {
        return (bool) Mage::getStoreConfig("retailexpress/observers/{$flag}");
    }
}
