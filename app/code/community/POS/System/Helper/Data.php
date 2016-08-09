<?php

class POS_System_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_RETAILEXPRESS_ENABLED = 'retailexpress/main/enabled';
    const XML_PATH_CHANNEL_ID = 'retailexpress/main/sales_channel_id';
    const XML_PATH_ETA_ENABLED = 'retailexpress/eta/enabled';

    // change this to false to switch to non-challels version
    const CHANNELS_VERSION = true;

    /**
     * Constants to be used for DB.
     */
    const DB_MAX_PACKET_SIZE = 1048576; // Maximal packet length by default in MySQL
    const DB_MAX_PACKET_COEFFICIENT = 0.85; // The coefficient of useful data from maximum packet length

    protected static $_address_sync = false;
    protected static $_order_sync = false;

    protected static $_created_orders = array();

    protected $_is_pcntl = false;

    protected $_groupCache = array();

    public function __construct()
    {
        $this->_is_pcntl = function_exists('pcntl_signal');
    }

    public function getIsEtaEnabled()
    {
        return self::CHANNELS_VERSION || Mage::getStoreConfig(self::XML_PATH_ETA_ENABLED);
    }

    public function synhOrdersBulk()
    {
        $model = Mage::getModel('retailexpress/retail');
        if ($model->getError()) {
            throw new Exception($model->getError());
        }

        self::$_order_sync = true;
        $last_date = $this->getBulkLastTime('order');
        $current_time = time();
        $data = $model->getOrdersBulkDetail($last_date);
        $report = array('Orders' => '');
        $created = 0;
        $updated = 0;
        $errored = 0;
        $error_mes = '';

        foreach ($data as $id => $items) {
            try {
                $order = Mage::getModel('sales/order')->loadByIncrementId($id);
                $rex_id = Mage::getModel('retailexpress/conf')->load('order_'.$order->getId())->getValue();
                if (!$order->getId() || !$rex_id) {
                    $report['Orders'] .= 'POS Order '.$id." do not create in magento\n";
                    continue;
                }
                if ($items['status'] == 'cancelled' || $items['status'] == 'cancel') {
                    $order->cancel();
                    $order->save();
                    continue;
                }

                //check if orders from rex and magento doesnt match

                $savedQtys = array();

                foreach ($order->getAllItems() as $item) {
                    if ($item->getIsVirtual() || $item->getParentItem()) {
                        continue;
                    }

                    if (count($item->getChildrenItems())) {
                        foreach ($item->getChildrenItems() as $_i) {
                            $product = Mage::getSingleton('catalog/product')->load($_i->getProductId());
                            break;
                        }
                    } else {
                        $product = Mage::getSingleton('catalog/product')->load($item->getProductId());
                    }

                    $rex_id = $this->getRetailProductId($product);

                    //register rex ids into an array for product checking between rex and magento for orders
                    $productArray[$id][] = $rex_id;

                    if (!isset($items[$rex_id])) {
                        continue;
                    }

                    if ($item->getQtyToShip() < $items[$rex_id]['qty']) {
                        continue;
                    }

                    $savedQtys[$item->getId()] = 0;
                    foreach ($items[$rex_id] as $fulfillment) {
                        $savedQtys[$item->getId()] += $fulfillment['qty'];
                    }
                }

                $rexOrderProductArray = $items['products'];

                $mageOrderProductArray = $productArray[$id];

                //check count of product values
                $orderNotMatch = false;
                if (count($rexOrderProductArray) != count($mageOrderProductArray)) {
                    $orderNotMatch = true;
                } else {
                    for ($i = 0; $i < count($rexOrderProductArray); ++$i) {
                        if ($rexOrderProductArray[$i] != $mageOrderProductArray[$i]) {
                            $orderNotMatch = true;
                            //break; //exit on the loop if there is discrep
                        }
                    }
                }

                //compare prices
                if ($order->getSubtotalInclTax() != $items['order_total']) {
                    $orderNotMatch = true;
                }

                //this is to temporarily disable order sync on creating new orders when there is
                //any discrepancy between order on magento and retailexpress POS
                $orderNotMatch = false;

                if ($orderNotMatch) {
                    self::$_order_sync = false;

                    if ($model->createOrderFromRex($id, $items)) {
                        ++$created;
                        $model->orderCancel($id);
                        $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
                        ++$updated;
                    }

                    self::$_order_sync = true;
                } else {
                    $transaction = false;
                    $is_ship = false;
                    $is_invoice = false;
                    if (count($savedQtys) && $order->canShip()) {
                        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                        $shipment->register();
                        $shipment->setEmailSent(true);
                        $shipment->getOrder()->setIsInProcess(true);
                        $is_ship = true;
                        $transaction = Mage::getModel('core/resource_transaction')
                            ->addObject($shipment)
                            ->addObject($shipment->getOrder());
                    } else {
                        $report['Orders'] .= 'POS Order '.$id." shipments already made\n";
                    }
                    if (isset($items['payment']) || (float) $items['order_total'] == 0) {
                        if ($order->canInvoice()) {
                            $invoice = $order->prepareInvoice();
                            $invoice->register();
                            $invoice->setEmailSent(true);
                            $invoice->getOrder()->setCustomerNoteNotify(true);
                            $invoice->getOrder()->setIsInProcess(true);
                            $is_invoice = true;
                            if ($transaction) {
                                $transaction->addObject($invoice);
                            } else {
                                $transaction = Mage::getModel('core/resource_transaction')
                                    ->addObject($invoice)
                                    ->addObject($invoice->getOrder());
                            }
                        } else {
                            $report['Orders'] .= 'POS Order '.$id." payment already made\n";
                        }
                    }
                    if ($transaction) {
                        $transaction->save();
                        try {
                            if ($is_invoice) {
                                $invoice->sendEmail(true, '');
                            } else {
                                $shipment->sendEmail(true, '');
                            }
                        } catch (Exception $e) {
                            Mage::log('Exception: POS_System_Helper_Data:: Order bulk sync error: '.$e->getMessage(), null, 'possystem.log');
                        }
                        ++$updated;
                        if ($is_invoice) {
                            Mage::log('+POS Order payment made');
                            $report['Orders'] .= 'POS Order '.$id." payment made\n";
                        }
                        if ($is_ship) {
                            Mage::log('+POS Order adding shipment ('.$shipment->getId().')');
                            $report['Orders'] .= 'POS Order '.$id.' adding shipment ('.$shipment->getId().")\n";
                        }
                    }
                }
            } catch (Exception $e) {
                ++$errored;
                $msg = 'POS Order '.$id.' adding shipment error: '.$e->getMessage()."\n";
                $error_mes .= $msg;
                $report['Orders'] .= $msg;
            }
        }

        self::$_order_sync = false;
        $this->setBulkLastTime('order', $current_time);
        if (!Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            return array('Bulk Synchronisation of Orders Completed Successfully' => $updated.' Updated, '.$errored.' Errors.'."\n".$error_mes);
        }

        return $report;
    }

    public function syncProductStockById($productId)
    {
        $pricegroup_id = 0;
        $mag_id = 0;

        $_globalAllowCheckAvailabilityStatus = Mage::helper('eta')->getGlobalAllowCheckAvailabilityStatus();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $mag_id = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            $pricegroup_id = (int) Mage::getModel('retailexpress/conf')->load('group_'.$mag_id)->getValue();
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if (is_callable(array($product, 'getTypeInstance')) && is_callable(array($product->getTypeInstance(), 'getUsedProducts'))) {
            $configurable_products = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
            $products = $configurable_products->getUsedProductCollection()->addAttributeToSelect('*');

            $t = Mage::getModel('retailexpress/product')
                ->getCollection()
                ->addFieldToFilter('product_id', $product->getId())
                ->addFieldToFilter('customer_id', (int) (($mag_id && $pricegroup_id) ? $mag_id : 0))
                ->getFirstItem()
                ->setProductId($product->getId())
                ->setCustomerId((int) (($mag_id && $pricegroup_id) ? $mag_id : 0))
                ->setLastDate($this->_time())
                ->save();
        } else {
            $products = array($product);
        }
		$m2eModel = Mage::getModel('M2ePro/PublicServices_Product_SqlChange');
        foreach ($products as $p) {
            $product = Mage::getModel('catalog/product')->load($p->getId());
            $stockItemModel = Mage::getModel('cataloginventory/stock_item');
            $stockItem = $stockItemModel->loadByProduct($product);
            $retail_product_id = $this->getRetailProductId($product);
            if (!$retail_product_id) {
                continue;
            }

            $history = Mage::getModel('retailexpress/history');
            $history->setData(
                array(
                      'type' => 'Online', 'comment' => '',
                )
            );
            Mage::register('retail_history_id', $history->getId());
			


            try {
                $retail_product_data = Mage::getSingleton('retailexpress/retail')->getProductStockPriceById($retail_product_id, $pricegroup_id);
                $need_save = false;
                if (!$mag_id || !$pricegroup_id) {
                    if (is_array($retail_product_data)) {
                        $updateData = $retail_product_data;
                        $productStatus = 1;
                    } else {
                        $productStatus = 2;
                        $updateData = array();
                    }

                    $need_save = true;
                } else {
                    if (is_array($retail_product_data)) {
                        $__t = $retail_product_data;
                        if (isset($__t['price'])) {
                            unset($__t['price']);
                        }

                        if (isset($__t['special_price'])) {
                            unset($__t['special_price']);
                        }

                        $productStatus = 1;
                        $updateData = $__t;
                    } else {
                        $productStatus = 2;
                        $updateData = array();
                    }

                    $need_save = true;
                }

                if ($need_save) {
                    $storeId = Mage::app()->getStore()->getId();
                    if (!isset($priceAttributeId)) {
                        $priceAttributeId = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->setCodeFilter('price')
                            ->getFirstItem()->getAttributeId();
                        $specialPriceAttributeId = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->setCodeFilter('special_price')
                            ->getFirstItem()->getAttributeId();
                        $statusAttributeId = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->setCodeFilter('status')
                            ->getFirstItem()->getAttributeId();
                    }

                    $resource = Mage::getSingleton('core/resource');
                    $read = $resource->getConnection('read');
                    $write = $resource->getConnection('write');
                    $tPrefix = (string) Mage::getConfig()->getTablePrefix();
                    $stockMainTable = $tPrefix.'cataloginventory_stock_item';
                    $stockStatusTable = $tPrefix.'cataloginventory_stock_status';
                    $stockStatusIndexTable = $tPrefix.'cataloginventory_stock_status_idx';
                    $catalogProductDecimalTable = $tPrefix.'catalog_product_entity_decimal';
                    $productStatusTable = $tPrefix.'catalog_product_entity_int';

                    $productId = $product->getId();
                    $updateStockData = false;

                    if (!empty($updateData['stock_data'])) {
                        $qty = $retail_product_data['stock_data']['qty'];
                        $isInStock = $retail_product_data['stock_data']['is_in_stock'] ? $retail_product_data['stock_data']['is_in_stock'] : 0;
                        $manageStock = $retail_product_data['stock_data']['manage_stock'] != '' ? $retail_product_data['stock_data']['manage_stock'] : 0;
                        $useConfigManageStock = $retail_product_data['stock_data']['use_config_manage_stock'];
                        $qtyOnOrder = $retail_product_data['stock_data']['qty_on_order'];

                        $allowCheckAvailability =
                                                    (
                                                            $_globalAllowCheckAvailabilityStatus && $stockItem->getUseConfigAllowCheckAvailabilityStatus()
                                                            || !$stockItem->getUseConfigAllowCheckAvailabilityStatus() && $stockItem->getAllowCheckAvailabilityStatus()

                                                    ) ? 1 : 0;

                        if (!$manageStock) {
                            $isInStock = 1;
                        } elseif ($allowCheckAvailability) {
                            if ($qty > $stockItem->getMinQty()) {
                                $isInStock = 1;
                            } elseif (($qtyOnOrder + $qty) > $stockItem->getMinQty()) {
                                $isInStock = 2;
                            } else {
                                $isInStock = 0;
                            }
                        } else {
                            if ($qty > $stockItem->getMinQty()) {
                                $isInStock = 1;
                            } else {
                                $isInStock = 0;
                            }
                        }

                        $updateStockData = true;
                    }

                    if (isset($updateData['price']) and $updateData['price'] != '') {
                        $price = $retail_product_data['price'];
                        $updatePrice = true;
                    } else {
                        $updatePrice = false;
                    }

                    if (isset($updateData['special_price']) and $updateData['special_price'] != '') {
                        $specialPrice = $retail_product_data['special_price'];
                        $updateSpecialPrice = true;
                    } else {
                        $updateSpecialPrice = false;
                    }

                    if ($updateData['weight'] !== false) {
                        $weight = $retail_product_data['weight'];
                        $updateWeight = true;
                    } else {
                        $updateWeight = false;
                    }

                    if ($updateData['tax_class_id'] !== false) {
                        $tasClass = $retail_product_data['tax_class_id'];
                        $updateTaxClass = true;
                    } else {
                        $updateTaxClass = false;
                    }

                    if ($updateWeight) {
                        try {
                            $attributeId = $this->_getAttibuteIdByCode('weight');
                            // attribute found, update value
                            $tableName = $tPrefix.'catalog_product_entity_decimal';
                            $sql = "UPDATE {$tableName} SET value={$weight} WHERE entity_id='{$productId}' AND attribute_id='$attributeId'";
                            $write->query($sql);
                        } catch (exception $e) {
                            Mage::log('Exception: POS_System_Helper_Data:: Product Stock Status Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
                        }
                    }

                    if ($updateTaxClass) {
                        try {
                            $attributeId = $this->_getAttibuteIdByCode('tax_class_id');
                            // attribute found, update value
                            $tableName = $tPrefix.'catalog_product_entity_int';
                            $sql = "UPDATE {$tableName} SET value={$tasClass} WHERE entity_id='{$productId}' AND attribute_id='$attributeId'";
                            $write->query($sql);
                        } catch (exception $e) {
                            Mage::log('Exception: POS_System_Helper_Data:: Product Stock Status Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
                        }
                    }

                    if ($updateStockData) {
                        $sql = "UPDATE {$stockMainTable} SET qty={$qty}, is_in_stock={$isInStock}, manage_stock={$manageStock}, use_config_manage_stock={$useConfigManageStock}, qty_on_order={$qtyOnOrder}, allow_check_availability={$allowCheckAvailability} WHERE product_id='{$productId}'";
                        try {
                            $write->query($sql);
                        } catch (Exception $e) {
                            Mage::log('Exception: POS_System_Helper_Data:: Product Stock Status Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
                        }

                        $sql = "UPDATE {$stockStatusTable} SET qty={$qty}, stock_status={$isInStock} WHERE product_id='{$productId}'";
                        try {
                            $write->query($sql);
                        } catch (Exception $e) {
                            Mage::log('Exception: POS_System_Helper_Data:: Product Stock Status Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
                        }

                        $sql = "UPDATE {$stockStatusIndexTable} SET qty={$qty}, stock_status={$isInStock} WHERE product_id='{$productId}'";
                        try {
                            $write->query($sql);
                        } catch (Exception $e) {
                            Mage::log('Exception: POS_System_Helper_Data:: Product Stock Status Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
                        }
                    }

                    if ($updatePrice) {
                        $sql = "UPDATE {$catalogProductDecimalTable} SET value={$price} WHERE attribute_id='{$priceAttributeId}' AND entity_id='{$productId}'";
                        try {
                            $write->query($sql);
                        } catch (Exception $e) {
                            Mage::log('Exception: POS_System_Helper_Data:: Product Stock Status Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
                        }
                    }

                    if ($updateSpecialPrice) {
                        $sql = "UPDATE {$catalogProductDecimalTable} SET value={$specialPrice} WHERE attribute_id='{$specialPriceAttributeId}' AND entity_id='{$productId}'";
                        try {
                            $write->query($sql);
                        } catch (Exception $e) {
                            Mage::log('Exception: POS_System_Helper_Data:: Product Stock Status Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
                        }
                    }

                    if ($updatePrice || $updateSpecialPrice) {
                        if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
                            // 1.6.0.0 or greater
                               Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($product);
                        } else {
                            $this->_applyAllRulesToProduct($product);
                        }
                    }

                    $sql = "UPDATE {$productStatusTable} SET value={$productStatus} WHERE attribute_id='{$statusAttributeId}' AND entity_id='{$productId}'";
                    try {
                        $write->query($sql);
                    } catch (Exception $e) {
                        Mage::log('Exception: POS_System_Helper_Data:: Product Stock Status Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
                    }
                }

                $t = Mage::getModel('retailexpress/product')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $product->getId())
                    ->addFieldToFilter('customer_id', $pricegroup_id ? $mag_id : 0)
                    ->getFirstItem()
                    ->setProductId($product->getId())
                    ->setCustomerId($pricegroup_id ? $mag_id : 0)
                    ->setPrice(isset($retail_product_data['price']) ? $retail_product_data['price'] : $product->getPrice())
                    ->setData('special_price', isset($retail_product_data['special_price']) ? $retail_product_data['special_price'] : $product->getSpecialPrice())
                    ->setLastDate($this->_time())
                    ->save();

                $history->setComment('Synchronise Product Stock for POS Product ID '.$retail_product_id.' synchronised (#'.$product->getId().')');
            } catch (Exception $e) {
                Mage::log('Exception: Synchronise Product Stock for POS Product ID '.$retail_product_id.' error: '.$e->getMessage().')', null, 'possystem.log');
                $history->setComment('Synchronise Product Stock for POS Product ID '.$retail_product_id.' error: '.$e->getMessage().')');
            }
		
		// m2e change//
			if($updateStockData) { $m2eModel->markProductChanged($product->getId()); $m2eModel->markQtyWasChanged($product->getId());  }
			if($updatePrice) { $m2eModel->markProductChanged($product->getId()); $m2eModel->markPriceWasChanged($product->getId()); }
			if($updateWeight) {  $m2eModel->markProductChanged($product->getId()); }
			if($updateSpecialPrice) { $m2eModel->markProductChanged($product->getId()); }
			
		
            if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
                $history->save();
            }
        }
		
		$m2eModel->applyChanges();
    }

    public function getRetailProductId($product)
    {
        return $product->getRexProductId();
    }

    protected function _needSyncProductByCustomer($productId, $pricegroup_id)
    {
        $_t = Mage::getSingleton('retailexpress/config')->getProductTrustTime();
        if ('' === $_t) {
            return false;
        }

        if (!$productId) {
            return false;
        }

        $_t = (int) $_t;
        $last = Mage::getModel('retailexpress/product')
            ->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('customer_id', $pricegroup_id)
            ->getFirstItem()
            ->getLastDate();
        $last = strtotime($last);
        if (($last + $_t) < $this->_time()) {
            return true;
        }

        return false;
    }

    public function needSyncPrice($productId)
    {
        $pricegroup_id = 0;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $pricegroup_id = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            if (!(int) Mage::getModel('retailexpress/conf')->load('group_'.$pricegroup_id)->getValue()) {
                $pricegroup_id = 0;
            }
        }

        return $this->_needSyncProductByCustomer($productId, $pricegroup_id);
    }

    public function unzip($str, $id)
    {
        $dir = Mage::getBaseDir('var').DS.'retail'.DS.'zip'.DS;
        $file_in = $dir.$id.'.in';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!is_dir_writeable($dir)) {
            chmod($dir, 0777);
        }

        $fd = fopen($file_in, 'wb+');
        if (!$fd) {
            throw new Exception('Cannot open zip file '.$file_in);
        }

        chmod($file_in, 0666);
        if (!fwrite($fd, $str)) {
            fclose($fd);
            throw new Exception('Cannot write to zip file '.$file_in);
        }

        fclose($fd);
        clearstatcache();
        $res = gzopen($file_in, 'rb');
        if ($res === false) {
            throw new Exception('Failed open gzip archive '.$file_in);
        }

        $return = '';
        while ($content = gzread($res, 1000)) {
            $return .= $content;
        }

        gzclose($res);

        return trim($return);
    }

    public function getWebsiteId()
    {
        return Mage::getModel('core/website')->getCollection()->getFirstItem()->getId();
    }

    public function getBulkLastTime($type)
    {
        if (Mage::getStoreConfig('retailexpress/main/sync_new')) {
            $v = Mage::getModel('retailexpress/conf')->load('bulklt_'.$type)->getValue();
            if ($v) {
                return date('c', $v);
            }
        }

        return '1900-01-01T00:00:00';
    }

    public function setBulkLastTime($type, $v)
    {
        Mage::getModel('retailexpress/conf')->load('bulklt_'.$type)
            ->setConfId('bulklt_'.$type)
            ->setValue($v)
            ->save();
    }

    public function createMultiOrder($order)
    {
        $this->createOrder($order);
        self::$_created_orders[Mage::registry('current_order_rexid')] = true;
    }

    public function cancelMultiOrder($orders)
    {
        foreach ($orders as $o) {
            Mage::unregister('current_order_rexid');
            if (isset(self::$_created_orders[$o->getIncrementId()])) {
                Mage::register('current_order_rexid', $o->getIncrementId());
            }

            $this->cancelOrder($o);
        }
    }

    public function doneMultiOrder($orders)
    {
        foreach ($orders as $o) {
            Mage::unregister('current_order_rexid');
            if (isset(self::$_created_orders[$o->getIncrementId()])) {
                Mage::register('current_order_rexid', $o->getIncrementId());
            }

            $this->doneOrder($o);
        }
    }

    public function createOrder($order)
    {
        Mage::unregister('current_order_rexid');
        Mage::unregister('current_customer_rexid');
        $rex_id = false;
        $rex_data = array();

        //get delivery method, explode the string - sample value = flatrate_flatrate
        $shippingMethod = explode('_', $order->getShippingMethod());

        $shippingMethod = $shippingMethod[0];

        $deliveryMethod = ($dmethod = Mage::getStoreConfig('carriers/'.$shippingMethod.'/delivery_mode')) ? $dmethod : 'home';

        $rex_data['DeliveryMethod'] = $deliveryMethod;

        $rex_data['FulfilmentOutletID'] = $order->getClickandcollectOrderFulfilmentouletid();
        $rex_data['PublicComments'] = $order->getClickandcollectOrderComment();

        $rex_data['Password'] = '';
        if ($order->getCustomer() && $order->getCustomer()->getId()) {
            $rex_data['ExternalCustomerId'] = $order->getCustomer()->getId();
            $rex_id = Mage::getModel('retailexpress/conf')->load('customer_'.$order->getCustomer()->getId())->getValue();
            if ($order->getCustomer()->getPassword()) {
                $rex_data['Password'] = $order->getCustomer()->getPassword();
            }
        } else {
            $rex_data['ExternalCustomerId'] = '';
        }

        $rex_data['BillEmail'] = $order->getData('customer_email');
        $rex_data['BillFirstName'] = $order->getData('customer_firstname');
        $rex_data['BillLastName'] = $order->getData('customer_lastname');

        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($order->getCustomer()->getEmail());
        $rex_data['ReceivesNews'] = (int) ($subscriber->getId() && $subscriber->getSubscriberStatus() == 1 ? 1 : 0);

        if ($order->getData('tax_percent')) {
            $rex_data['TaxRateApplied'] = $order->getData('tax_percent') / 100;
        } else {
            $rex_data['TaxRateApplied'] = '0';
        }

        if ($order->getBillingAddress()) {
            $streets = $order->getBillingAddress()->getStreet();
            $rex_data['BillAddress'] = isset($streets[0]) ? $streets[0] : '';
            $rex_data['BillAddress2'] = isset($streets[1]) ? $streets[1] : '';
//            $rex_data['BillAddress'] = join(' ', $order->getBillingAddress()->getStreet());
            $rex_data['BillCompany'] = $order->getBillingAddress()->getCompany();
            $rex_data['BillPhone'] = $order->getBillingAddress()->getData('telephone');
            $rex_data['BillPostCode'] = $order->getBillingAddress()->getData('postcode');
            $rex_data['BillSuburb'] = $order->getBillingAddress()->getData('city');
            $rex_data['BillState'] = $order->getBillingAddress()->getData('region');
            $rex_data['BillCountry'] = Mage::getModel('directory/country')->loadByCode($order->getBillingAddress()->getData('country_id'))->getName();
            if (!$rex_data['BillFirstName']) {
                $rex_data['BillFirstName'] = $order->getBillingAddress()->getData('firstname');
            }

            if (!$rex_data['BillLastName']) {
                $rex_data['BillLastName'] = $order->getBillingAddress()->getData('lastname');
            }
        }

        $order_info = "Customer\nEmail: ".$rex_data['BillEmail']."\n Billing Name: ".$rex_data['BillFirstName'].' '.$rex_data['BillLastName']."\n";
        $order_info .= 'Billing Country: '.$rex_data['BillCountry']."\nBilling State: ".$rex_data['BillState']."\nBilling Postcode: ".$rex_data['BillPostCode']."\nBilling Address: ".$rex_data['BillAddress']."\n";
        $order_info .= 'Billing Phone: '.$rex_data['BillPhone']."\n";
        if ($order->getShippingAddress()) {
            $streets = $order->getShippingAddress()->getStreet();
            $rex_data['DelAddress'] = isset($streets[0]) ? $streets[0] : '';
            $rex_data['DelAddress2'] = isset($streets[1]) ? $streets[1] : '';
            $rex_data['DelName'] = $order->getShippingAddress()->getData('firstname').' '.$order->getShippingAddress()->getData('lastname');
//            $rex_data['DelAddress'] = join(' ', $order->getShippingAddress()->getStreet());
            $rex_data['DelCompany'] = $order->getShippingAddress()->getCompany();
            $rex_data['DelPhone'] = $order->getShippingAddress()->getData('telephone');
            $rex_data['DelPostCode'] = $order->getShippingAddress()->getData('postcode');
            $rex_data['DelSuburb'] = $order->getShippingAddress()->getData('city');
            $rex_data['DelState'] = $order->getShippingAddress()->getData('region');
            $rex_data['DelCountry'] = Mage::getModel('directory/country')->loadByCode($order->getShippingAddress()->getData('country_id'))->getName();
        } elseif ($order->getBillingAddress()) {
            $streets = $order->getBillingAddress()->getStreet();
            $rex_data['DelAddress'] = isset($streets[0]) ? $streets[0] : '';
            $rex_data['DelAddress2'] = isset($streets[1]) ? $streets[1] : '';
            $rex_data['DelCompany'] = $order->getBillingAddress()->getCompany();
            $rex_data['DelPhone'] = $order->getBillingAddress()->getData('telephone');
            $rex_data['DelPostCode'] = $order->getBillingAddress()->getData('postcode');
            $rex_data['DelSuburb'] = $order->getBillingAddress()->getData('city');
            $rex_data['DelState'] = $order->getBillingAddress()->getData('region');
            $rex_data['DelCountry'] = Mage::getModel('directory/country')->loadByCode($order->getBillingAddress()->getData('country_id'))->getName();
            if (!$rex_data['BillFirstName']) {
                $rex_data['DelName'] = $order->getBillingAddress()->getData('firstname');
            } else {
                $rex_data['DelName'] = $rex_data['BillFirstName'];
            }

            if (!$rex_data['BillLastName']) {
                $rex_data['DelName'] .= ' '.$order->getBillingAddress()->getData('lastname');
            } else {
                $rex_data['DelName'] .= ' '.$rex_data['BillLastName'];
            }
        }

        $order_info .= 'Delivery Name: '.$rex_data['DelName']."\n";
        $order_info .= 'Delivery Country: '.$rex_data['DelCountry']."\nDelivery State: ".$rex_data['DelState']."\nDelivery Postcode: ".$rex_data['DelPostCode']."\nDelivery Address: ".$rex_data['DelAddress']."\n";
        $order_info .= 'Delivery Phone: '.$rex_data['DelPhone']."\n\n";
        if ($rex_id) {
            $rex_data['CustomerId'] = $rex_id;
        } else {
            $rex_data['CustomerId'] = '';
        }

        $rex_data['OrderStatus'] = 'Quote';
        $rex_data['OrderTotal'] = ($order->getData('grand_total') * 1 >= 0.01) ? $order->getData('grand_total') : 0;
        $rex_data['FreightTotal'] = $order->getData('shipping_incl_tax');
        $rex_data['PrivateComments'] = 'Delivery by '.$order->getShippingDescription()."\n";
        if ($order->getGiftMessageId()) {
            $gift = Mage::helper('giftmessage/message')->getGiftMessage($order->getGiftMessageId());
            $rex_data['PrivateComments'] .= 'Order giftmessage from: '.$gift->getSender()."\n";
            $rex_data['PrivateComments'] .= 'Order giftmessage to: '.$gift->getRecipient()."\n";
            $rex_data['PrivateComments'] .= 'Order giftmessage message: '.$gift->getMessage()."\n";
        }

        $rex_data['OrderItems'] = array('OrderItem' => array());
        $rex_data['OrderPayments'] = array(
            'OrderPayment' => array(),
        );
        $rex_payment_id = '';
        if ($order->getPayment()) {
            $__t = Mage::getStoreConfig('retailexpress/payments/'.$order->getPayment()->getMethod());
            $rex_payment_id = Mage::getModel('retailexpress/payment')->load($__t)->getRexId();
        }
        if ($rex_payment_id && $order->getTotalPaid()) {
            $rex_data['OrderPayments']['OrderPayment'][] = array(
                'MethodId' => $rex_payment_id,
                'Amount' => (float) $order->getTotalPaid(),
                'DateCreated' => date('c'),

            );
        }

        if (!count($rex_data['OrderPayments']['OrderPayment'])) {
            unset($rex_data['OrderPayments']['OrderPayment']);
        }

        $order_info .= "Ordered Items\n";
        foreach ($order->getAllVisibleItems() as $i) {
            if ($i->getParentItem()) {
                continue;
            }

            $product = Mage::getModel('catalog/product')->load($i->getProductId());
            $order_info .= $product->getName().' (#'.$product->getId().', SKU: '.$product->getSku().'), qty: '.$i->getData('qty_ordered')."\n";
            if (!$product->getRexProductId()) {
                continue;
            }

            $tax = '0';
            if ($i->getData('tax_percent')) {
                $tax = $i->getData('tax_percent') / 100;
            }

            //get delivery driver
            //for pickup, string needs to extract and get the title only for pickup method
            $deliveryDriverArray = explode(' - ', $order->getShippingDescription());
            $deliveryDriver = $deliveryDriverArray[0];

            if (count($i->getChildrenItems())) {
                foreach ($i->getChildrenItems() as $_i) {
                    $product = Mage::getModel('catalog/product')->load($_i->getProductId());
                    if ($i->getGiftMessageId()) {
                        $gift = Mage::helper('giftmessage/message')->getGiftMessage($i->getGiftMessageId());
                        $rex_data['PrivateComments'] .= "Item '".$product->getRexProductId()."' giftmessage from: ".$gift->getSender()."\n";
                        $rex_data['PrivateComments'] .= "Item '".$product->getRexProductId()."' giftmessage to: ".$gift->getRecipient()."\n";
                        $rex_data['PrivateComments'] .= "Item '".$product->getRexProductId()."' giftmessage message: ".$gift->getMessage()."\n";
                    }

                    $unitPrice = $i->getPriceInclTax() - $i->getDiscountAmount() / (int) $i->getData('qty_ordered');
                    $unitPrice = round($unitPrice, 2);
                    $lineTotalCalculated = $unitPrice * (int) $i->getData('qty_ordered');
                    $lineTotal = $i->getPriceInclTax() * (int) $i->getData('qty_ordered') - $i->getDiscountAmount();

                    if ($lineTotalCalculated == $lineTotal) {
                        $rex_data['OrderItems']['OrderItem'][] = array(
                            'ProductId' => $product->getRexProductId(),
                            'QtyOrdered' => (int) $i->getData('qty_ordered'),
                            'QtyFulfilled' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? ((int) $i->getData('qty_ordered')) : 0,
                            'UnitPrice' => $i->getPriceInclTax() - $i->getDiscountAmount() / (int) $i->getData('qty_ordered'),
                            'DeliveryDueDate' => '',
                            'DeliveryMethod' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? '' : (isset($deliveryMethod) ? $deliveryMethod : 'home'),
                            'DeliveryDriverName' => $deliveryDriver,
                            'TaxRateApplied' => $tax,
                        );
                    } else {
                        $diff = $lineTotal - $lineTotalCalculated;
                        $rex_data['OrderItems']['OrderItem'][] = array(
                            'ProductId' => $product->getRexProductId(),
                            'QtyOrdered' => (int) $i->getData('qty_ordered') - 1,
                            'QtyFulfilled' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? ((int) $i->getData('qty_ordered')) - 1 : 0,
                            'UnitPrice' => $i->getPriceInclTax() - $i->getDiscountAmount() / (int) $i->getData('qty_ordered'),
                            'DeliveryDueDate' => '',
                            'DeliveryMethod' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? '' : (isset($deliveryMethod) ? $deliveryMethod : 'home'),
                            'DeliveryDriverName' => $deliveryDriver,
                            'TaxRateApplied' => $tax,
                        );
                        $rex_data['OrderItems']['OrderItem'][] = array(
                            'ProductId' => $product->getRexProductId(),
                            'QtyOrdered' => 1,
                            'QtyFulfilled' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? 1 : 0,
                            'UnitPrice' => $i->getPriceInclTax() - $i->getDiscountAmount() / (int) $i->getData('qty_ordered') + $diff,
                            'DeliveryDueDate' => '',
                            'DeliveryMethod' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? '' : (isset($deliveryMethod) ? $deliveryMethod : 'home'),
                            'DeliveryDriverName' => $deliveryDriver,
                            'TaxRateApplied' => $tax,
                        );
                    }
                }
            } else {
                if ($i->getGiftMessageId()) {
                    $gift = Mage::helper('giftmessage/message')->getGiftMessage($i->getGiftMessageId());
                    $rex_data['PrivateComments'] .= "Item '".$product->getRexProductId()."' giftmessage from: ".$gift->getSender()."\n";
                    $rex_data['PrivateComments'] .= "Item '".$product->getRexProductId()."' giftmessage to: ".$gift->getRecipient()."\n";
                    $rex_data['PrivateComments'] .= "Item '".$product->getRexProductId()."' giftmessage message: ".$gift->getMessage()."\n";
                }

                $unitPrice = $i->getPriceInclTax() - $i->getDiscountAmount() / (int) $i->getData('qty_ordered');
                $unitPrice = round($unitPrice, 2);
                $lineTotalCalculated = $unitPrice * (int) $i->getData('qty_ordered');
                $lineTotal = $i->getPriceInclTax() * (int) $i->getData('qty_ordered') - $i->getDiscountAmount();

                if ($lineTotalCalculated == $lineTotal) {
                    $rex_data['OrderItems']['OrderItem'][] = array(
                            'ProductId' => $product->getRexProductId(),
                            'QtyOrdered' => (int) $i->getData('qty_ordered'),
                            'QtyFulfilled' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? ((int) $i->getData('qty_ordered')) : 0,
                            'UnitPrice' => $i->getPriceInclTax() - $i->getDiscountAmount() / (int) $i->getData('qty_ordered'),
                            'DeliveryDueDate' => '',
                            'DeliveryMethod' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? '' : (isset($deliveryMethod) ? $deliveryMethod : 'home'),
                            'DeliveryDriverName' => $deliveryDriver,
                            'TaxRateApplied' => $tax,
                        );
                } else {
                    $diff = $lineTotal - $lineTotalCalculated;
                    $rex_data['OrderItems']['OrderItem'][] = array(
                            'ProductId' => $product->getRexProductId(),
                            'QtyOrdered' => (int) $i->getData('qty_ordered') - 1,
                            'QtyFulfilled' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? ((int) $i->getData('qty_ordered')) - 1 : 0,
                            'UnitPrice' => $i->getPriceInclTax() - $i->getDiscountAmount() / (int) $i->getData('qty_ordered'),
                            'DeliveryDueDate' => '',
                            'DeliveryMethod' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? '' : (isset($deliveryMethod) ? $deliveryMethod : 'home'),
                            'DeliveryDriverName' => $deliveryDriver,
                            'TaxRateApplied' => $tax,
                        );
                    $rex_data['OrderItems']['OrderItem'][] = array(
                            'ProductId' => $product->getRexProductId(),
                            'QtyOrdered' => 1,
                            'QtyFulfilled' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? 1 : 0,
                            'UnitPrice' => $i->getPriceInclTax() - $i->getDiscountAmount() / (int) $i->getData('qty_ordered') + $diff,
                            'DeliveryDueDate' => '',
                            'DeliveryMethod' => $i->getIsVirtual() || $product->getTypeId() == 'downloadable' ? '' : (isset($deliveryMethod) ? $deliveryMethod : 'home'),
                            'DeliveryDriverName' => $deliveryDriver,
                            'TaxRateApplied' => $tax,
                        );
                }
            }
        }

        if ($order->getCustomerNote()) {
            $rex_data['PrivateComments'] .= 'Comment: '.$order->getCustomerNote()."\n";
        }

        $history = Mage::getModel('retailexpress/history');
        Mage::register('retail_history_id', $history->getId());
        $history->setData(
            array(
                  'type' => 'Online', 'comment' => '',
            )
        );
        try {
            $rex_data = Mage::getSingleton('retailexpress/retail')->OrderCreate(array($rex_data));
            $history->setComment('Order for quote #'.$order->getQuoteId().' created in POS: '.$rex_data['order_id']);
            Mage::register('current_order_rexid', $rex_data['order_id']);
            $order->setIncrementId($rex_data['order_id']);
            if (isset($rex_data['customer_id'])) {
                Mage::register('current_customer_rexid', $rex_data['customer_id']);
            }
        } catch (Exception $e) {
            if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
                $history->setComment('Error create order for quote #'.$order->getQuoteId().': '.$e->getMessage());
                $history->save();
            }
            // send email
            try {
                $email = Mage::getStoreConfig('retailexpress/main/email_log');
                if ($email) {
                    $mail = new Zend_Mail();
                    $mail->setBodyText('Error create order for quote #'.$order->getQuoteId().': '.$e->getMessage()."\n\nOrder Info\n\n".$order_info);
                    $mail->setFrom(Mage::getStoreConfig('trans_email/ident_general/name'), Mage::getStoreConfig('trans_email/ident_general/email'));
                    $mail->addTo($email, '');
                    $mail->setSubject('POS Order create error');
                    $mail->send();
                }
            } catch (Exception $ea) {
            }

            throw $e;
        }
        if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            $history->save();
        }
    }

    public function updateCustomerInfo($mag_id)
    {
        $customer_id = Mage::getModel('retailexpress/conf')->load('customer_'.$mag_id)->getValue();
        if (!$mag_id || !$customer_id) {
            return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online', 'comment' => '',
            )
        );
        try {
            $customer_data = Mage::getSingleton('retailexpress/retail')->CustomerGetDetails($customer_id);
            $_t = $this->updateMagentoCustomer($customer_data);
            $history->setComment($_t['str']);
        } catch (Exception $e) {
            Mage::log('Exception: updateCustomerInfo - '.$e->getMessage(), null, 'possystem.log');
            $history->setComment($e->getMessage());
        }

        if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            $history->save();
        }
    }

    public function updateMagentoCustomer($c)
    {
        if (!isset($c['rex_id'])) {
            throw new Exception('No POS Customer Id');
        }

        try {
            self::$_address_sync = true;
            if (!isset($c['email'])) {
                throw new Exception('No email');
            }

            $customer = Mage::getModel('customer/customer')->setData('website_id', $this->getWebsiteId())->loadByEmail($c['email']);
            $isNewCustomer = $customer->getId() ? false : true;
            $main = array();
            $billing = array();
            $shipping = array();
            foreach ($c as $k => $v) {
                $_t = explode('_', $k, 2);
                if (count($_t) > 1) {
                    if ('b' == $_t[0]) {
                        $billing[$_t[1]] = $v;
                    } elseif ('s' == $_t[0]) {
                        $shipping[$_t[1]] = $v;
                    }
                } else {
                    if ('subscription' == $k) {
                        $v = ($v == '0') ? false : true;
                    }

                    $main[$k] = $v;
                }
            }

            if (isset($shipping['firstname'])) {
                $_t = explode(' ', $shipping['firstname'], 2);
                $shipping['firstname'] = $_t[0];
                if (isset($_t[1])) {
                    $shipping['lastname'] = $_t[1];
                }
            }

            if (isset($billing['address']) || isset($billing['address2'])) {
                $billing['street'] = array();
                $billing['street'][] = isset($billing['address']) ? $billing['address'] : '';
                $billing['street'][] = isset($billing['address2']) ? $billing['address2'] : '';
            }

            if (isset($shipping['address']) || isset($shipping['address2'])) {
                $shipping['street'] = array();
                $shipping['street'][] = isset($shipping['address']) ? $shipping['address'] : '';
                $shipping['street'][] = isset($shipping['address2']) ? $shipping['address2'] : '';
            }

            $c_collection = Mage::getModel('directory/country')->getCollection();
            if (isset($billing['country_id'])) {
                foreach ($c_collection as $country) {
                    if (strtolower($country->getName()) == strtolower($billing['country_id'])) {
                        $billing['country_id'] = $country->getId();
                    }
                }
            }

            if (isset($shipping['country_id'])) {
                foreach ($c_collection as $country) {
                    if (strtolower($country->getName()) == strtolower($shipping['country_id'])) {
                        $shipping['country_id'] = $country->getId();
                    }
                }
            }

            if (!$isNewCustomer) {
                // remove password synchronization for existing customers
                unset($main['password']);
            } else {
                if (!isset($main['password'])) {
                    // generate new password if doesn't exist
                    $main['password'] = isset($main['password']) ? $main['password'] : $customer->generatePassword();
                }
            }

            if (isset($c['rex_group_id'])) {
                $groupCode = isset($c['rex_group_name']) ? $c['rex_group_name'] : ('POS Group Id '.$c['rex_group_id']);
                $groupModel = Mage::getModel('customer/group');
                $group = $groupModel->load($groupCode, 'customer_group_code');

                if (!$group->getId()) {
                    // if group doesn't exists, create it
                    $group = Mage::getModel('customer/group')
                                    ->setCode($groupCode)
                                    ->setTaxClassId(Mage::getModel('customer/group')->load('1')->getTaxClassId())
                                    ->save();
                }
                $group_id = $group->getId();

                if (!isset($this->_groupCache[$group_id])) {
                    $this->_groupCache[$group_id] = true;

                    $retailGroups = Mage::getModel('retailexpress/conf')->getCollection()
                            ->addFieldToFilter('conf_id', array('like' => 'group_%'))
                            ->addFieldToFilter('value', $c['rex_group_id']);
                    foreach ($retailGroups as $retailGroup) {
                        $retailGroup->delete();
                    }

                    Mage::getModel('retailexpress/conf')
                        ->setConfId('group_'.$group_id)
                        ->setValue($c['rex_group_id'])
                        ->save();
                }

                $main['group_id'] = $group_id;
            } else {
                // this assigns default group for customer
                if (!$customer->getGroupId()) {
                    $customer->setGroupId(1);
                }
            }

            $customer->setData('website_id', $this->getWebsiteId())->addData($main)->save();
            $storeId = $customer->getSendemailStoreId();

            if (count($billing)) {
                if (isset($main['firstname'])) {
                    $billing['firstname'] = $main['firstname'];
                }

                if (isset($main['lastname'])) {
                    $billing['lastname'] = $main['lastname'];
                }

                if ($customer->getDefaultBillingAddress()) {
                    $customer->getDefaultBillingAddress()
                        ->addData($billing)
                        ->save();
                } else {
                    $billing_id = Mage::getModel('customer/address')
                        ->setCustomer($customer)
                        ->addData($billing)
                        ->save()
                        ->getId();
                    $customer->setData('default_billing', $billing_id);
                }
            }

            $same_address = true;
            $_fields = array('address', 'address2', 'company', 'telephone', 'postcode', 'city', 'region', 'country_id');
            foreach ($_fields as $_f) {
                if (!isset($billing[$_f]) && !isset($shipping[$_f])) {
                    continue;
                }

                if (!isset($billing[$_f]) || !isset($shipping[$_f])) {
                    $same_address = false;
                    break;
                }

                if ($billing[$_f] != $shipping[$_f]) {
                    $same_address = false;
                    break;
                }
            }

            if (count($shipping)) {
                if ($customer->getDefaultShippingAddress()) {
                    if ($customer->getDefaultBillingAddress() && ($customer->getDefaultShippingAddress()->getId() == $customer->getDefaultBillingAddress()->getId())) {
                        if (!$same_address) {
                            $shiping_id = Mage::getModel('customer/address')
                                ->setCustomer($customer)
                                ->addData($shipping)
                                ->save()
                                ->getId();
                            $customer->setData('default_shipping', $shiping_id);
                        }
                    } else {
                        $customer->getDefaultShippingAddress()
                            ->addData($shipping)
                            ->save();
                    }
                } else {
                    $shiping_id = Mage::getModel('customer/address')
                        ->setCustomer($customer)
                        ->addData($shipping)
                        ->save()
                        ->getId();
                    $customer->setData('default_shipping', $shiping_id);
                }
            }

            if (count($billing) || count($shipping)) {
                $customer->save();
            }

            Mage::getModel('retailexpress/conf')->load('customer_'.$customer->getId())
                ->setConfId('customer_'.$customer->getId())
                ->setValue($c['rex_id'])
                ->save();
            self::$_address_sync = false;

            return array(
                'str' => 'POS Customers ID '.$c['rex_id'].' synchronised ('.$customer->getId().")\n",
                'new' => $isNewCustomer,
            );
        } catch (Exception $e) {
            self::$_address_sync = false;
            throw new Exception('POS Customers ID '.$c['rex_id'].' error: '.$e->getMessage()."\n");
        }
    }

    public function putCustomer($customer)
    {
        if (self::$_address_sync) {
            return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online', 'comment' => '',
            )
        );
        self::$_address_sync = true;
        try {
            if (Mage::registry('current_customer_rexid')) {
                Mage::getModel('retailexpress/conf')->load('customer_'.$customer->getId())
                    ->setConfId('customer_'.$customer->getId())
                    ->setValue(Mage::registry('current_customer_rexid'))
                    ->save();
                self::$_address_sync = false;

                return;
                Mage::unregister('current_customer_rexid');
            }

            $rex_id = Mage::getModel('retailexpress/conf')->load('customer_'.$customer->getId())->getValue();
            $rex_data = array();
            $rex_data['ExternalCustomerId'] = $customer->getId();

            $rex_data['BillEmail'] = $customer->getEmail();
            $rex_data['BillFirstName'] = $customer->getData('firstname');
            $rex_data['BillLastName'] = $customer->getData('lastname');
            $rex_data['ReceivesNews'] = (int) $customer->getIsSubscribed();
            if ($customer->getDefaultBillingAddress()) {
                $streets = $customer->getDefaultBillingAddress()->getStreet();
                $rex_data['BillAddress'] = isset($streets[0]) ? $streets[0] : '';
                $rex_data['BillAddress2'] = isset($streets[1]) ? $streets[1] : '';
                $rex_data['BillCompany'] = $customer->getDefaultBillingAddress()->getCompany();
                $rex_data['BillPhone'] = $customer->getDefaultBillingAddress()->getData('telephone');
                $rex_data['BillPostCode'] = $customer->getDefaultBillingAddress()->getData('postcode');
                $rex_data['BillSuburb'] = $customer->getDefaultBillingAddress()->getData('city');
                $rex_data['BillState'] = $customer->getDefaultBillingAddress()->getData('region');
                $rex_data['BillCountry'] = Mage::getModel('directory/country')->loadByCode($customer->getDefaultBillingAddress()->getData('country_id'))->getName();
            }

            if ($customer->getDefaultShippingAddress()) {
                $streets = $customer->getDefaultShippingAddress()->getStreet();
                $rex_data['DelName'] = $customer->getDefaultShippingAddress()->getData('firstname').' '.$customer->getDefaultShippingAddress()->getData('lastname');
                $rex_data['DelAddress'] = isset($streets[0]) ? $streets[0] : '';
                $rex_data['DelAddress2'] = isset($streets[1]) ? $streets[1] : '';
                $rex_data['DelCompany'] = $customer->getDefaultShippingAddress()->getCompany();
                $rex_data['DelPhone'] = $customer->getDefaultShippingAddress()->getData('telephone');
                $rex_data['DelPostCode'] = $customer->getDefaultShippingAddress()->getData('postcode');
                $rex_data['DelSuburb'] = $customer->getDefaultShippingAddress()->getData('city');
                $rex_data['DelState'] = $customer->getDefaultShippingAddress()->getData('region');
                $rex_data['DelCountry'] = Mage::getModel('directory/country')->loadByCode($customer->getDefaultShippingAddress()->getData('country_id'))->getName();
            }

            if ($rex_id) {
                $rex_data['CustomerId'] = $rex_id;
            }

            $rex_id = Mage::getSingleton('retailexpress/retail')->CustomerCreateUpdate(array($rex_data));
            Mage::getModel('retailexpress/conf')->load('customer_'.$customer->getId())
                    ->setConfId('customer_'.$customer->getId())
                    ->setValue($rex_id)
                    ->save();
            $history->setComment('Magento Customers ID '.$customer->getId().' synchronised to POS ('.$rex_id.")\n");
        } catch (Exception $e) {
            Mage::log('Exception: Magento Customers ID '.$customer->getId().' error synchronised to POS: '.$e->getMessage(), null, 'possystem.log');
            $history->setComment('Magento Customers ID '.$customer->getId().' error synchronised to POS: '.$e->getMessage()."\n");
        }

        self::$_address_sync = false;
        if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            $history->save();
        }
    }

    public function getOrdersHistory()
    {
        $return = array();
        $customer = Mage::registry('current_customer');
        if (!$customer || !$customer->getId()) {
            return $return;
        }

        $rex_id = Mage::getModel('retailexpress/conf')->load('customer_'.$customer->getId())->getValue();
        if (!$rex_id) {
            return $return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online', 'comment' => '',
            )
        );
        try {
            $return = Mage::getSingleton('retailexpress/retail')->OrdersGetHistory($rex_id);
            $history->setComment('Magento Customers ID '.$customer->getId()." synchronised history orders from POS\n");
        } catch (Exception $e) {
            Mage::log('Exception: Magento Customers ID '.$customer->getId().' error in history orders from POS: '.$e->getMessage(), null, 'possystem.log');
            $history->setComment('Magento Customers ID '.$customer->getId().' error in history orders from POS: '.$e->getMessage()."\n");
        }

        if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            $history->save();
        }

        return $return;
    }

    public function getOrderHistory($order_id)
    {
        $return = new Varien_Object();
        $customer = Mage::registry('current_customer');
        if (!$customer || !$customer->getId()) {
            return $return;
        }

        $rex_id = Mage::getModel('retailexpress/conf')->load('customer_'.$customer->getId())->getValue();
        if (!$rex_id) {
            return $return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online', 'comment' => '',
            )
        );
        try {
            $return->addData(Mage::getSingleton('retailexpress/retail')->OrdersGetHistory($rex_id, $order_id));
            $history->setComment('Magento Customers ID '.$customer->getId()." synchronised history orders from POS\n");
        } catch (Exception $e) {
            Mage::log('Exception: Magento Customers ID '.$customer->getId().' error in history orders from POS: '.$e->getMessage(), null, 'possystem.log');
            $history->setComment('Magento Customers ID '.$customer->getId().' error in history orders from POS: '.$e->getMessage()."\n");
        }

        if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            $history->save();
        }

        return $return;
    }

    public function cancelOrder($order)
    {
        $this->_saveRexOrderId($order);
        $rex_id = Mage::getModel('retailexpress/conf')->load('order_'.$order->getId())->getValue();
        if (!$rex_id) {
            return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online', 'comment' => '',
            )
        );
        try {
            $return = Mage::getSingleton('retailexpress/retail')->orderCancel($rex_id);
            $history->setComment('Magento Order ID '.$order->getId().' canceled in POS result : '.$return);
        } catch (Exception $e) {
            Mage::log('Exception: Magento Order ID '.$order->getId().' error during cancel in POS: '.$e->getMessage(), null, 'possystem.log');
            $history->setComment('Magento Order ID '.$order->getId().' error during cancel in POS: '.$e->getMessage()."\n");
        }
        if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            $history->save();
        }
    }

    /**
     * Check for Process Control functions exists.
     *
     * @return bool
     */
    public function isPcntl()
    {
        return $this->_is_pcntl;
    }

    protected function _saveRexOrderId($order)
    {
        if (Mage::registry('current_order_rexid')) {
            $rex_id = Mage::registry('current_order_rexid');
            Mage::unregister('current_order_rexid');
            $this->_setConfigValue('order_'.$order->getId(), $rex_id);
        }
    }

    public function doneOrder($order)
    {
        $this->_saveRexOrderId($order);
    }

    public function payOrder($invoice)
    {
        if (self::$_order_sync) {
            return;
        }
        $order = $invoice->getOrder();
        $rex_id = $this->_getConfigValue('order_'.$order->getId());

        if (!$rex_id && Mage::registry('current_order_rexid')) {
            $rex_id = Mage::registry('current_order_rexid');
        }

        if (!$rex_id) {
            return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online', 'comment' => '',
            )
        );
        $rex_payment_id = '';
        $method = '';
        if ($order->getPayment()) {
            $__t = Mage::getStoreConfig('retailexpress/payments/'.$order->getPayment()->getMethod());
            $rex_payment_id = Mage::getModel('retailexpress/payment')->load($__t)->getRexId();
            $method = $order->getPayment()->getMethod();
        }

        try {
            if (!$rex_payment_id) {
                throw new Exception('Rex payment not setuped for '.$method);
            }

            $rex_data = array(
                'OrderId' => $order->getIncrementId(),
                'MethodId' => $rex_payment_id,
                'Amount' => (float) abs($invoice->getGrandTotal()),
                'DateCreated' => date('c'),

            );
            $return = Mage::getSingleton('retailexpress/retail')->OrderAddPayment(array($rex_data));
            $history->setComment('Magento Order ID '.$order->getId().' added payment to POS with result: '.$return);
        } catch (Exception $e) {
            Mage::log('Exception: Magento Order ID '.$order->getId().' added payment error: '.$e->getMessage(), null, 'possystem.log');
            $history->setComment('Magento Order ID '.$order->getId().' added payment error: '.$e->getMessage()."\n");
        }

        if (Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            $history->save();
        }
    }

    /**
     * Setting internal config value.
     *
     * @param $name string - name of config
     * @param $value string - value of config
     */
    protected function _setConfigValue($name, $value)
    {
        Mage::getModel('retailexpress/conf')->load($name)
            ->setConfId($name)
            ->setValue($value)
            ->save();
    }

    /**
     * Getting internal config value.
     *
     * @param string $name - name of internal config
     *
     * @return string|null - value of the config
     */
    protected function _getConfigValue($name)
    {
        return Mage::getModel('retailexpress/conf')->load($name)->getValue();
    }

    /**
     * get status of product bulk method.
     *
     * @return int status ot product bulk sync
     */
    public function getProductBulkSynchronizationStatus()
    {
        return Mage::getModel('retailexpress/import_product')->getStatus();
    }

    /**
     * get status of customer bulk method.
     *
     * @return int status ot customer bulk sync
     */
    public function getCustomerBulkSynchronizationStatus()
    {
        return Mage::getModel('retailexpress/import_customer')->getStatus();
    }

    /**
     * run all synchronization.
     */
    public function synchronizeBulk()
    {
        $start_time = time();
        $this->_cleanUpCategories();
        Mage::getModel('retailexpress/import_product')->import();
        $end_time = time();
        if (($end_time - $start_time) < Mage::getSingleton('retailexpress/config')->getConfigBulkTime()) {
            Mage::getModel('retailexpress/import_customer')->import();
        }
    }

    /**
     * Apply all price rules to product.
     *
     * @param int|Mage_Catalog_Model_Product $product
     *
     * @return Mage_CatalogRule_Model_Rule
     */
    protected function _applyAllRulesToProduct($product)
    {
        $resource = Mage::getResourceSingleton('catalogrule/rule');
        $resource->applyAllRulesForDateRange(null, null, $product);
    }

    /**
     * Invalidate related cache types.
     *
     * @return Mage_CatalogRule_Model_Rule
     */
    protected function _invalidateCache()
    {
        $types = Mage::getConfig()->getNode('global/catalogrule/related_cache_types');
        if ($types) {
            $types = $types->asArray();
            Mage::app()->getCacheInstance()->invalidateType(array_keys($types));
        }

        return $this;
    }

    // retrive attribute id by code
    protected function _getAttibuteIdByCode($code)
    {
        try {
            $attributeModel = Mage::getModel('eav/entity_attribute');
            // doing this check to prevent a blank screen in incompatible versions of magento
            if ($attributeModel) {
                $attribute = $attributeModel->getCollection()->addFieldToFilter('attribute_code', $code)->getFirstItem();
                if ($attribute->getAttributeId()) {
                    return $attribute->getAttributeId();
                }
            } else {
                throw new exception('Attribute model eav/entity_attribute doesn\'t exists. Tested on magento 1.5.1.0 and 1.7.0.2, so maybe issue is in magento version');
            }
            throw new exception('Attribute '.$code.' not found');
        } catch (exception $e) {
            throw $e;
        }
    }

    /**
     * This function clean up the broken categories before bulk import.
     **/
    protected function _cleanUpCategories()
    {
        $collection = Mage::getModel('catalog/category')->getCollection()->addFieldToFilter('path', array('like' => '%//%'));
        if (count($collection) > 0) {
            foreach ($collection as $category) {
                $category->delete();
            }
        }
    }

    /**
     * make command to sync.
     */
    public function waitBulk()
    {
        // OUTLETS START
        $result = $this->syncOutlet();
        $comment = '';
        foreach ($result as $k => $v) {
            $comment .= $k.":\n".$v."\n\n";
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Cron', 'comment' => $comment,
            )
        );
        $history->save();
        // OUTLETS END

        // QUEUED PRODUCTS / CUSTOMERS START
        Mage::getModel('retailexpress/import_product')->wait();
        Mage::getModel('retailexpress/import_customer')->wait();
        // QUEUED PRODUCTS / CUSTOMERS END

        // ORDERS START
        $result = $this->synhOrdersBulk();
        $comment = '';
        foreach ($result as $k => $v) {
            $comment .= $k.":\n".$v."\n\n";
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Cron', 'comment' => $comment,
            )
        );
        $history->save();
        // ORDERS END
    }

    /**
     * process each step of cron iteration.
     */
    protected function _processProductBulk()
    {
        if ($this->_getConfigValue('pb_pid') && function_exists('posix_getsid') && (posix_getsid($this->_getConfigValue('pb_pid')) !== false)) {
            return;
        }

        $this->_setConfigValue('pb_pid', getmypid());
        $finished = $this->synhProductsBulk();
        $this->_setConfigValue('pb_pid', '');
        if ($finished) {
            $this->_finishProductBulk();
        }
    }

    public function isSerialized($data)
    {
        // if it isn't a string, it isn't serialized
        if (!is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (!preg_match('/^([adObis]):/', $data, $badions)) {
            return false;
        }
        switch ($badions[1]) {
            case 'a' :
            case 'O' :
            case 's' :
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                    return true;
                }
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) {
                    return true;
                }
                break;
        }

        return false;
    }

    public function syncOutlet()
    {
        $model = Mage::getModel('retailexpress/retail');
        if ($model->getError()) {
            throw new Exception($model->getError());
        }

        $data = $model->OutletsGet();
        $data = $model->parseOutletXML($data);

        $report = array('Outlets' => '');
        $processed = 0;
        $created = 0;
        $updated = 0;
        $errored = 0;
        $removed = 0;
        $error_mes = '';

        $addedOutletId = array();

        foreach ($data['outlets'] as $outletData) {
            try {
                ++$processed;
                $fulfilmentOutletId = $outletData['fulfilment_outlet_id'];
                $addedOutletId[] = $fulfilmentOutletId;
                $outlet = Mage::getModel('retailexpress/outlet')->loadByFulfilmentOutletId($fulfilmentOutletId);
                if ($outlet->getOutletId()) {
                    $outletId = $outlet->getOutletId();
                    foreach ($outletData as $key => $value) {
                        if ($value != $outlet->getData($key)) {
                            ++$updated;
                            $outlet->setData($outletData)->setOutletId($outletId);
                            $outlet->save();
                            continue;
                        }
                    }
                } else {
                    ++$created;
                    $outlet = Mage::getModel('retailexpress/outlet');
                    $outlet->setData($outletData);
                    $outlet->save();
                }
            } catch (Exception $e) {
                ++$errored;
                $msg = 'Outlet '.$fulfilmentOutletId.':  '.$e->getMessage()."\n";
                $error_mes .= $msg;
                $report['Outlets'] .= $msg;
            }
        }

        $outletCollection = Mage::getModel('retailexpress/outlet')->getCollection()->addFieldToFilter('fulfilment_outlet_id', array('nin' => $addedOutletId));
        $removed = count($outletCollection);
        foreach ($outletCollection as $item) {
            $item->delete();
        }

        if (!Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            return array('Bulk Synchronisation of Outlets Completed Successfully' => $processed.' Processed, '.$created.' Created, '.$updated.' Updated, '.$removed.' Removed, '.$errored.' Errors.'."\n".$error_mes);
        }

        return $report;
    }

    public function getChannelId()
    {
        return Mage::getStoreConfig(self::XML_PATH_CHANNEL_ID);
    }

    public function getPosSystemEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_RETAILEXPRESS_ENABLED);
    }

    public function OrderDeliveryUpdate($shipment)
    {
        $order = Mage::getModel('sales/order')->load($shipment->order_id);
        $rexData = array();
        $tracknums = array();
        $rexData['OrderId'] = $order->getIncrementId();
        $rexData['Reference'] = $shipment->number;
        $deliveryDriverArray = explode(' - ', $order->getShippingDescription());
        $deliveryDriver = $deliveryDriverArray[0];
        $rexData['DeliveryDriverName'] = $deliveryDriver;
        $rex_data = Mage::getSingleton('retailexpress/retail')->OrderDeliveryUpdate($rexData);
    }

    /**
     * Returns maximum size of packet, that we can send to DB.
     *
     * @return int
     */
    public function getMaxDataSize()
    {
        $maxPacketData = $this->_getReadAdapter()->fetchRow('SHOW VARIABLES LIKE "max_allowed_packet"');
        $maxPacket = empty($maxPacketData['Value']) ? self::DB_MAX_PACKET_SIZE : $maxPacketData['Value'];

        return floor($maxPacket * self::DB_MAX_PACKET_COEFFICIENT);
    }

    /**
     * Retrieve connection for read data.
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getReadAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }

    /**
     * update last_date column of sync_product table.
     *
     * @param array $productIds
     */
    public function updateProductSynchronizationTime($productIds)
    {
        foreach ($productIds as $id) {
            $p = Mage::getModel('retailexpress/product')
                ->getCollection()
                ->addFieldToFilter('product_id', $id)
                ->addFieldToFilter('customer_id', 0)
                ->getFirstItem()
                ->setProductId($id)
                ->setCustomerId(0)
                ->setLastDate($this->_time())
                ->save();
        }
    }

    /**
     * returns UTC time.
     *
     * @return string
     */
    public function _time()
    {
        return gmdate('U', time());
    }
}
