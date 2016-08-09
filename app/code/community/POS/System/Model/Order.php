<?php

class POS_System_Model_Order extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('retailexpress/order');
    }

    public function createNewOrderFromRex($oldOrder, $newOrder)
    {
        try {
            $websiteId = Mage::getModel('core/website')->getCollection()->getFirstItem()->getId();

            $quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId());

            if ($newOrder['customer_email']) {
                $customer = Mage::getModel('customer/customer')->setData('website_id', $websiteId)->loadByEmail($newOrder['customer_email']);

                if (!$customer) {
                    $customer = Mage::getModel('customer/customer')->load($oldOrder->getCustomerId());
                }

                Mage::log('loaded via email '.$newOrder['customer_email']);
            } else {
                $customer = Mage::getModel('customer/customer')->load($oldOrder->getCustomerId());
            }

            $customerAddress = $customer->getDefaultBillingAddress();

            $quote->assignCustomer($customer);

            //$customerAddress = $quote->getBillingAddress();
            //$customerAddress = $customer->getDefaultBillingAddress();

            $processedProducts = [];
            //add products
            foreach ($newOrder['products'] as $retailId) {
                $product = Mage::getModel('catalog/product')->load(Mage::getModel('catalog/product')
                            ->getCollection()
                            ->addAttributeToFilter('rex_product_id', $retailId)
                            ->getFirstItem()->getId());

                $buyInfo = [
                        'qty' => $newOrder['items'][$retailId],
                    ];

                if (!in_array($retailId, $processedProducts)) {
                    $quote->addProduct($product, new Varien_Object($buyInfo));
                    array_push($processedProducts, $retailId);
                }
            }

            $addressData = [];

            if (($customerAddress) || (is_object($customerAddress))) {
                $addressData = [
                        'customer_address_id' => $customerAddress->getId(),
                        'prefix' => $customerAddress->getPrefix(),
                        'firstname' => $customerAddress->getFirstname(),
                        'middlename' => $customerAddress->getMiddlename(),
                        'lastname' => $customerAddress->getLastname(),
                        'suffix' => $customerAddress->getPrefix(),
                        'company' => $customerAddress->getCompany(),
                        'street' => $customerAddress->getStreet(),
                        'city' => $customerAddress->getCity(),
                        'country_id' => $customerAddress->getCountryId(),
                        'region' => $customerAddress->getRegion(),
                        'region_id' => $customerAddress->getRegionId(),
                        'postcode' => $customerAddress->getPostcode(),
                        'telephone' => $customerAddress->getTelephone(),
                        'fax' => $customerAddress->getFax(),
                ];
            }

            $billingAddressData = [];
            if (is_array($newOrder['billing_address'])) {
                $regionId = Mage::getModel('directory/region')->loadByName($newOrder['billing_address']['state'], $oldOrder->getBillingAddress()->getCountryId())->getId();
                if (!$regionId) {
                    $regionId = $oldOrder->getBillingAddress()->getRegionId();
                }

                $billingAddressData = [
                    'prefix' => $customer->getPrefix(),
                    'firstname' => $customer->getFirstname(),
                    'middlename' => $customer->getMiddlename(),
                    'lastname' => $customer->getLastname(),
                    'suffix' => $customer->getSuffix(),
                    'company' => $newOrder['billing_address']['company'],
                    'street' => $newOrder['billing_address']['address'],
                    'city' => $newOrder['billing_address']['city'],
                    'country_id' => $oldOrder->getBillingAddress()->getCountryId(),
                    'region' => $newOrder['billing_address']['state'],
                    'region_id' => $regionId,
                    'postcode' => $newOrder['billing_address']['postcode'],
                    'telephone' => $newOrder['billing_address']['phone'],
                    'fax' => '',
                ];
            }

            $shippingAddressData = [];
            if (is_array($newOrder['shipping_address'])) {
                $regionId = Mage::getModel('directory/region')->loadByName($newOrder['shipping_address']['state'], $oldOrder->getShippingAddress()->getCountryId())->getId();
                if (!$regionId) {
                    $regionId = $oldOrder->getShippingAddress()->getRegionId();
                }

                $shippingAddressData = [
                    'prefix' => $customer->getPrefix(),
                    'firstname' => $customer->getFirstname(),
                    'middlename' => $customer->getMiddlename(),
                    'lastname' => $customer->getLastname(),
                    'suffix' => $customer->getSuffix(),
                    'company' => $newOrder['shipping_address']['company'],
                    'street' => $newOrder['shipping_address']['address'],
                    'city' => $newOrder['shipping_address']['city'],
                    'country_id' => $oldOrder->getShippingAddress()->getCountryId(),
                    'region' => $newOrder['shipping_address']['state'],
                    'region_id' => $regionId,
                    'postcode' => $newOrder['shipping_address']['postcode'],
                    'telephone' => $newOrder['shipping_address']['phone'],
                    'fax' => '',
                ];
            }

            //Mage::log('addressss . '.print_r($addressData, true));
            if (!empty($billingAddressData)) {
                $billingAddress = $quote->getBillingAddress()->addData($billingAddressData);
            } else {
                $billingAddress = $quote->getBillingAddress()->addData($addressData);
            }

            if (!empty($shippingAddressData)) {
                $shippingAddress = $quote->getShippingAddress()->addData($shippingAddressData);
            } else {
                $shippingAddress = $quote->getShippingAddress()->addData($addressData);
            }

            if (empty($addressData) && empty($billingAddressData) && empty($shippingAddressData)) {
                Mage::log('Error: selected customer has no billing/shipping address');
                throw new Exception('selected customer has no billing/shipping address');

                return false;
            }

            /*
            $shippingAddress->setCollectShippingRates(true)->collectShippingRates()
                    ->setShippingMethod('clickandcollect_group_2_outlet_37')
                    ->setPaymentMethod('directdeposit_au');
            */

            //Mage::log(print_r($customerAddress->debug(), true), null, 'adressss.log');

            $shippingAddress->setCollectShippingRates(true)->collectShippingRates()
                    ->setCollectShippingRates(true)->collectShippingRates()
                    ->setShippingMethod($oldOrder->getShippingMethod())
                    ->setPaymentMethod($oldOrder->getPayment()->getMethod());

            /* [adamw] Free shipping would look like this:

            $shippingAddress->setFreeShipping( true )
                    ->setCollectShippingRates(true)->collectShippingRates()
                    ->setShippingMethod('freeshipping_freeshipping')
                    ->setPaymentMethod('checkmo');

            */

            $quote->getPayment()->importData(
                                    ['method' => $oldOrder->getPayment()->getMethod()]
                                    );

            $quote->collectTotals()->save();

            $service = Mage::getModel('sales/service_quote', $quote);

            $service->submitAll();

            $newCreatedOrder = $service->getOrder();
            //$newOrder['status'] = 'Processed';

            $this->_checkPaymentTrans($newCreatedOrder->getIncrementId(), $newOrder);

            $resource = Mage::getSingleton('core/resource');
            $read = $resource->getConnection('read');
            $write = $resource->getConnection('write');

            $tPrefix = (string) Mage::getConfig()->getTablePrefix();
            $salesFlatOrderTable = $tPrefix.'sales_flat_order';
            $salesFlatQuoteTable = $tPrefix.'sales_flat_quote';

            $oldIncrementId = $oldOrder->getData('increment_id');
            $oldEntityId = $oldOrder->getData('entity_id');

            $newIncrementId = $newCreatedOrder->getData('increment_id');
            $newEntityId = $newCreatedOrder->getData('entity_id');

            //update new data
            $sql = "UPDATE {$salesFlatOrderTable} SET
	        			original_increment_id={$oldIncrementId},
	        			relation_parent_id={$oldEntityId},
	        			relation_parent_real_id={$oldIncrementId},
	        			edit_increment=1 WHERE increment_id='{$newIncrementId}'";
            try {
                $write->query($sql);
            } catch (Exception $e) {
                Mage::log('POS_System_Helper_Data:: Order Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
            }

            //update old data
            $sql = "UPDATE {$salesFlatOrderTable} SET
	        			relation_child_id={$newEntityId},
	        			relation_child_real_id={$newIncrementId},
	        			edit_increment=1 WHERE increment_id='{$oldIncrementId}'";
            try {
                $write->query($sql);
            } catch (Exception $e) {
                Mage::log('POS_System_Helper_Data:: Order Error Update. Error: '.$e->getMessage(), null, 'possystem.log');
            }

            return $newCreatedOrder->getIncrementId();
        } catch (Exception $e) {
            throw $e;
        }

        return false;
    }

    private function _checkPaymentTrans($orderId, $items)
    {
        try {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

            $savedQtys = [];

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

                $rex_id = Mage::helper('retailexpress')->getRetailProductId($product);

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
                Mage::log('POS Order shipments already made');
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
                    Mage::log('POS Order payment already made');
                }
            } else {
                Mage::log('NO Payment array');
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
                    Mage::log(Exception($e));
                }
                //$updated++;
                if ($is_invoice) {
                    Mage::log('POS Order payment made');
                }
                if ($is_ship) {
                    Mage::log('POS Order adding shipment ('.$shipment->getId().')');
                }

                if ($is_invoice && $is_ship) {
                    $order->setStatus('complete');
                    $order->addStatusHistoryComment('Complete')->setIsVisibleOnFront(true)->setIsCustomerNotified(true);
                    $order->save();
                }
            }
        } catch (Exception $e) {
            Mage::log('POS Order adding shipment error: '.$e->getMessage());
        }
    }

//
}
