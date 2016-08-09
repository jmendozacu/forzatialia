<?php

class POS_ClickAndCollect_Model_Carrier_Clickandcollect
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_helper;

    const STORE_DELIVERY_RULE_ALLOW = 1;
    const STORE_DELIVERY_RULE_DISALLOW = 0;

    // default value for store_pickup_rule
    const STORE_PICKUP_RULE_DEFAULT_VALUE = 2;

    const STORE_PICKUP_RULE_GROUP_1 = 1;
    const STORE_PICKUP_RULE_GROUP_1_OR_2 = 2;
    const STORE_PICKUP_RULE_NO_PICKUP = 3;

    const XML_PATH_OUTLET_GROUP_1 = 'carriers/clickandcollect/pickup_group_1';
    const XML_PATH_OUTLET_GROUP_2 = 'carriers/clickandcollect/pickup_group_2';

    // will store product store_pickup_rule attribute here
    protected $_storePickupRuleCache = [];
    protected $_code = 'clickandcollect';

    public function __construct()
    {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('clickandcollect');
        }
        parent::__construct();
    }

    /**
     * collect shipping rates based on request.
     *
     * @param Mage_Shipping_Model_Rate_Request
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        //methodOptionsSet this is to set if the method options are already sent or not
        $methodOptionsSet = false;

        if (!$this->_helper->isMethodActive()) {
            return false;
        }

        // sort products in cart
        if (!$request->getAllItems()) {
            return false;
        }

        $productIds = [];
        $parentIds = [];
        $childs = [];
        foreach ($request->getAllItems() as $item) {
            if ($item->getParentItem()) {
                $childs[$item->getParentItem()->getProductId()][] = $item->getProductId();
                $parentIds[] = $item->getParentItem()->getProductId();
            } else {
                $productIds[] = $item->getProductId();
            }
        }

        $parentIds = array_unique($parentIds);
        $productIds = array_unique($productIds);
        $productIds = array_diff($productIds, $parentIds);

        // collect all products pickup rules into array
        $storePickupRules = [];
        $storeDeliveryRules = [];

        $deliveryRuleEnabled = true;

        foreach ($childs as $parentId => $childIds) {
            foreach ($childIds as $childId) {
                if ($this->_getStorePickupRule($childId)) {
                    $storePickupRules[] = $this->_getStorePickupRule($childId);
                } elseif ($this->_getStorePickupRule($parentId)) {
                    $storePickupRules[] = $this->_getStorePickupRule($parentId);
                } else {
                    $storePickupRules[] = self::STORE_PICKUP_RULE_DEFAULT_VALUE;
                }

                if ($this->_getStoreDeliveryRule($childId) == self::STORE_DELIVERY_RULE_DISALLOW) {
                    $storeDeliveryRules[] = $this->_getStoreDeliveryRule($childId);
                    $deliveryRuleEnabled = false;
                } elseif ($this->_getStoreDeliveryRule($parentId) == self::STORE_DELIVERY_RULE_DISALLOW) {
                    $storeDeliveryRules[] = $this->_getStoreDeliveryRule($parentId);
                    $deliveryRuleEnabled = false;
                }
            }
        }

        foreach ($productIds as $productId) {
            if ($this->_getStorePickupRule($productId)) {
                $storePickupRules[] = $this->_getStorePickupRule($productId);
            } else {
                $storePickupRules[] = self::STORE_PICKUP_RULE_DEFAULT_VALUE;
            }

            if ($this->_getStoreDeliveryRule($productId) == '') {
                $storeDeliveryRules[] = $deliveryRuleEnabled = false;
            } elseif ($this->_getStoreDeliveryRule($productId) === self::STORE_DELIVERY_RULE_DISALLOW) {
                $storeDeliveryRules[] = $this->_getStoreDeliveryRule($productId);
                $deliveryRuleEnabled = false;
            }
        }

        $storePickupRules = array_unique($storePickupRules);

        Mage::register('deliveryrule', $deliveryRuleEnabled, true);

        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        // if at least one product has 'no_pickup' option return no quotes
        if (in_array(self::STORE_PICKUP_RULE_NO_PICKUP, $storePickupRules)) {
            return $result;
        }

        // if at least one product has 'pickup_group_1' option return group 1 quotes
        if (in_array(self::STORE_PICKUP_RULE_GROUP_1, $storePickupRules)) {
            $methodOptions = $this->getMethodOptionsGroup1();

            //if one product has pickup group 1, all return methods will be group 1 only
            $methodOptionsSet = true;

        // return group 2 quotes
        } else {
            //if $methodoptions has not been set above
            if (!$methodOptionsSet) {
                $methodOptions = $this->getMethodOptionsGroup2();
            }
        }

        // if no methods return no quotes
        if (empty($methodOptions)) {
            return $result;
        }

        $shippingPrice = $this->_getCarrierPrice();

        if ($shippingPrice !== false) {
            // add shpping rates
            foreach ($methodOptions as $option) {
                $method = Mage::getModel('shipping/rate_result_method');

                $method->setCarrier('clickandcollect');
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod($option['code']);
                $method->setMethodTitle($option['title']);
                $method->setMethodDescription($option['description']);

                $method->setPrice($shippingPrice);
                $method->setCost($shippingPrice);

                $result->append($method);
            }
        }

        return $result;
    }

    /**
     * return Product Store Pickup Rule Value.
     *
     * @return self::STORE_PICKUP_RULE_GROUP_1|self::STORE_PICKUP_RULE_GROUP_1_OR_2|self::STORE_PICKUP_RULE_NO_PICKUP
     */
    protected function _getStorePickupRule($productId)
    {
        if (isset($this->_storePickupRuleCache[$productId])) {
            return $this->_storePickupRuleCache[$productId];
        }
        $_product = Mage::getModel('catalog/product')->load($productId);
        if ($_product->getStorePickupRule() == '' || $_product->getStorePickupRule() == self::STORE_PICKUP_RULE_NO_PICKUP) {
            $this->_storePickupRuleCache[$productId] = self::STORE_PICKUP_RULE_NO_PICKUP;
        } elseif ($_product->getStorePickupRule() == 2) {
            $this->_storePickupRuleCache[$productId] = self::STORE_PICKUP_RULE_GROUP_1_OR_2;
        } elseif ($_product->getStorePickupRule() == 1) {
            $this->_storePickupRuleCache[$productId] = self::STORE_PICKUP_RULE_GROUP_1;
        } else {
            $this->_storePickupRuleCache[$productId] = false;
        }

        return $this->_storePickupRuleCache[$productId];
    }

    /**
     * return Product Store Delivery Rule Value.
     *
     * @return int
     */
    protected function _getStoreDeliveryRule($productId)
    {
        $_product = Mage::getModel('catalog/product')->load($productId);
        if ($_product->getStoreDeliveryRule() == '') {
            return true;
        } elseif ($_product->getStoreDeliveryRule() == '1') {
            return true;
        } else {
            return false;
        }
    }

    protected function _getCarrierPrice()
    {
        $shippingPrice = false;
        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        return $shippingPrice;
    }

    /**
     * return Carrier Data.
     *
     * @return array
     */
    public function getShippingData()
    {
        $data = [
            'title' => $this->getConfigData('title'),
            'price' => $this->_getCarrierPrice(),
            'code' => $this->_code,
        ];

        return $data;
    }

    /**
     * retreive Outlets of Group 1.
     *
     * @return array
     */
    public function getMethodOptionsGroup1()
    {
        $outletGroup = Mage::getStoreConfig(self::XML_PATH_OUTLET_GROUP_1);
        $outletGroup = explode(',', $outletGroup);
        $outletCollection = Mage::getModel('retailexpress/outlet')->getCollection()->addFieldToFilter('fulfilment_outlet_id', ['in' => $outletGroup]);
        $methodOptions = [];
        foreach ($outletCollection as $outlet) {
            $address = $outlet->getAddress_1();
            $address = $outlet->getAddress_2() ? $address.'<br />'.$outlet->getAddress_2() : $address;
            $address = $outlet->getAddress_3() ? $address.'<br />'.$outlet->getAddress_3() : $address;
            $methodOptions[] = [
                'code' => 'group_1_outlet_'.$outlet->getFulfilmentOutletId(),
                'title' => $outlet->getOutletName(),
                'description' => $outlet->getOutletName().', '.$address,
            ];
        }

        return $methodOptions;
    }

    /**
     * retreive Outlets of Group 1 and 2.
     *
     * @return array
     */
    public function getMethodOptionsGroup2()
    {
        $outletGroup1 = Mage::getStoreConfig(self::XML_PATH_OUTLET_GROUP_1);
        $outletGroup1 = explode(',', $outletGroup1);
        $outletGroup2 = Mage::getStoreConfig(self::XML_PATH_OUTLET_GROUP_2);
        $outletGroup2 = explode(',', $outletGroup2);
        $outletGroup = array_merge($outletGroup1, $outletGroup2);
        $outletGroup = array_unique($outletGroup);
        $outletCollection = Mage::getModel('retailexpress/outlet')->getCollection()->addFieldToFilter('fulfilment_outlet_id', ['in' => $outletGroup]);
        $methodOptions = [];
        foreach ($outletCollection as $outlet) {
            $address = $outlet->getAddress_1();
            $address = $outlet->getAddress_2() ? $address.'<br />'.$outlet->getAddress_2() : $address;
            $address = $outlet->getAddress_3() ? $address.'<br />'.$outlet->getAddress_3() : $address;
            $methodOptions[] = [
                'code' => 'group_2_outlet_'.$outlet->getFulfilmentOutletId(),
                'title' => $outlet->getOutletName(),
                'description' => $outlet->getOutletName().', '.$address,
            ];
        }

        return $methodOptions;
    }

    /**
     * abstract method.
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['clickandcollect' => $this->getConfigData('name')];
    }
}
