<?php

class POS_System_Helper_Data extends Mage_Core_Helper_Abstract
{

    static protected $_address_sync = false;
    static protected $_order_sync = false;

    static protected $_created_orders = array();

	public function synhProducts()
	{
		$page = 1;
		$step = 100;
		$model = Mage::getModel('retailexpress/retail');
		if ($model->getError()) {
			throw new Exception($model->getError());
		}

		$return = "Product Synchronise:\n";
		while (true) {
			$collection = Mage::getResourceModel('catalog/product_collection')
    	        ->setCurPage($page)
    	        ->setPageSize($step)
    	        ->setOrder('entity_id', 'ASC');
    		$products = $collection->getItems();
    		foreach ($products as $p) {
    			$product = Mage::getModel('catalog/product')->load($p->getId());
    			$retail_product_id = $product->getRetailProductId();
    			if (!$retail_product_id) {
    				$retail_product_id = $product->getSku();
    			}

    			if ($retail_product_id) {
    				$data = $model->getProductById($retail_product_id);
    				$time = explode(" ", microtime());
            		$string = "[" . sprintf("% 15s", date("H:i:s.", $time[1]) . sprintf("%06s", intval($time[0]*1000000))) . "] ";
            		$log = $string . "Product #" . $p->getId() . " - " . $retail_product_id . " - '" . print_r($data, 1) . "';\n";
    				$file = Mage::getBaseDir('var') . DS . 'log' . DS . 'retail.log';
					$fd = fopen($file, "ab+");
					chmod($file, 0666);
					fwrite($fd, $log);
					fclose($fd);
    				if (is_array($data)) {
    					$return .= "#" . $retail_product_id . " - synchronised;\n";
    					$save_data = array(
    						'price' => $data['price'],
    						'special_price' => $data['special_price'],
    						'stock_data' => array('qty' => $data['qty'])
    					);
    					$product->addData($save_data);
    				} else {
    					$return .= "#" . $p->getId() . " - " . $data . ";\n";
    				}

    				$product->save();
    			}
    		}

    		if ($page == $collection->getLastPageNumber()) {
    			break;
    		}

    		$page++;
		}

		return $return . ".\n";
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
                $rex_id = Mage::getModel('retailexpress/conf')->load('order_' . $order->getId())->getValue();
                if (!$order->getId() || !$rex_id) {
                    $report['Orders'] .= "POS Order " . $id . " do not create in magento\n";
                    continue;
                }

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
                    if (!isset($items[$rex_id])) {
                        continue;
                    }

                    if ($item->getQtyToShip() < $items[$rex_id]['qty']) {
                        continue;
                    }

                    $savedQtys[$item->getId()] = $items[$rex_id]['qty'];
                }

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
                    $report['Orders'] .= "POS Order " . $id . " shipments already made\n";
                }
                if (isset($items['payment'])) {
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
                        $report['Orders'] .= "POS Order " . $id . " payment already made\n";
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
                        Mage::logException($e);
                    }
                    $updated++;
                    if ($is_invoice) {
                        $report['Orders'] .= "POS Order " . $id . " payment made\n";
                    }
                    if ($is_ship) {
                        $report['Orders'] .= "POS Order " . $id . " adding shipment (" . $shipment->getId() . ")\n";
                    }
                }
            } catch (Exception $e) {
                $errored++;
                $msg = "POS Order " . $id . " adding shipment error: " . $e->getMessage() . "\n";
                $error_mes .= $msg;
                $report['Orders'] .= $msg;
            }
        }

        self::$_order_sync = false;
        $this->setBulkLastTime('order', $current_time);
        if (!POS_System_Model_Config::DEBUG_IN_HISTORY) {
            return array('Bulk Synchronisation of Orders Completed Successfully' => $updated . ' Updated, ' . $errored . ' Errors.' . "\n" . $error_mes);
        }

        return $report;
    }


	public function synhOrders()
	{
		$page = 1;
		$step = 10;
		$write_db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$model = Mage::getModel('retailexpress/retail');
		if ($model->getError()) {
			throw new Exception($model->getError());
		}

		$return = "Order Synchronise:\n";
		while (true) {
			$conditions = array("`retailexpress/order`.order_id = `main_table`.entity_id");

			$collection = Mage::getResourceModel('sales/order_grid_collection');
			$collection->getSelect()->joinLeft(
	            	array('retailexpress/order' => $collection->getTable('retailexpress/order')),
	            	join(' AND ', $conditions),
	            	array('so_id' => new Zend_Db_Expr("`retailexpress/order`.order_id"))
	        	);
	        $collection->addFieldToFilter('`retailexpress/order`.order_id', array('null' => 1))
	        	->addFieldToFilter('status', array('in' => array('processing', 'complete')))
    	        ->setCurPage($page)
    	        ->setPageSize($step)
    	        ->setOrder('entity_id', 'ASC');
    		$orders = $collection->getItems();
    		foreach ($orders as $o) {
    			$order = Mage::getModel('sales/order')->load($o->getId());
    			$data = array(
    				  'Customer' => array(
    					  'FirstName' => $order->getData('customer_firstname')
    					, 'LastName' => $order->getData('customer_lastname')
    					, 'EmailAddress' => $order->getData('customer_email')
    					, 'Address1' => $order->getBillingAddress()?$order->getBillingAddress()->getData('street'):''
    					, 'Suburb' => $order->getBillingAddress()?$order->getBillingAddress()->getCity():''
    					, 'State' => $order->getBillingAddress()?$order->getBillingAddress()->getRegion():''
    					, 'PostCode' => $order->getBillingAddress()?$order->getBillingAddress()->getPostcode():''
    					, 'DeliveryAddress1' => $order->getShippingAddress()?$order->getShippingAddress()->getData('street'):''
    					, 'DeliverySuburb' => $order->getShippingAddress()?$order->getShippingAddress()->getCity():''
    					, 'DeliveryState' => $order->getShippingAddress()?$order->getShippingAddress()->getRegion():''
    					, 'DeliveryPostCode' => $order->getShippingAddress()?$order->getShippingAddress()->getPostcode():''
    				)
    				, 'DateCreated' => time()//$order->getData('created')
    				, 'OrderTotal' => $order->getData('total_due')
    				, 'AmountPaid' => $order->getData('total_due')
    				, 'FreightTotal' => 0
    				, 'OrderItems' => array(
    					'OrderItem' => array(
    					)
    				)
    				, 'OrderPayments' => array(
    					'OrderPayment' => array(
    						  'PaymentMethodID' => ''
    						, 'Amount' => $order->getData('total_due')
    						, 'DateCreated' => time()//$order->getData('created')
    					)
    				)
    			);

    			foreach ($order->getItemsCollection() as $i) {
    				$data['OrderItems']['OrderItem'][] = array(
    					  'QtyOrdered' => (int)$i->getData('qty_ordered')
    					, 'QtyFulfilled' => (int)$i->getData('qty_ordered')
    					, 'UnitPrice' => $i->getData('price')
    					, 'ProductID' => (int)Mage::getModel('catalog/product')->load($i->getProductId())->getSku()
    				);
    			}
    			$result = $model->putOrder($data);

    				$time = explode(" ", microtime());
            		$string = "[" . sprintf("% 15s", date("H:i:s.", $time[1]) . sprintf("%06s", intval($time[0]*1000000))) . "] ";
            		$log = $string . "Order #" . $o->getId() . " - '" . print_r($result, 1) . "';\n";
    				$file = Mage::getBaseDir('var') . DS . 'log' . DS . 'retail.log';
					$fd = fopen($file, "ab+");
					chmod($file, 0666);
					fwrite($fd, $log);
					fclose($fd);

    			if (is_array($result)) {
    				$return .= "#" . $o->getId() . " - synchronised (" . $result["id"] . ");\n";
    			} else {
    				$result .= "#" . $o->getId() . " - " . $result . ";\n";
    			}

				Mage::getModel('retailexpress/order')->setOrderId($o->getId())->save();
    		}

    		if ($page == $collection->getLastPageNumber()) {
    			break;
    		}

    		$page++;
		}

		return $return . ".\n";
	}


	public function syncProductStockById($productId)
	{

        $pricegroup_id = 0;
        $mag_id = 0;
        if (Mage::getSingleton('customer/session')->getCustomer()) {
            $mag_id = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            $pricegroup_id = (int)Mage::getModel('retailexpress/conf')->load('group_' . $mag_id)->getValue();
        }
		$product = Mage::getModel('catalog/product')->load($productId);
        if (is_callable(array($product, 'getTypeInstance')) && is_callable(array($product->getTypeInstance(), 'getUsedProducts'))) {
            $configurable_products = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
    	    $products = $configurable_products->getUsedProductCollection()->addAttributeToSelect('*');

            $t = Mage::getModel('retailexpress/product')
                ->getCollection()
                ->addFieldToFilter('product_id', $product->getId())
                ->addFieldToFilter('customer_id', 0)
                ->getFirstItem()
                ->setProductId($product->getId())
                ->setCustomerId(0)
                ->setLastDate(time())
                ->save();

            if ($mag_id && $pricegroup_id) {

                $t = Mage::getModel('retailexpress/product')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $product->getId())
                    ->addFieldToFilter('customer_id', $pricegroup_id)
                    ->getFirstItem()
                    ->setProductId($product->getId())
                    ->setCustomerId($mag_id)
                    ->setLastDate(time())
                    ->save();

            }
        } else {
            $products = array($product);
        }

        foreach ($products as $p) {
            $product = Mage::getModel('catalog/product')->load($p->getId());
            $retail_product_id = $this->getRetailProductId($product);
            if (!$retail_product_id) {
                continue;
            }


            $history = Mage::getModel('retailexpress/history');
            $history->setData(
                array(
                      'type' => 'Online'
                    , 'comment' => ''
                )
            );
            Mage::register("retail_history_id", $history->getId());

            try {
                $retail_product_data = Mage::getSingleton('retailexpress/retail')->getProductStockPriceById($retail_product_id, $pricegroup_id);
                $need_save = false;
                if (!$mag_id || ! $pricegroup_id) {
                    if (is_array($retail_product_data)) {

                        $updateData = $retail_product_data;
                        $productStatus = 1;

                    } else {

                        $productStatus = 2;
                        $updateData = array();

                    }

                    $need_save = true;
                } else {
                    if ($this->_needSyncProductByCustomer($product->getId(), 0)) {
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

                        $t = Mage::getModel('retailexpress/product')
                            ->getCollection()
                            ->addFieldToFilter('product_id', $product->getId())
                            ->addFieldToFilter('customer_id', 0)
                            ->getFirstItem()
                            ->setProductId($product->getId())
                            ->setCustomerId(0)
                            ->setLastDate(time())
                            ->save();

                    }
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
                    $stockMainTable = $tPrefix . 'cataloginventory_stock_item';
                    $stockStatusTable = $tPrefix . 'cataloginventory_stock_status';
                    $stockStatusIndexTable = $tPrefix . 'cataloginventory_stock_status_idx';
                    $catalogProductDecimalTable = $tPrefix . 'catalog_product_entity_decimal';
                    $productStatusTable = $tPrefix . 'catalog_product_entity_int';

                    $productId = $product->getId();
                    $updateStockData = false;

                    if (!empty($updateData['stock_data'])) {

                        $qty = $retail_product_data['stock_data']['qty'];
                        $isInStock = $retail_product_data['stock_data']['is_in_stock'] ? $retail_product_data['stock_data']['is_in_stock'] : 0;
                        $manageStock = $retail_product_data['stock_data']['manage_stock'] != '' ? $retail_product_data['stock_data']['manage_stock'] : 0;
                        $useConfigManageStock = $retail_product_data['stock_data']['use_config_manage_stock'];
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

                    if ($updateStockData) {

                        $sql = "UPDATE {$stockMainTable} SET qty={$qty}, is_in_stock={$isInStock}, manage_stock={$manageStock}, use_config_manage_stock={$useConfigManageStock} WHERE product_id='{$productId}'";
                        try {
                        	$write->query($sql);
                        } catch (Exception $e) {
                        	Mage::log('POS_System_Helper_Data:: Product Stock Status Error Update. Error: ' . $e->getMessage(), null, 'possystem.log');
                        }

                        $sql = "UPDATE {$stockStatusTable} SET qty={$qty}, stock_status={$isInStock} WHERE product_id='{$productId}'";
                        try {
                        	$write->query($sql);
                        } catch (Exception $e) {
                        	Mage::log('POS_System_Helper_Data:: Product Stock Status Error Update. Error: ' . $e->getMessage(), null, 'possystem.log');
                        }

                        $sql = "UPDATE {$stockStatusIndexTable} SET qty={$qty}, stock_status={$isInStock} WHERE product_id='{$productId}'";
                        try {
                        	$write->query($sql);
                        } catch (Exception $e) {
                        	Mage::log('POS_System_Helper_Data:: Product Stock Status Error Update. Error: ' . $e->getMessage(), null, 'possystem.log');
                        }

                    }

                    if ($updatePrice) {

                        $sql = "UPDATE {$catalogProductDecimalTable} SET value={$price} WHERE attribute_id='{$priceAttributeId}' AND entity_id='{$productId}'";
                        try {
                        	$write->query($sql);
                        } catch (Exception $e) {
                        	Mage::log('POS_System_Helper_Data:: Product Stock Status Error Update. Error: ' . $e->getMessage(), null, 'possystem.log');
                        }

                    }

                    if ($updateSpecialPrice) {

                        $sql = "UPDATE {$catalogProductDecimalTable} SET value={$specialPrice} WHERE attribute_id='{$specialPriceAttributeId}' AND entity_id='{$productId}'";
                        try {
                        	$write->query($sql);
                        } catch (Exception $e) {
                        	Mage::log('POS_System_Helper_Data:: Product Stock Status Error Update. Error: ' . $e->getMessage(), null, 'possystem.log');
                        }

                    }

                    $sql = "UPDATE {$productStatusTable} SET value={$productStatus} WHERE attribute_id='{$statusAttributeId}' AND entity_id='{$productId}'";
                    try {
                       	$write->query($sql);
                    } catch (Exception $e) {
                    	Mage::log('POS_System_Helper_Data:: Product Stock Status Error Update. Error: ' . $e->getMessage(), null, 'possystem.log');
                    }

                }

                $t = Mage::getModel('retailexpress/product')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $product->getId())
                    ->addFieldToFilter('customer_id', $pricegroup_id?$mag_id:0)
                    ->getFirstItem()
                    ->setProductId($product->getId())
                    ->setCustomerId($pricegroup_id?$mag_id:0)
                    ->setPrice(isset($retail_product_data['price'])?$retail_product_data['price']:$product->getPrice())
                    ->setData('special_price', isset($retail_product_data['special_price']) ? $retail_product_data['special_price']:$product->getSpecialPrice())
                    ->setLastDate(time())
                    ->save();

                $history->setComment('Synchronise Product Stock for POS Product ID ' . $retail_product_id . " synchronised (#" . $product->getId() . ")");
            } catch (Exception $e) {
                $history->setComment('Synchronise Product Stock for POS Product ID ' . $retail_product_id . " error: " . $e->getMessage() . ")");
            }

            if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
                $history->save();
            }
        }
	}


	public function getRetailProductId($product)
	{
		return $product->getRexProductId();
	}

    protected function _needSyncProductByCustomer($productId, $pricegroup_id)
    {
		$_t = POS_System_Model_Config::PRODUCT_TRUST_TIME;
		if ("" === $_t) {
			return false;
		}

        if (!$productId) {
            return false;
        }

		$_t = (int)$_t;
		$last = (int)Mage::getModel('retailexpress/product')
            ->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('customer_id', $pricegroup_id)
			->getFirstItem()
			->getLastDate();
		if (($last + $_t) < time()) {
			return true;
		}

		return false;
    }


	public function needSyncPrice($productId)
	{
        $pricegroup_id = 0;
        if (Mage::getSingleton('customer/session')->getCustomer()) {
            $pricegroup_id = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            if (!(int)Mage::getModel('retailexpress/conf')->load('group_' . $pricegroup_id)->getValue()) {
                $pricegroup_id = 0;
            }
        }

        return $this->_needSyncProductByCustomer($productId, $pricegroup_id);
	}

    public function unzip($str, $id)
    {
        $dir =  Mage::getBaseDir('var') . DS . 'retail' . DS . 'zip' . DS;
        $file_in = $dir . $id . '.in';
        $file_out = $dir . $id . '.in';
        $fd = fopen($file_in, "wb+");
        if (!$fd) {
            throw new Exception('Cannot open file ' . $file_in);
        }

        chmod($file_in, 0666);
        if (!fwrite($fd, $str)) {
            fclose($fd);
            throw new Exception('Cannot write to file ' . $file_in);
        }

        fclose($fd);
        clearstatcache();
        $res = gzopen($file_in, "rb");
        if ($res === false) {
            throw new Exception('Failed open gzip archive ' . $file_in);
        }

        $return = "";
        while ($content = gzread($res, 1000)) {
            $return .= $content;
        }

        gzclose($res);
        return trim($return);
    }


    public function checkVoucher($code)
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}

        try {
            $history = Mage::getModel('retailexpress/history');
            $history->setData(
                array(
                      'type' => 'Online'
                    , 'comment' => ''
                )
            );
            Mage::register("retail_history_id", $history->getId());
            $amount = Mage::getModel('retailexpress/retail')->VoucherGetBalance($code);
            if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
                $history->setComment('Synchronise voucher "' . $code . '" Amount: ' . $amount)
                    ->save();
            }

            $model = Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('code', $code)->getFirstItem();
            if ($model->getId()) {
                $model->delete();
            }

            if ($amount < 0.01) {
                return;
            }

            $customerGroups = Mage::getResourceModel('customer/group_collection')
                ->load()->toOptionArray();
            $found = false;
            $groups = array();
            foreach ($customerGroups as $group) {
                $groups[] = $group['value'];
                if ($group['value']==0) {
                    $found = true;
                }
            }
            if (!$found) {
                $groups[] = 0;
            }

            $model = Mage::getModel('salesrule/rule');
            $data = array(
                'apply_to_shipping' => 0,
                'coupon_code' => $code,
                'coupon_type' => Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC,
                'customer_group_ids' => $groups,
                'description' => '',
                'discount_amount' => $amount,
                'discount_qty' => '',
                'discount_step' => '',
                'is_active' => 1,
                'is_rss' => 1,
                'name' => 'Coupon',
                'product_ids' => '',
                'rule' > array(
                    'action' => array(
                        1 => array(
                            'type' => 'salesrule/rule_condition_product_combine',
                            'aggregator' => 'all',
                            'value' => 'a',
                            'new_child' => '',
                        )
                    ),
                    'conditions' => array(
                        1 => array(
                            'type' => 'salesrule/rule_condition_combine',
                            'aggregator' => 'all',
                            'value' => 'a',
                            'new_child' => '',
                        )
                    )
                ),
                'simple_action' => Mage_SalesRule_Model_Rule::CART_FIXED_ACTION,
                'simple_free_shipping' => 0,
                'sort_order' => 0,
                'stop_rules_processing' => 0,
                'uses_per_coupon' => 1,
                'uses_per_customer' => 1,
                'website_ids' => array(Mage::app()->getStore()->getWebsiteId()),
            );
            $validateResult = $model->validateData(new Varien_Object($data));
            if ($validateResult === true) {
                $model->loadPost($data);
                $model->save();
            }
        } catch (Exception $e) {
            if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
                $history->setComment('Synchronise voucher "' . $code . '" error: ' . $e->getMessage())
                    ->save();
            }
        }
    }

    public function getWebsiteId()
    {
        return Mage::getModel('core/website')->getCollection()->getFirstItem()->getId();
    }

    public function checkBulkTimeout($label)
    {
        // REX do it on thier side
        return true;
        $cur_value = Mage::getModel('retailexpress/conf')->load('bulk_' . $label)->getValue();
        if (((int)$cur_value + POS_System_Model_Config::SYNC_BULK_TIMEOUT) > time()) {
            $sec = (int)$cur_value + POS_System_Model_Config::SYNC_BULK_TIMEOUT - time();
            throw new Exception('Bulk method are not available in next ' . $sec . " seconds");
        }

        Mage::getModel('retailexpress/conf')->load('bulk_' . $label)
                ->setConfId('bulk_' . $label)
                ->setValue(time())
                ->save();
        return true;
    }

    public function getBulkLastTime($type)
    {
        if (Mage::getStoreConfig('retailexpress/main/sync_new')) {
            $v = Mage::getModel('retailexpress/conf')->load('bulklt_' . $type)->getValue();
            if ($v) {
                return date('c', $v);
            }
        }

        return '1900-01-01T00:00:00';
    }

    public function setBulkLastTime($type, $v)
    {
        Mage::getModel('retailexpress/conf')->load('bulklt_' . $type)
            ->setConfId('bulklt_' . $type)
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
        if ($order->getCustomer() && $order->getCustomer()->getId()) {
            $rex_data['ExternalCustomerId'] = $order->getCustomer()->getId();
            $rex_id = Mage::getModel('retailexpress/conf')->load('customer_' . $order->getCustomer()->getId())->getValue();
            if ($order->getCustomer()->getPassword()) {
                $rex_data['Password'] = $order->getCustomer()->getPassword();
            }
        } else {
            $rex_data['ExternalCustomerId'] = "";
            $rex_data['Password'] = "";
        }

        $rex_data['BillEmail'] = $order->getData('customer_email');
        $rex_data['BillFirstName'] = $order->getData('customer_firstname');
        $rex_data['BillLastName'] = $order->getData('customer_lastname');
        $rex_data['ReceivesNews'] = 0;
        if ($order->getData('tax_percent')) {
            $rex_data['TaxRateApplied'] = $order->getData('tax_percent') / 100;
        } else {
            $rex_data['TaxRateApplied'] = "0";
        }

        if ($order->getBillingAddress()) {
            $streets = $order->getBillingAddress()->getStreet();
            $rex_data['BillAddress'] = isset($streets[0])?$streets[0]:'';
            $rex_data['BillAddress2'] = isset($streets[1])?$streets[1]:'';
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

        $order_info = "Customer\nEmail: " . $rex_data['BillEmail'] . "\n Billing Name: " . $rex_data['BillFirstName']. " " . $rex_data['BillLastName'] . "\n";
        $order_info .= "Billing Country: " . $rex_data['BillCountry'] . "\nBilling State: " . $rex_data['BillState'] . "\nBilling Postcode: " . $rex_data['BillPostCode'] . "\nBilling Address: " . $rex_data['BillAddress'] . "\n";
        $order_info .= "Billing Phone: " . $rex_data['BillPhone'] . "\n";
        if ($order->getShippingAddress()) {
            $streets = $order->getShippingAddress()->getStreet();
            $rex_data['DelAddress'] = isset($streets[0])?$streets[0]:'';
            $rex_data['DelAddress2'] = isset($streets[1])?$streets[1]:'';
            $rex_data['DelName'] = $order->getShippingAddress()->getData('firstname') . " " . $order->getShippingAddress()->getData('lastname');
//            $rex_data['DelAddress'] = join(' ', $order->getShippingAddress()->getStreet());
            $rex_data['DelCompany'] = $order->getShippingAddress()->getCompany();
            $rex_data['DelPhone'] = $order->getShippingAddress()->getData('telephone');
            $rex_data['DelPostCode'] = $order->getShippingAddress()->getData('postcode');
            $rex_data['DelSuburb'] = $order->getShippingAddress()->getData('city');
            $rex_data['DelState'] = $order->getShippingAddress()->getData('region');
            $rex_data['DelCountry'] = Mage::getModel('directory/country')->loadByCode($order->getShippingAddress()->getData('country_id'))->getName();
        }

        $order_info .= "Delivery Name: " . $rex_data['DelName'] . "\n";
        $order_info .= "Delivery Country: " . $rex_data['DelCountry'] . "\nDelivery State: " . $rex_data['DelState'] . "\nDelivery Postcode: " . $rex_data['DelPostCode'] . "\nDelivery Address: " . $rex_data['DelAddress'] . "\n";
        $order_info .= "Delivery Phone: " . $rex_data['DelPhone'] . "\n\n";
        if ($rex_id) {
            $rex_data['CustomerId'] = $rex_id;
        } else {
            $rex_data['CustomerId'] = "";
        }

        $rex_data['OrderStatus'] = 'Quote';
        $rex_data['OrderTotal'] = $order->getData('grand_total');
        $rex_data['FreightTotal'] = $order->getData('shipping_incl_tax');
        $rex_data['PublicComments'] = "Delivery by " . $order->getShippingDescription() . "\n";
        if ($order->getGiftMessageId()) {
            $gift = Mage::helper('giftmessage/message')->getGiftMessage($order->getGiftMessageId());
            $rex_data['PublicComments'] .= "Order giftmessage from: " . $gift->getSender() . "\n";
            $rex_data['PublicComments'] .= "Order giftmessage to: " . $gift->getRecipient() . "\n";
            $rex_data['PublicComments'] .= "Order giftmessage message: " . $gift->getMessage() . "\n";
        }

        $rex_data['OrderItems'] = array('OrderItem' => array());
        $rex_data['OrderPayments'] = array(
            'OrderPayment' => array()
        );
        $rex_payment_id = '';
        if ($order->getPayment()) {
            $__t = Mage::getStoreConfig('retailexpress/payments/' . $order->getPayment()->getMethod());
            $rex_payment_id = Mage::getModel('retailexpress/payment')->load($__t)->getRexId();
        }
        if ($rex_payment_id && $order->getTotalPaid()) {
            $rex_data['OrderPayments']['OrderPayment'][] = array(
                'MethodId' => $rex_payment_id,
                'Amount' => (float)$order->getTotalPaid(),
                'DateCreated' => date('c'),
                'VoucherCode' => $order->getCouponCode(),
            );
        }
        if ($order->getCouponCode()) {
            $rex_data['OrderPayments']['OrderPayment'][] = array(
                'MethodId' => 11,
                'Amount' => (float)abs($order->getDiscountAmount()),
                'DateCreated' => date('c'),
                'VoucherCode' => $order->getCouponCode(),
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
            $order_info .= $product->getName() . " (#" . $product->getId() . ", SKU: " . $product->getSku() . "), qty: " . $i->getData('qty_ordered') . "\n";
            if (!$product->getRexProductId()) {
                continue;
            }

            $tax = "0";
            if ($i->getData('tax_percent')) {
                $tax = $i->getData('tax_percent') / 100;
            }

            if (count($i->getChildrenItems())) {
                foreach ($i->getChildrenItems() as $_i) {
                    $product = Mage::getModel('catalog/product')->load($_i->getProductId());
                    if ($i->getGiftMessageId()) {
                        $gift = Mage::helper('giftmessage/message')->getGiftMessage($i->getGiftMessageId());
                        $rex_data['PublicComments'] .= "Item '" . $product->getRexProductId() . "' giftmessage from: " . $gift->getSender() . "\n";
                        $rex_data['PublicComments'] .= "Item '" . $product->getRexProductId() . "' giftmessage to: " . $gift->getRecipient() . "\n";
                        $rex_data['PublicComments'] .= "Item '" . $product->getRexProductId() . "' giftmessage message: " . $gift->getMessage() . "\n";
                    }

                    $rex_data['OrderItems']['OrderItem'][] = array(
                        'ProductId' => $product->getRexProductId(),
                        'QtyOrdered' => (int)$i->getData('qty_ordered'),
                        'QtyFulfilled' => $i->getIsVirtual()?((int)$i->getData('qty_ordered')):0,
                        'UnitPrice' => $i->getPriceInclTax(),
                        'DeliveryDueDate' => '',
                        'DeliveryMethod' => $i->getIsVirtual()?'':'home',
                        'TaxRateApplied' => $tax,
                    );
                }
            } else {
                if ($i->getGiftMessageId()) {
                    $gift = Mage::helper('giftmessage/message')->getGiftMessage($i->getGiftMessageId());
                    $rex_data['PublicComments'] .= "Item '" . $product->getRexProductId() . "' giftmessage from: " . $gift->getSender() . "\n";
                    $rex_data['PublicComments'] .= "Item '" . $product->getRexProductId() . "' giftmessage to: " . $gift->getRecipient() . "\n";
                    $rex_data['PublicComments'] .= "Item '" . $product->getRexProductId() . "' giftmessage message: " . $gift->getMessage() . "\n";
                }

                $rex_data['OrderItems']['OrderItem'][] = array(
                    'ProductId' => $product->getRexProductId(),
                    'QtyOrdered' => (int)$i->getData('qty_ordered'),
                    'QtyFulfilled' => $i->getIsVirtual()?((int)$i->getData('qty_ordered')):0,
                    'UnitPrice' => $i->getPriceInclTax(),
                    'DeliveryDueDate' => '',
                    'DeliveryMethod' => $i->getIsVirtual()?'':'home',
                    'TaxRateApplied' => $tax,
                );
            }
        }

        if ($order->getCustomerNote()) {
            $rex_data['PublicComments'] .= 'Comment: ' . $order->getCustomerNote() . "\n";
        }

        $history = Mage::getModel('retailexpress/history');
        Mage::register("retail_history_id", $history->getId());
        $history->setData(
            array(
                  'type' => 'Online'
                , 'comment' => ''
            )
        );
        try {
            $rex_data = Mage::getSingleton('retailexpress/retail')->OrderCreate(array($rex_data));
            $history->setComment('Order for quote #' . $order->getQuoteId() . ' created in POS: ' . $rex_data['order_id']);
            Mage::register('current_order_rexid', $rex_data['order_id']);
            $order->setIncrementId($rex_data['order_id']);
            if (isset($rex_data['customer_id'])) {
                Mage::register('current_customer_rexid', $rex_data['customer_id']);
            }
        } catch (Exception $e) {
            if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
                $history->setComment('Error create order for quote #' . $order->getQuoteId() . ': ' . $e->getMessage());
                $history->save();
            }
            // send email
            try {
                $email = Mage::getStoreConfig('retailexpress/main/email_log');
                if ($email) {
                    $mail = new Zend_Mail();
                    $mail->setBodyText('Error create order for quote #' . $order->getQuoteId() . ': ' . $e->getMessage() . "\n\nOrder Info\n\n" . $order_info);
                    $mail->setFrom(Mage::getStoreConfig('trans_email/ident_general/name'), Mage::getStoreConfig('trans_email/ident_general/email'));
                    $mail->addTo($email, '');
                    $mail->setSubject("POS Order create error");
                    $mail->send();
                }
            } catch (Exception $ea) {

            }

            throw $e;
        }
        if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
            $history->save();
        }
    }

    public function updateCustomerInfo($mag_id)
    {
        $customer_id = Mage::getModel('retailexpress/conf')->load('customer_' . $mag_id)->getValue();
        if (!$mag_id || !$customer_id) {
            return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online'
                , 'comment' => ''
            )
        );
        try {
            $customer_data = Mage::getSingleton('retailexpress/retail')->CustomerGetDetails($customer_id);
            $_t = $this->updateMagentoCustomer($customer_data);
            $history->setComment($_t['str']);
        } catch (Exception $e) {
            $history->setComment($e->getMessage());
        }

        if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
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
            $main = array();
            $billing = array();
            $shipping = array();
            foreach ($c as $k => $v) {
                $_t = explode('_', $k, 2);
                if (count($_t) > 1) {
                    if ('b' == $_t[0]) {
                        $billing[$_t[1]] = $v;
                    } else if ('s' == $_t[0]) {
                        $shipping[$_t[1]] = $v;
                    }
                } else {
                    if ("subscription" == $k) {
                        $v = ($v == '0')?false:true;
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
                $billing['street'][] = isset($billing['address'])?$billing['address']:'';
                $billing['street'][] = isset($billing['address2'])?$billing['address2']:'';
            }

            if (isset($shipping['address']) || isset($shipping['address2'])) {
                $shipping['street'] = array();
                $shipping['street'][] = isset($shipping['address'])?$shipping['address']:'';
                $shipping['street'][] = isset($shipping['address2'])?$shipping['address2']:'';
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

            $isNewCustomer = false;
            $isChangePassword = false;
            if ($customer->getId()) {
                if (isset($main['password'])) {
                    if (!Mage::helper('core')->validateHash($main['password'], $customer->getPasswordHash())) {
                        // unset password to did not sync
                        $isChangePassword = true;
                    } else {
                        unset($main['password']);
                    }
                }
            } else {
                $isNewCustomer = true;
            }

            if (isset($c['rex_group_id'])) {
                $group_id = Mage::getModel('retailexpress/conf')->getCollection()
                        ->addFieldToFilter('conf_id', array('like' => 'group_%'))
                        ->addFieldToFilter('value', $c['rex_group_id'])
                        ->getFirstItem()
                        ->getConfId();
                if ($group_id) {
                    $_t = explode('_', $group_id, 2);
                    $group_id = $_t[1];
                }
                if (!$group_id) {
                    $model = Mage::getModel('customer/group')
                                    ->setCode(isset($c['rex_group_name'])?$c['rex_group_name']:('POS Group Id ' . $c['rex_group_id']))
                                    ->setTaxClassId(Mage::getModel('customer/group')->load('1')->getTaxClassId())
                                    ->save();
                    $group_id = $model->getId();
                    Mage::getModel('retailexpress/conf')
                        ->setConfId('group_' . $group_id)
                        ->setValue($c['rex_group_id'])
                        ->save();
                } else {
                    Mage::getModel('customer/group')->load($group_id)
                            ->setCode(isset($c['rex_group_name'])?$c['rex_group_name']:('POS Group Id ' . $c['rex_group_id']))
                            ->save();
                }
                $main['group_id'] = $group_id;
            }

            $customer->setData('website_id', $this->getWebsiteId())->addData($main)->save();
            $storeId = $customer->getSendemailStoreId();
            if ($isNewCustomer) {
                $customer->sendNewAccountEmail('registered', '', $storeId);
            }

            if ($isChangePassword) {
                $customer->sendPasswordReminderEmail();
            }

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

            Mage::getModel('retailexpress/conf')->load('customer_' . $customer->getId())
                ->setConfId('customer_' . $customer->getId())
                ->setValue($c['rex_id'])
                ->save();
            self::$_address_sync = false;
            return array('str' => "POS Customers ID ". $c['rex_id'] . " synchronised (" . $customer->getId() . ")\n" , 'new' => $isNewCustomer);
        } catch (Exception $e) {
            self::$_address_sync = false;
            throw new Exception("POS Customers ID ". $c['rex_id'] . " error: " . $e->getMessage() . "\n");
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
                  'type' => 'Online'
                , 'comment' => ''
            )
        );
        self::$_address_sync = true;
        try {
            if (Mage::registry('current_customer_rexid')) {
                Mage::getModel('retailexpress/conf')->load('customer_' . $customer->getId())
                    ->setConfId('customer_' . $customer->getId())
                    ->setValue(Mage::registry('current_customer_rexid'))
                    ->save();
                self::$_address_sync = false;
                return;
                Mage::unregister('current_customer_rexid');
            }

            $rex_id = Mage::getModel('retailexpress/conf')->load('customer_' . $customer->getId())->getValue();
            $rex_data = array();
            $rex_data['ExternalCustomerId'] = $customer->getId();
            if ($customer->getPassword()) {
//                echo "<pre>"; var_dump($customer->getPassword()); exit;
                $rex_data['Password'] = $customer->getPassword();
            }

            $rex_data['BillEmail'] = $customer->getEmail();
            $rex_data['BillFirstName'] = $customer->getData('firstname');
            $rex_data['BillLastName'] = $customer->getData('lastname');
            $rex_data['ReceivesNews'] = (int)$customer->getIsSubscribed();
            if ($customer->getDefaultBillingAddress()) {
                $streets = $customer->getDefaultBillingAddress()->getStreet();
                $rex_data['BillAddress'] = isset($streets[0])?$streets[0]:'';
                $rex_data['BillAddress2'] = isset($streets[1])?$streets[1]:'';
                $rex_data['BillCompany'] = $customer->getDefaultBillingAddress()->getCompany();
                $rex_data['BillPhone'] = $customer->getDefaultBillingAddress()->getData('telephone');
                $rex_data['BillPostCode'] = $customer->getDefaultBillingAddress()->getData('postcode');
                $rex_data['BillSuburb'] = $customer->getDefaultBillingAddress()->getData('city');
                $rex_data['BillState'] = $customer->getDefaultBillingAddress()->getData('region');
                $rex_data['BillCountry'] = Mage::getModel('directory/country')->loadByCode($customer->getDefaultBillingAddress()->getData('country_id'))->getName();
            }

            if ($customer->getDefaultShippingAddress()) {
                $streets = $customer->getDefaultShippingAddress()->getStreet();
                $rex_data['DelName'] = $customer->getDefaultShippingAddress()->getData('firstname') . " " . $customer->getDefaultShippingAddress()->getData('lastname');
                $rex_data['DelAddress'] = isset($streets[0])?$streets[0]:'';
                $rex_data['DelAddress2'] = isset($streets[1])?$streets[1]:'';
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
            Mage::getModel('retailexpress/conf')->load('customer_' . $customer->getId())
                    ->setConfId('customer_' . $customer->getId())
                    ->setValue($rex_id)
                    ->save();
            $history->setComment("Magento Customers ID ". $customer->getId() . " synchronised to POS (" . $rex_id . ")\n");
        } catch (Exception $e) {
            $history->setComment("Magento Customers ID ". $customer->getId() . " error synchronised to POS: " . $e->getMessage() . "\n");
        }

        self::$_address_sync = false;
        if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
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

        $rex_id = Mage::getModel('retailexpress/conf')->load('customer_' . $customer->getId())->getValue();
        if (!$rex_id) {
            return $return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online'
                , 'comment' => ''
            )
        );
        try {
            $return = Mage::getSingleton('retailexpress/retail')->OrdersGetHistory($rex_id);
            $history->setComment("Magento Customers ID ". $customer->getId() . " synchronised history orders from POS\n");
        } catch (Exception $e) {
            $history->setComment("Magento Customers ID ". $customer->getId() . " error in history orders from POS: " . $e->getMessage() . "\n");
        }

        if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
            $history->save();
        }
        return $return;
    }

    public function getOrderHistory($order_id)
    {
        $return = new Varien_Object;
        $customer = Mage::registry('current_customer');
        if (!$customer || !$customer->getId()) {
            return $return;
        }

        $rex_id = Mage::getModel('retailexpress/conf')->load('customer_' . $customer->getId())->getValue();
        if (!$rex_id) {
            return $return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online'
                , 'comment' => ''
            )
        );
        try {
            $return->addData(Mage::getSingleton('retailexpress/retail')->OrdersGetHistory($rex_id, $order_id));
            $history->setComment("Magento Customers ID ". $customer->getId() . " synchronised history orders from POS\n");
        } catch (Exception $e) {
            $history->setComment("Magento Customers ID ". $customer->getId() . " error in history orders from POS: " . $e->getMessage() . "\n");
        }

        if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
            $history->save();
        }
        return $return;
    }

    public function cancelOrder($order)
    {
        $this->_saveRexOrderId($order);
        $rex_id = Mage::getModel('retailexpress/conf')->load('order_' . $order->getId())->getValue();
        if (!$rex_id) {
            return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online'
                , 'comment' => ''
            )
        );
        try {
            $return = Mage::getSingleton('retailexpress/retail')->orderCancel($rex_id);
            $history->setComment("Magento Order ID ". $order->getId() . " canceled in POS result : " . $return);
        } catch (Exception $e) {
            $history->setComment("Magento Order ID ". $order->getId() . " error during cancel in POS: " . $e->getMessage() . "\n");
        }
        if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
            $history->save();
        }
    }

    protected function _saveRexOrderId($order)
    {
        if (Mage::registry('current_order_rexid')) {
            $rex_id = Mage::registry('current_order_rexid');
            Mage::unregister('current_order_rexid');
            $this->_setConfigValue('order_' . $order->getId(), $rex_id);
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
        $rex_id = $this->_getConfigValue('order_' . $order->getId());
        if (!$rex_id && Mage::registry('current_order_rexid')) {
            $rex_id = Mage::registry('current_order_rexid');
        }

        if (!$rex_id) {
            return;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Online'
                , 'comment' => ''
            )
        );
        $rex_payment_id = '';
        $method = '';
        if ($order->getPayment()) {
            $__t = Mage::getStoreConfig('retailexpress/payments/' . $order->getPayment()->getMethod());
            $rex_payment_id = Mage::getModel('retailexpress/payment')->load($__t)->getRexId();
            $method = $order->getPayment()->getMethod();
        }

        try {
            if (!$rex_payment_id) {
                throw new Exception('Rex payment not setuped for ' . $method);
            }

            $rex_data = array(
                'OrderId'  => $rex_id,
                'MethodId' => $rex_payment_id,
                'Amount' => (float)abs($invoice->getGrandTotal()),
                'DateCreated' => date('c'),
                'VoucherCode' => $order->getCouponCode(),
            );
            $return = Mage::getSingleton('retailexpress/retail')->OrderAddPayment(array($rex_data));
            $history->setComment("Magento Order ID ". $order->getId() . " added payment to POS with result: " . $return);
        } catch (Exception $e) {
            $history->setComment("Magento Order ID ". $order->getId() . " added payment error: " . $e->getMessage() . "\n");
        }

        if (POS_System_Model_Config::DEBUG_IN_HISTORY) {
            $history->save();
        }
    }

    /**
     * Setting internal config value
     *
     * @param $name string - name of config
     * @param $value string - value of config
     * @return void
     */
    protected function _setConfigValue($name, $value)
    {
        Mage::getModel('retailexpress/conf')->load($name)
            ->setConfId($name)
            ->setValue($value)
            ->save();
    }

    /**
     * Getting internal config value
     *
     * @param string $name - name of internal config
     * @return string|null - value of the config
     */
    protected function _getConfigValue($name)
    {
        return Mage::getModel('retailexpress/conf')->load($name)->getValue();
    }

    /**
     * get status of product bulk method
     *
     * @return int status ot product bulk sync
     */
    public function getProductBulkSynchronizationStatus()
    {
        return Mage::getModel('retailexpress/import_product')->getStatus();
    }

    /**
     * get status of customer bulk method
     *
     * @return int status ot customer bulk sync
     */
    public function getCustomerBulkSynchronizationStatus()
    {
        return Mage::getModel('retailexpress/import_customer')->getStatus();
    }

    /**
     * run all synchronization
     *
     * @return void
     */
    public function synchronizeBulk()
    {
	    $this->_cleanUpCategories();
        $start_time = time();
        Mage::getModel('retailexpress/import_product')->import();
        $end_time = time();
        if (($end_time - $start_time) < POS_System_Model_Config::CONFIG_BULK_TIME) {
            Mage::getModel('retailexpress/import_customer')->setStartTime($start_time)->import();
        }
    }

    /**
     * This function clean up the broken categories before bulk import
     *
     * @return void
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
     * make command to sync
     *
     * @return void
     */
    public function waitBulk()
    {
        Mage::getModel('retailexpress/import_product')->wait(mt_rand(0, POS_System_Model_Config::MAX_WAIT_ITERATIONS));
        Mage::getModel('retailexpress/import_customer')->wait(mt_rand(0, POS_System_Model_Config::MAX_WAIT_ITERATIONS));
        $result = $this->synhOrdersBulk();
        $comment = '';
        foreach ($result as $k => $v) {
            $comment .= $k . ":\n" . $v . "\n\n";
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Cron'
                , 'comment' => $comment
            )
        );
        $history->save();
    }

    /**
     * process each step of cron iteration
     * @return void
     */
    protected function _processProductBulk()
    {
        if ($this->_getConfigValue('pb_pid') && function_exists("posix_getsid") && (posix_getsid($this->_getConfigValue('pb_pid')) !== false)) {
            return;
        }

        $this->_setConfigValue('pb_pid', getmypid());
        $finished = $this->synhProductsBulk();
        $this->_setConfigValue('pb_pid', "");
        if ($finished) {
            $this->_finishProductBulk();
        }
    }

}
