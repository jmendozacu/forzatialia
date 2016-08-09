<?php

class POS_System_Model_Retail extends Mage_Core_Model_Abstract
{
    /** @var RetailSoapClient */
    protected static $soap = null;

    protected static $outlet = null;

    // keep products sync data here to prevent multiple soap requests on getProductStockPriceById
    protected static $stockPriceDataCache = [];

    protected static $error = null;

    protected $_channelId = null;

    protected $_deliveryMode = null;

    protected function _init($resourceModel)
    {
        parent::_init($resourceModel);
        $this->_initSoap();
    }

    protected function _getUrl()
    {
        $url = Mage::getStoreConfig('retailexpress/main/url');
        if (!$url) {
            $url = 'http://webservicestest.retailexpress.com.au/DOTNET/Admin/WebServices/WebStore/ServiceV2.asmx?WSDL';
        }

        return $url;
    }

    protected function _getOptions()
    {
        return [
            'soap_version' => SOAP_1_1,
            'exceptions' => true,
            'trace' => true,
        ];
    }

    protected function _initSoapZip()
    {
        $this->_channelId = Mage::helper('retailexpress')->getChannelId();
        try {
            self::$soap = new REX_RetailSoapClient($this->_getUrl(), $this->_getOptions());
            $this->_initSoapHeaders();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    protected function _initSoap()
    {
        $this->_channelId = Mage::helper('retailexpress')->getChannelId();
        try {
            self::$soap = new SoapClient($this->_getUrl(), $this->_getOptions());
            $this->_initSoapHeaders();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
    }

    protected function _initSoapHeaders()
    {
        $ns = 'http://retailexpress.com.au/';
        $clientId = Mage::getStoreConfig('retailexpress/main/client_id');
        $username = Mage::getStoreConfig('retailexpress/main/username');
        $password = Mage::getStoreConfig('retailexpress/main/password');
        $headerbody = [
            'ClientID' => $clientId,
            'UserName' => $username,
            'Password' => $password,
        ];
        $header = new SOAPHeader($ns, 'ClientHeader', $headerbody);
        self::$soap->__setSoapHeaders($header);
    }

    /**
     * __getlastSentRequest.
     *
     * Method that gets the last sent xml request from the soap client
     *
     * @return XML
     */
    protected function __getlastSentSoapRequest()
    {
        return self::$soap->__getLastRequest();
    }

    protected function setError($error)
    {
        self::$error = $error;
    }

    public function getError()
    {
        return self::$error;
    }

    /**
     * getProductStockPriceById
     * get price/stock product data on demand
     * returns false or array of data.
     *
     * @param int $id
     * @param int $pricegroup_id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getProductStockPriceById($id, $pricegroup_id)
    {
        $helper = Mage::helper('retailexpress');
        try {
            if (!isset(self::$stockPriceDataCache[(string) $pricegroup_id.'-'.(string) $id])) {
                $this->_initSoap();
                Mage::log("POS:: Retail.php > getProductStockPriceById > Params: id=$id, pricegroup_id=$pricegroup_id", null, 'possystem.log');
                if ($helper->getIsEtaEnabled()) {
                    $result = self::$soap->ProductGetDetailsStockPricingByChannel(['ProductId' => $id, 'CustomerId' => 0, 'PriceGroupId' => $pricegroup_id, 'ChannelId' => $this->_channelId]);
                    if (!is_object($result) || !$result->ProductGetDetailsStockPricingByChannelResult || !$result->ProductGetDetailsStockPricingByChannelResult->any) {
                        Mage::log('POS:: Retail.php > getProductStockPriceById > ProductGetDetailsStockPricingByChannel SOAP: '.print_r($result, true), null, 'possystem.log');
                        throw new Exception("Incorrect method 'ProductGetDetailsStockPricingByChannel' response");
                    }
                    //save logs
                    //get the last sent xml request
                    $logLastRequest = $this->__getlastSentSoapRequest();
                    $logModel = Mage::getModel('retailexpress/log');
                    $logModel->saveSyncLogs($result, $logLastRequest, 'PRODUCTGETDETAILSSTOCKPRICING', 'ONDEMAND');
                    //end save logs

                    $ProductXML = new SimpleXMLElement($result->ProductGetDetailsStockPricingByChannelResult->any);

                    if (!$ProductXML) {
                        throw new Exception("No XML in method 'ProductGetDetailsStockPricingByChannel' response");
                    }

                    if (!$ProductXML->Product || !$ProductXML->Product->ProductId) {
                        throw new Exception("No valid XML format in method 'ProductGetDetailsStockPricing' response");
                    }
                } else {
                    $result = self::$soap->ProductGetDetailsStockPricing(['ProductId' => $id, 'CustomerId' => 0, 'PriceGroupId' => $pricegroup_id]);
                    Mage::log('POS:: Retail.php > getProductStockPriceById > ProductGetDetailsStockPricing SOAP: '.print_r($result, true), null, 'possystem.log');
                    if (!is_object($result) || !$result->ProductGetDetailsStockPricingResult || !$result->ProductGetDetailsStockPricingResult->any) {
                        throw new Exception("Incorrect method 'ProductGetDetailsStockPricingResult' response");
                    }
                    //save logs
                    //get the last sent xml request
                    $logLastRequest = $this->__getlastSentSoapRequest();
                    $logModel = Mage::getModel('retailexpress/log');
                    $logModel->saveSyncLogs($result, $logLastRequest, 'PRODUCTGETDETAILSSTOCKPRICING', 'ONDEMAND');
                    //end save logs

                    $ProductXML = new SimpleXMLElement($result->ProductGetDetailsStockPricingResult->any);

                    if (!$ProductXML) {
                        throw new Exception("No XML in method 'ProductGetDetailsStockPricingResult' response");
                    }

                    if (!$ProductXML->Product || !$ProductXML->Product->ProductId) {
                        throw new Exception("No valid XML format in method 'ProductGetDetailsStockPricingResult' response");
                    }
                }

                $p = $ProductXML->Product;
                if (isset($ProductXML->Product[0])) {
                    foreach ($ProductXML->Product as $__p) {
                        if (is_object($__p)) {
                            self::$stockPriceDataCache[(string) $pricegroup_id.'-'.(string) $__p->ProductId] = $__p;
                            if (((string) $__p->ProductId) == $id) {
                                $p = $__p;
                            }
                        }
                    }
                }
            } else {
                $p = self::$stockPriceDataCache[(string) $pricegroup_id.'-'.(string) $id];
            }

            $price_field = Mage::getStoreConfig('retailexpress/price/regular');
            $special_field = Mage::getStoreConfig('retailexpress/price/special');
            $price = (float) $p->DefaultPrice;
            $special_price = '';
            if ($price_field && isset($p->$price_field)) {
                $price = (float) $p->$price_field;
            }

            if ($special_field && isset($p->$special_field)) {
                $special_price = (float) $p->$special_field;
            }

            if ('RRP' == $special_field) {
                $special_price = '';
            }

            if (('RRP' == $price_field) && (!$price)) {
                if (!$special_price) {
                    $special_price = (float) $p->CustomerDiscountedPrice;
                }

                $price = $special_price;
                $special_price = '';
            }

//            $_productMinQty = Mage::getModel('cataloginventory/stock_item')->load(Mage::getModel('catalog/product')
//                            ->getCollection()
//                            ->addAttributeToFilter('rex_product_id', $id)
//                            ->getFirstItem()->getMinQty());

            $stockModel = Mage::getModel('cataloginventory/stock_item');

            // @todo: need to get min qty dependent on product use_config_min_qty field
            $_productMinQty = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_QTY);

            if ($p->ManageStock && $p->ManageStock == 0) {
                $isInStock = 1;
            } elseif (($p->ManageStock) && ($p->StockAvailable > $_productMinQty)) {
                $isInStock = 1;
            } elseif (($p->ManageStock) && (($p->StockOnOrder + $p->StockAvailable) > $_productMinQty)) {
                $isInStock = 2;
            } else {
                $isInStock = 0;
            }

            $return = [
                'price' => $price,
                'special_price' => $special_price,
                'stock_data' => [
                    'qty' => (int) $p->StockAvailable,
                    'manage_stock' => (int) $p->ManageStock,
                    'is_in_stock' => $isInStock,
                    'use_config_manage_stock' => 0,
                    'qty_on_order' => (int) $p->StockOnOrder,
                ],
                'weight' => isset($p->Weight) ? (float) $p->Weight : false,
                'tax_class_id' => isset($p->Taxable) ? (((int) $p->Taxable) ? 2 : 0) : false,
            ];

            return $return;
        } catch (Exception $e) {
            throw $e;
        }

        return false;
    }

    public function getOrdersBulkDetail($last_update)
    {
        $this->_initSoapZip();
        $helper = Mage::helper('retailexpress');
        try {
            try {
                if ($helper->getIsEtaEnabled()) {
                    $result = self::$soap->WebOrderGetBulkFulfillmentByChannel(['LastUpdated' => $last_update, 'ChannelId' => $this->_channelId]);
                } else {
                    $result = self::$soap->WebOrderGetBulkFulfillment(['LastUpdated' => $last_update]);
                }
            } catch (REX_RetailSoapZipException $e) {
                $result = $e->getXml();
            }
            if (!$result) {
                if ($helper->getIsEtaEnabled()) {
                    throw new Exception("Incorrect method 'WebOrderGetBulkFulfillmentByChannel' response");
                } else {
                    throw new Exception("Incorrect method 'WebOrderGetBulkFulfillment' response");
                }
            }

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            $logModel->saveSyncLogs($result, $logLastRequest, 'WEBORDERGETBULKFULFILLMENT', 'BULK');
            //end save logs

            $XML = new SimpleXMLElement($result);
            if (!$XML) {
                if ($helper->getIsEtaEnabled()) {
                    throw new Exception("No XML in method 'WebOrderGetBulkFulfillmentByChannel' response");
                } else {
                    throw new Exception("No XML in method 'WebOrderGetBulkFulfillment' response");
                }
            }

            $items = [];
            $return = [];

            if (isset($XML->OrderItems) && isset($XML->OrderItems->OrderItem)) {
                foreach ($XML->OrderItems->OrderItem as $i) {
                    if (!isset($items[(string) $i->OrderId])) {
                        $items[(string) $i->OrderId] = [];
                    }

                    $items[(string) $i->OrderId][(string) $i->OrderItemId] = (int) $i->ProductId;

                    $return[(string) $i->OrderId]['products'][] = (int) $i->ProductId;

                    if (isset($return[(string) $i->OrderId]['items'][(int) $i->ProductId])) {
                        $return[(string) $i->OrderId]['items'][(int) $i->ProductId] += (int) $i->QtyOrdered;
                    } else {
                        $return[(string) $i->OrderId]['items'][(int) $i->ProductId] = (int) $i->QtyOrdered;
                    }
                }
            }

            if (isset($XML->OrderFulfillment) && isset($XML->OrderFulfillment->Fulfillment)) {
                foreach ($XML->OrderFulfillment->Fulfillment as $i) {
                    if (!isset($items[(string) $i->OrderId]) || !isset($items[(string) $i->OrderId][(string) $i->OrderItemId])) {
                        continue;
                    }

                    if (!isset($return[(string) $i->OrderId])) {
                        $return[(string) $i->OrderId] = [];
                    }

                    if (!isset($return[(string) $i->OrderId][$items[(string) $i->OrderId][(string) $i->OrderItemId]])) {
                        $return[(string) $i->OrderId][$items[(string) $i->OrderId][(string) $i->OrderItemId]] = [];
                    }
                    $return[(string) $i->OrderId][$items[(string) $i->OrderId][(string) $i->OrderItemId]][] = ['qty' => (int) $i->QtyFulfilled, 'date' => (string) $i->DateFulfilled];
                }
            }
            if (isset($XML->OrderPayments) && isset($XML->OrderPayments->Payment)) {
                foreach ($XML->OrderPayments->Payment as $i) {
                    if (!isset($return[(string) $i->OrderId])) {
                        $return[(string) $i->OrderId] = [];
                    }

                    $return[(string) $i->OrderId]['payment'] = (string) $i->Payment;
                    $return[(string) $i->OrderId]['method'] = (string) $i->MethodId;
                }
            }
            if (isset($XML->Orders) && isset($XML->Orders->Order)) {
                foreach ($XML->Orders->Order as $i) {
                    if (isset($return[(string) $i->OrderId])) {
                        $return[(string) $i->OrderId]['status'] = strtolower((string) $i->OrderStatus);
                        $return[(string) $i->OrderId]['customerid'] = (string) $i->CustomerId;
                        $return[(string) $i->OrderId]['order_total'] = (string) $i->OrderTotal;
                        $return[(string) $i->OrderId]['customer_email'] = (string) $i->CustomerEmail;
                        $return[(string) $i->OrderId]['billing_address'] = [
                            'name' => (string) $i->BillName,
                            'address' => (string) $i->BillAddress,
                            'address2' => (string) $i->BillAddress2,
                            'company' => (string) $i->BillCompany,
                            'mobile' => (string) $i->BillMobile,
                            'phone' => (string) $i->BillPhone,
                            'postcode' => (string) $i->BillPostCode,
                            'city' => (string) $i->BillSuburb,
                            'state' => (string) $i->BillState,
                            'country' => (string) $i->BillCountry,
                            'email' => (string) $i->BillEmail,
                        ];

                        $return[(string) $i->OrderId]['shipping_address'] = [
                            'name' => (string) $i->DelName,
                            'address' => (string) $i->DelAddress,
                            'address2' => (string) $i->DelAddress2,
                            'company' => (string) $i->DelCompany,
                            'mobile' => (string) $i->DelMobile,
                            'phone' => (string) $i->DelPhone,
                            'postcode' => (string) $i->DelPostCode,
                            'city' => (string) $i->DelSuburb,
                            'state' => (string) $i->DelState,
                            'country' => (string) $i->DelCountry,
                            'email' => (string) $i->DelEmail,
                        ];
                    }
                }
            }

            return $return;
        } catch (Exception $e) {
            Mage::getModel('retailexpress/job')->load(self::$soap->getJobId())
                ->setErrorText($e->getMessage())
                ;
            throw $e;
        }
    }

    /**
     * Get XML with product bulk method.
     *
     * @throws Exception - Error in XML
     *
     * @param string $last_update - changes from
     *
     * @return string - XML files
     */
    public function getProductsBulkDetail($last_update)
    {
        $this->_initSoapZip();
        $helper = Mage::helper('retailexpress');
        try {
            try {
                if ($helper->getIsEtaEnabled()) {
                    $result = self::$soap->ProductsGetBulkDetailsByChannel(['LastUpdated' => $last_update, 'ChannelId' => $this->_channelId]);
                } else {
                    $result = self::$soap->ProductsGetBulkDetails(['LastUpdated' => $last_update]);
                }
            } catch (REX_RetailSoapZipException $e) {
                $result = $e->getXml();
            }

            if (!$result) {
                if ($helper->getIsEtaEnabled()) {
                    throw new Exception("Incorrect method 'ProductsGetBulkDetailsByChannel' response");
                } else {
                    throw new Exception("Incorrect method 'ProductsGetBulkDetails' response");
                }
            }

            //save logs

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            $logModel->saveSyncLogs($result, $logLastRequest, 'PRODUCTSGETBULKDETAILS', 'BULK');

            //end save logs

            return $result;
        } catch (Exception $e) {
            Mage::getModel('retailexpress/job')->load(self::$soap->getJobId())
                ->setErrorText($e->getMessage())
                ;
            throw $e;
        }
    }

    /**
     * return parsed product XML file.
     *
     * @param $result
     *
     * @return array
     *
     * @throws Exception
     */
    public function parseProductXml($result)
    {
        Mage::getConfig()->cleanCache();

        libxml_use_internal_errors(false);

        $XML = false;
        $k = 0;

        while (!$XML || $k >= 5) {
            try {
                $XML = new SimpleXMLElement($result, LIBXML_COMPACT & LIBXML_NOERROR & LIBXML_NOWARNING & LIBXML_PARSEHUGE);
            } catch (Exception $e) {
                ++$k;
            }
        }

        if (!$XML) {
            throw new Exception("No XML in method 'ProductsGetBulkDetails' response");
        }

        $result = null;

        if (!$XML->Attributes) {
            throw new Exception("No valid XML format in method 'ProductsGetBulkDetails' response");
        }

        $attr_codes = [
            'rex_sizes' => [
                'parent_tag' => 'Sizes',
                'child_tag' => 'Size',
                'id_tag' => 'SizeId',
                'name_tag' => 'SizeName',
                'product_tag' => 'SizeId',
                'order_tag' => 'ListOrder',
            ],
             'rex_colours' => [
                'parent_tag' => 'Colours',
                'child_tag' => 'Colour',
                'id_tag' => 'ColourId',
                'name_tag' => 'ColourName',
                'product_tag' => 'ColourId',
            ],
             'rex_seasons' => [
                'parent_tag' => 'Seasons',
                'child_tag' => 'Season',
                'id_tag' => 'SeasonId',
                'name_tag' => 'SeasonName',
                'product_tag' => 'SeasonId',
            ],
             'rex_product_types' => [
                'parent_tag' => 'ProductTypes',
                'child_tag' => 'ProductType',
                'id_tag' => 'ProductTypeId',
                'name_tag' => 'ProductTypeName',
                'product_tag' => 'ProductTypeId',
            ],
             'rex_brands' => [
                'parent_tag' => 'Brands',
                'child_tag' => 'Brand',
                'id_tag' => 'BrandId',
                'name_tag' => 'BrandName',
                'product_tag' => 'BrandId',
            ],
        ];
        $attributes = [];
        $payments = [];

        if ($XML->Attributes) {
            foreach ($attr_codes as $a_key => $a_data) {
                $parent_tag = $a_data['parent_tag'];
                $child_tag = $a_data['child_tag'];
                $id_tag = $a_data['id_tag'];
                $name_tag = $a_data['name_tag'];
                if ($XML->Attributes->$parent_tag) {
                    $attributes[$a_key] = [];
                    if ($XML->Attributes->$parent_tag->$child_tag) {
                        foreach ($XML->Attributes->$parent_tag->$child_tag as $attr_value) {
                            $___t = [
                                'id' => (string) $attr_value->$id_tag,
                                'name' => (string) $attr_value->$name_tag,
                            ];
                            if (isset($a_data['order_tag'])) {
                                $__tag_name = $a_data['order_tag'];
                                $___t['sort_order'] = (int) $attr_value->$__tag_name;
                            }

                            $attributes[$a_key][] = $___t;
                        }
                    }
                }
            }

            if ($XML->Attributes->PaymentMethods) {
                foreach ($XML->Attributes->PaymentMethods->PaymentMethod as $PaymentXML) {
                    if ((string) $PaymentXML->Enabled == 'true') {
                        $payments[(int) $PaymentXML->ID] = [
                            'rex_id' => (int) $PaymentXML->ID,
                            'name' => (string) $PaymentXML->Name,
                        ];
                    }
                }
            }
        }

        $disabled_products = [];
        if ($XML->DisabledProducts && $XML->DisabledProducts->Product) {
            foreach ($XML->DisabledProducts->Product as $XML_Disable) {
                $disabled_products[(string) $XML_Disable->ProductId] = true;
            }
        }

        $products = [];
        $conf_products = [];
        $product_exists = [];
        $associated_products = [];

//        $stockModel = Mage::getModel('cataloginventory/stock_item');

        // @todo: need to get min qty dependent on product use_config_min_qty field
        $_defaultProductMinQty = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_QTY);

        if ($XML->Products && $XML->Products->Product) {
            foreach ($XML->Products->Product as $XML_Product) {
                $product_id = (string) $XML_Product->ProductId;
                if (isset($product_exists[$product_id])) {
                    continue;
                }

                $product_exists[$product_id] = true;

                $price_field = Mage::getStoreConfig('retailexpress/price/regular');
                $special_field = Mage::getStoreConfig('retailexpress/price/special');
                $price = (float) $XML_Product->DefaultPrice;
                $special_price = (isset($XML_Product->DiscountedPrice)) ? (float) $XML_Product->DiscountedPrice : '';
                if ($price_field && isset($XML_Product->$price_field)) {
                    $price = (float) $XML_Product->$price_field;
                }

                if ($special_field && isset($XML_Product->$special_field)) {
                    $special_price = (float) $XML_Product->$special_field;
                }

                if ('RRP' == $special_field) {
                    $special_price = '';
                }

                if (('RRP' == $price_field) && (!$price)) {
                    if (!$special_price) {
                        $special_price = (float) $XML_Product->CustomerDiscountedPrice;
                    }

                    $price = $special_price;
                    $special_price = '';
                }

                // load product exists (simple products only)
                $existingSimpleProduct = Mage::getModel('catalog/product')->loadByAttribute('rex_product_id', $product_id);

                if ($existingSimpleProduct &&
                    ($stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($existingSimpleProduct)) &&
                    $stockItem->getId()
                ) {
                    $productStock = [
                        'use_config_min_qty' => $stockItem->getUseConfigMinQty(),
                        'use_config_min_sale_qty' => $stockItem->getUseConfigMinSaleQty(),
                        'use_config_max_sale_qty' => $stockItem->getUseConfigMaxSaleQty(),
                        'use_config_enable_qty_increments' => $stockItem->getUseConfigEnableQtyIncrements(),
                        'use_config_qty_increments' => $stockItem->getUseConfigQtyIncrements(),
                        'use_config_manage_stock' => $stockItem->getUseConfigManageStock(),
                    ];

                    $_productMinQty = $stockItem->getMinQty();
                } else {
                    $productStock = [
                        'use_config_min_qty' => 1,
                        'use_config_min_sale_qty' => 1,
                        'use_config_max_sale_qty' => 1,
                        'use_config_enable_qty_increments' => 1,
                        'use_config_qty_increments' => 1,
                        'use_config_manage_stock' => 1,
                    ];

                    $_productMinQty = $_defaultProductMinQty;
                }

                $fallbackStockMinSaleQty = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_SALE_QTY);
                $fallbackStockMaxSaleQty = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MAX_SALE_QTY);
                $fallbackStockEnableQtyIncrements = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ENABLE_QTY_INCREMENTS);
                $fallbackStockQtyIncrements = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_QTY_INCREMENTS);

                if ($XML_Product->ManageStock && $XML_Product->ManageStock == 0) {
                    $isInStock = 1;
                } elseif (($XML_Product->ManageStock) && ($XML_Product->StockAvailable > $_productMinQty)) {
                    $isInStock = 1;
                } elseif (($XML_Product->ManageStock) && (($XML_Product->StockOnOrder + $XML_Product->StockAvailable) > $_productMinQty)) {
                    $isInStock = 2;
                } else {
                    $isInStock = 0;
                }
                if (!$XML_Product->ManageStock) {
                    $isInStock = 1;
                }

                $product = [
                    'rex_product_id' => $product_id,
                    'rex_sku' => (string) $XML_Product->SKU,
                    'sku' => $existingSimpleProduct ? $existingSimpleProduct->getData('sku') : 'POS-'.$product_id,
                    'type_id' => $existingSimpleProduct ? $existingSimpleProduct->getData('type_id') : 'simple',
                    'visibility' => $existingSimpleProduct ? $existingSimpleProduct->getData('visibility') : Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                    'store_pickup_rule' => $existingSimpleProduct ? $existingSimpleProduct->getAttributeText('store_pickup_rule') : POS_ClickAndCollect_Model_Product_Attribute_Source_Pickuprule::DEFAULT_VALUE,
                    'name' => (string) $XML_Product->Description,
                    'freight' => (string) $XML_Product->Freight,
                    'weight' => (string) $XML_Product->Weight,
                    'status' => isset($disabled_products[$product_id]) ? 2 : 1,
                    'stock_data' => [
                        'qty' => (int) $XML_Product->StockAvailable,
                        'is_in_stock' => $isInStock,
                        'allow_check_availability' => ((int) $XML_Product->StockAvailable <= 0 && (int) $XML_Product->StockOnOrder > (int) $XML_Product->StockAvailable),
                        'qty_on_order' => (int) $XML_Product->StockOnOrder,
                        'manage_stock' => (int) $XML_Product->ManageStock,
                        'min_sale_qty' => (int) ($existingSimpleProduct ? $stockItem->getMinSaleQty() : $fallbackStockMinSaleQty),
                        'max_sale_qty' => (int) ($existingSimpleProduct ? $stockItem->getMaxSaleQty() : $fallbackStockMaxSaleQty),
                        'min_qty' => (int) ($existingSimpleProduct ? $stockItem->getMinQty() : $_productMinQty),
                        'enable_qty_increments' => (int) ($existingSimpleProduct ? $stockItem->getEnableQtyIncrements() : $fallbackStockEnableQtyIncrements),
                        'qty_increments' => (int) ($existingSimpleProduct ? $stockItem->getQtyIncrements() : $fallbackStockQtyIncrements),
                    ],
                    'price' => $price,
                    'tax_class_id' => ((int) $XML_Product->Taxable) ? 2 : 0,
                ];

                $product['stock_data'] = array_merge($productStock, $product['stock_data']);

                if (isset($XML_Product->Custom1) && Mage::getStoreConfig('retailexpress/attr/rex_custom1')) {
                    $product[Mage::getStoreConfig('retailexpress/attr/rex_custom1')] = (string) $XML_Product->Custom1;
                }
                if (isset($XML_Product->Custom2) && Mage::getStoreConfig('retailexpress/attr/rex_custom2')) {
                    $product[Mage::getStoreConfig('retailexpress/attr/rex_custom2')] = (string) $XML_Product->Custom2;
                }
                if (isset($XML_Product->Custom3) && Mage::getStoreConfig('retailexpress/attr/rex_custom3')) {
                    $product[Mage::getStoreConfig('retailexpress/attr/rex_custom3')] = (string) $XML_Product->Custom3;
                }
                if ($special_price) {
                    $product['special_price'] = $special_price;
                }
                foreach ($attr_codes as $a_code => $a_data) {
                    $product_tag = $a_data['product_tag'];
                    $product[$a_code] = (string) $XML_Product->$product_tag;
                }

                if ((int) $XML_Product->MatrixProduct) {
                    //load configurable product details if exists
                    $existingConfigurableProduct = Mage::getModel('catalog/product')->loadByAttribute('rex_product_id', (string) $XML_Product->Code);

                    $configurableProduct = $product;

                    if (!$existingConfigurableProduct) {
                        // set new configurable products visible in Catalog, Search
                        $configurableProduct['visibility'] = Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH;
                        $configurableProduct['store_pickup_rule'] = POS_ClickAndCollect_Model_Product_Attribute_Source_Pickuprule::DEFAULT_VALUE;
                        $configurableProduct['sku'] = 'POS-'.(string) $XML_Product->Code;
                    } else {
                        // keep visibility of existing products
                        $configurableProduct['visibility'] = $existingConfigurableProduct->getData('visibility');
                        $configurableProduct['store_pickup_rule'] = $existingConfigurableProduct->getAttributeText('store_pickup_rule');
                        $configurableProduct['sku'] = $existingConfigurableProduct->getData('sku');

                        foreach ($existingConfigurableProduct->getTypeInstance()->getUsedProducts() as $child) {
                            if (!isset($existing_associated_products[(string) $XML_Product->Code])) {
                                $existing_associated_products[(string) $XML_Product->Code] = [];
                            }
                            $existing_associated_product = [];
                            $existing_associated_product['id'] = $child->getId();
                            $existing_associated_product['rex_product_id'] = $child->getData('rex_product_id');

                            foreach ($attr_codes as $key => $values) {
                                if (Mage::getStoreConfig('retailexpress/attr/'.$key)) {
                                    $attributeCode = Mage::getStoreConfig('retailexpress/attr/'.$key);
                                    $attributeValue = $child->getData($attributeCode);
                                    $posAttributValue = Mage::getModel('retailexpress/attr')->getRexIdByMagentoCode($attributeCode, $attributeValue);
                                    //$existing_associated_product[$key] = $child->getData($attributeCode);
                                    $existing_associated_product[$key] = $posAttributValue;
                                }
                            }

                            $existing_associated_products[(string) $XML_Product->Code][$child->getData('rex_product_id')] = $existing_associated_product;
                        }
                    }

                    if (!isset($associated_products[(string) $XML_Product->Code])) {
                        $associated_products[(string) $XML_Product->Code] = [];
                    }

                    if (!$existingSimpleProduct) {
                        $product['visibility'] = Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;
                    }

                    $associated_products[(string) $XML_Product->Code][] = $product;
                    $existing_associated_products[(string) $XML_Product->Code][$product_id] = $product;
                    $products[] = $product;
                    $configurableProduct['type_id'] = 'configurable';
                    $configurableProduct['rex_product_id'] = (string) $XML_Product->Code;
                    $configurableProduct['rex_sku'] = (string) $XML_Product->Code;
                    $configurableProduct['stock_data']['is_in_stock'] = 1;
                    $conf_products[(string) $XML_Product->Code] = $configurableProduct;
                } else {
                    if (isset($XML_Product->MatrixProduct)) {
                        if (!isset($associated_products[(string) $XML_Product->Code])) {
                            $associated_products[(string) $XML_Product->Code] = [];
                        }

                        if (!$existingSimpleProduct) {
                            $product['visibility'] = Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;
                        }

                        $associated_products[(string) $XML_Product->Code][] = $product;
                        $existing_associated_products[(string) $XML_Product->Code][$product_id] = $product;
                    }

                    $products[] = $product;
                }
            }
        }

        foreach ($associated_products as $sku => $items) {
            if (isset($conf_products[$sku])) {
                $conf_products[$sku]['associated_products'] = $items;
            }
        }

        $return = [];
        $return['existing_associated_products'] = $existing_associated_products;
        $return['products'] = array_merge($products, $conf_products);
        $return['attributes'] = $attributes;
        $return['payments'] = $payments;

        return $return;
    }

    public function CustomerGetDetails($customer_id)
    {
        $this->_initSoap();
        $result = self::$soap->CustomerGetDetails(['CustomerId' => $customer_id]);
        if (!is_object($result) || !$result->CustomerGetDetailsResult || !$result->CustomerGetDetailsResult->any) {
            throw new Exception("Incorrect method 'CustomerGetDetailsResult' response");
        }

        //save logs

        //get the last sent xml request
        $logLastRequest = $this->__getlastSentSoapRequest();
        $logModel = Mage::getModel('retailexpress/log');
        $logModel->saveSyncLogs($result, $logLastRequest, 'CUSTOMERGETDETAILS', 'ONDEMAND');

        //end save logs

        $XML = new SimpleXMLElement($result->CustomerGetDetailsResult->any);
        if (!$XML) {
            throw new Exception("No XML in method 'CustomerGetDetailsResult' response");
        }

        if (!$XML->Customers || !($XML->Customers->Customer)) {
            throw new Exception('No Customer info in response');
        }

        return $this->_getCustomerData($XML->Customers->Customer);
    }

    public function getCustomerBulkDetails($last_update)
    {
        $this->_initSoapZip();
        try {
            try {
                $result = self::$soap->CustomerGetBulkDetails(['LastUpdated' => $last_update, 'OnlyCustomersWithEmails' => 1]);
            } catch (REX_RetailSoapZipException $e) {
                $result = $e->getXml();
            }

            //save logs

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            if (version_compare(Mage::getVersion(), '1.6.0.0', '>=') || (strlen($result) <= Mage::helper('retailexpress')->getMaxDataSize())) {
                $logModel->saveSyncLogs($result, $logLastRequest, 'CUSTOMERGETBULKDETAILS', 'BULK');
            } else {
                $logModel->saveSyncLogs('We can\'t log big data packets on magento 1.5', $logLastRequest, 'CUSTOMERGETBULKDETAILS', 'BULK');
            }
            //end save logs

            if (!$result) {
                throw new Exception("Incorrect method 'CustomerGetBulkDetails' response");
            }

            return $result;
        } catch (Exception $e) {
            Mage::getModel('retailexpress/job')->load(self::$soap->getJobId())
                ->setErrorText($e->getMessage())
//                ->save()
                ;
            throw $e;
        }
    }

    /**
     * parse customers XML.
     *
     * @throws Exception
     *
     * @param $result string - XML with customers
     *
     * @return array - array with customer data
     */
    public function parseCustomersXml($result)
    {
        try {
            $XML = new SimpleXMLElement($result);
            unset($result);
            if (!$XML) {
                throw new Exception("No XML in method 'CustomerGetBulkDetails' response");
            }

            if (!$XML->Customers) {
                throw new Exception("No valid XML format in method 'CustomerGetBulkDetails' response");
            }

            $customers = [];
            if ($XML->Customers->Customer) {
                foreach ($XML->Customers->Customer as $Customer) {
                    $customers[] = $this->_getCustomerData($Customer);
                }
            }

            $return = [];
            $return['customers'] = $customers;

            return $return;
        } catch (Exception $e) {
            Mage::getModel('retailexpress/job')->load(self::$soap->getJobId())
                ->setErrorText($e->getMessage())
//                ->save()
                ;
            throw $e;
        }
    }

    protected function _getCustomerData($Customer)
    {
        $fields = [
            'CustomerId' => 'rex_id',
            'Password' => 'password',
            'DelName' => 's_firstname',
            'DelAddress' => 's_address',
            'DelAddress2' => 's_address2',
            'DelCompany' => 's_company',
            'DelPhone' => 's_telephone',
            'DelPostCode' => 's_postcode',
            'DelSuburb' => 's_city',
            'DelState' => 's_region',
            'DelCountry' => 's_country_id',
            'BillFirstName' => 'firstname',
            'BillLastName' => 'lastname',
            'BillEmail' => 'email',
            'BillPhone' => 'b_telephone',
            'BillFax' => 'b_fax',
            'BillCompany' => 'b_company',
            'BillAddress' => 'b_address',
            'BillAddress2' => 'b_address2',
            'BillSuburb' => 'b_city',
            'BillState' => 'b_region',
            'BillPostCode' => 'b_postcode',
            'BillCountry' => 'b_country_id',
            'ReceivesNews' => 'subscription',
            'PriceGroupId' => 'rex_group_id',
            'PriceGroupName' => 'rex_group_name',
            'BillABN' => 'taxvat',
        ];
        $cus_data = [];
        foreach ($fields as $f => $name) {
            if (isset($Customer->$f) && (string) $Customer->$f && trim((string) $Customer->$f)) {
                $cus_data[$name] = trim((string) $Customer->$f);
            }
        }

        return $cus_data;
    }

    public function CustomerCreateUpdate($data)
    {
        try {
            //Mage::log("POS:: Retail.php > CustomerCreateUpdate > Begin Function, Params: data=$data", 'possystem.log');
            $this->_initSoap();
            $CustomerXML = $this->_doXml(['Customer' => $data], 'Customers');
            $result = self::$soap->CustomerCreateUpdate(['CustomerXML' => $CustomerXML]);
            //Mage::log("POS:: Retail.php > CustomerCreateUpdate > XML to send: result $result", 'possystem.log');
            if (!is_object($result) || !$result->CustomerCreateUpdateResult || !$result->CustomerCreateUpdateResult->any) {
                throw new Exception("Incorrect method 'CustomerCreateUpdateResult' response");
            }

            //save logs

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            $logModel->saveSyncLogs($result, $logLastRequest, 'CUSTOMERCREATEUPDATE', 'ONDEMAND');

            //end save logs

            $XML = new SimpleXMLElement($result->CustomerCreateUpdateResult->any);
            if (!$XML) {
                throw new Exception("No XML in method 'CustomerCreateUpdateResult' response");
            }

            if ($XML->Error) {
                throw new Exception((string) $XML->Error);
            }

            if ($XML->Customer && $XML->Customer->CustomerId) {
                return (int) $XML->Customer->CustomerId;
            }

            throw new Exception("No customer tag in return 'CustomerCreateUpdateResult' response");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function OrderCreate($data)
    {
        $helper = Mage::helper('retailexpress');
        try {
            $this->_initSoap();
            $OrderXML = $this->_doXml(['Order' => $data], 'Orders');

            if ($helper->getIsEtaEnabled()) {
                $result = self::$soap->OrderCreateByChannel(['OrderXML' => $OrderXML, 'ChannelId' => $this->_channelId]);
                if (!is_object($result) || !$result->OrderCreateByChannelResult || !$result->OrderCreateByChannelResult->any) {
                    throw new Exception("Incorrect method 'OrderCreateByChannel' response");
                }

                //save logs
                //get the last sent xml request
                $logLastRequest = $this->__getlastSentSoapRequest();
                $logModel = Mage::getModel('retailexpress/log');
                $logModel->saveSyncLogs($result->OrderCreateByChannelResult->any, $logLastRequest, 'ORDERCREATEBYCHANNEL', 'ONDEMAND');
                //end save logs

                $XML = new SimpleXMLElement($result->OrderCreateByChannelResult->any);
            } else {
                $result = self::$soap->OrderCreate(['OrderXML' => $OrderXML]);
                if (!is_object($result) || !$result->OrderCreateResult || !$result->OrderCreateResult->any) {
                    throw new Exception("Incorrect method 'OrderCreateResult' response");
                }

                //save logs
                //get the last sent xml request
                $logLastRequest = $this->__getlastSentSoapRequest();
                $logModel = Mage::getModel('retailexpress/log');
                $logModel->saveSyncLogs($result->OrderCreateResult->any, $logLastRequest, 'ORDERCREATE', 'ONDEMAND');
                //end save logs

                $XML = new SimpleXMLElement($result->OrderCreateResult->any);
            }

            if (!$XML) {
                if ($helper->getIsEtaEnabled()) {
                    throw new Exception("No XML in method 'OrderCreateByChannel' response");
                } else {
                    throw new Exception("No XML in method 'OrderCreateResult' response");
                }
            }

            if ($XML->Error) {
                throw new Exception((string) $XML->Error);
            }

            if ((string) $XML->OrderCreate->Order->Result == 'Fail') {
                throw new Exception('Error place order');
            }

            $return = [];
            if (!$XML->OrderCreate || !$XML->OrderCreate->Order || !$XML->OrderCreate->Order->OrderId) {
                throw new Exception('No OrderId in response');
            }

            $return['order_id'] = (string) $XML->OrderCreate->Order->OrderId;
            if ($XML->OrderCreate->Customer && $XML->OrderCreate->Customer->CustomerId) {
                $return['customer_id'] = (string) $XML->OrderCreate->Customer->CustomerId;
            }

            return $return;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function _doXml($data, $rootNodeName = 'data', $xml = null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }

        if (is_null($xml)) {
            $xml = new REX_CDATAXMLElement("<$rootNodeName></$rootNodeName>", LIBXML_NOERROR & LIBXML_NOWARNING);
        }

        // loop through the data passed in.
        foreach ($data as $key => $value) {
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                $this->_doXml($value, $rootNodeName, $xml);
            } else {
                // delete any char not allowed in XML element names
                $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

                // if there is another array found recrusively call this function
                if (is_array($value)) {
                    if (isset($value[0])) {
                        foreach ($value as $k => $v) {
                            $node = $xml->addChild($key);

                            // recrusive call.
                            $this->_doXml($v, $key, $node);
                        }
                    } else {
                        $node = $xml->addChild($key);

                        // recrusive call.
                        $this->_doXml($value, $key, $node);
                    }
                } else {
                    // If our stripped value is not the same as the original
                    // value, we will need to encode it in a CDATA tag.
                    // This means any characters may exist here
                    // and not break a valid XML document.
                    if (htmlentities($value) !== $value) {
                        $xml->$key = null;
                        $xml->$key->addCDATA($value);
                    } else {
                        $xml->addChild($key, $value);
                    }
                }
            }
        }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }

    public function OrdersGetHistory($id, $order_id = false)
    {
        $helper = Mage::helper('retailexpress');
        try {
            $this->_initSoap();
            if ($helper->getIsEtaEnabled()) {
                $result = self::$soap->OrdersGetHistoryByChannel(['CustomerId' => $id, 'WebOrdersOnly' => '0', 'ChannelId' => $this->_channelId]);
            } else {
                $result = self::$soap->OrdersGetHistory(['CustomerId' => $id, 'WebOrdersOnly' => '0']);
            }

            if ($helper->getIsEtaEnabled()) {
                if (!is_object($result) || !$result->OrdersGetHistoryByChannelResult || !$result->OrdersGetHistoryByChannelResult->any) {
                    throw new Exception("Incorrect method 'OrdersGetHistoryByChannel' response");
                }
            } else {
                if (!is_object($result) || !$result->OrdersGetHistoryResult || !$result->OrdersGetHistoryResult->any) {
                    throw new Exception("Incorrect method 'OrdersGetHistoryResult' response");
                }
            }

            //save logs

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            $logModel->saveSyncLogs($result, $logLastRequest, 'ORDERSGETHISTORY', 'ONDEMAND');

            //end save logs

            if ($helper->getIsEtaEnabled()) {
                $any = preg_replace('|<xs:schema.*</xs:schema>|', '', $result->OrdersGetHistoryByChannelResult->any);
                $XML = new SimpleXMLElement($any);
                if (!$XML) {
                    throw new Exception("No XML in method 'OrdersGetHistoryByChannel' response");
                }
            } else {
                $any = preg_replace('|<xs:schema.*</xs:schema>|', '', $result->OrdersGetHistoryResult->any);
                $XML = new SimpleXMLElement($any);
                if (!$XML) {
                    throw new Exception("No XML in method 'OrdersGetHistoryResult' response");
                }
            }

            if ($XML->Error) {
                throw new Exception((string) $XML->Error);
            }

            $return = [];
            if ($XML->Order) {
                foreach ($XML->Order as $o) {
                    if ($order_id) {
                        if ((string) $o->OrderId != $order_id) {
                            continue;
                        } else {
                            $mapping = [
                                'OrderId' => 'real_order_id',
                                'DateCreated' => 'created_at',
                                'OrderTotal' => 'grand_total',
                                'FreightTotal' => 'shipping_amount',
                                'OrderStatus' => 'status_label',
                                'DelName' => 's_firstname',
                                'DelAddress' => 's_address',
                                'DelCompany' => 's_company',
                                'DelPhone' => 's_telephone',
                                'DelPostCode' => 's_postcode',
                                'DelSuburb' => 's_city',
                                'DelState' => 's_region',
                                'DelCountry' => 's_country_id',
                                'BillName' => 'b_firstname',
                                'BillEmail' => 'email',
                                'BillPhone' => 'b_telephone',
                                'BillFax' => 'b_fax',
                                'BillCompany' => 'b_company',
                                'BillAddress' => 'b_address',
                                'BillSuburb' => 'b_city',
                                'BillState' => 'b_region',
                                'BillPostCode' => 'b_postcode',
                                'BillCountry' => 'b_country_id',
                            ];
                            foreach ($mapping as $k => $v) {
                                if (isset($o->$k) && $o->$k) {
                                    $return[$v] = (string) $o->$k;
                                }
                            }
                            $return['items'] = [];
                            foreach ($XML->OrderDetail as $d) {
                                if ((string) $d->OrderId != $order_id) {
                                    continue;
                                }

                                $return['items'][] = [
                                    'product_id' => (string) $d->ProductId,
                                    'price' => (string) $d->UnitPrice,
                                    'qty' => (string) $d->QtyOrdered,
                                    'name' => (string) $d->Description,
                                    'qty_fulfilled' => (string) $d->QtyFulfilled,
                                ];
                            }
                            foreach ($XML->OrderPayments as $d) {
                                if ((string) $d->OrderId != $order_id) {
                                    continue;
                                }

                                $return['pay'] = (string) $d->MethodId;
                            }

                            return $return;
                        }
                    }
                    $data = [
                        'order_id' => '',
                        'created_at' => '',
                        'billing_name' => '',
                        'shipping_name' => '',
                        'grand_total' => '',
                    ];
                    if ($o->OrderId) {
                        $data['order_id'] = (string) $o->OrderId;
                    }
                    if ($o->DateCreated) {
                        $data['created_at'] = str_replace('T', ' ', $o->DateCreated);
                    }
                    if ($o->BillName) {
                        $data['billing_name'] = (string) $o->BillName;
                    }
                    if ($o->DelName) {
                        $data['shipping_name'] = (string) $o->DelName;
                    }
                    if ($o->OrderStatus) {
                        $data['order_status'] = (string) $o->OrderStatus;
                    }
                    if ($o->PublicComments) {
                        $data['public_comment'] = (string) $o->PublicComments;
                    }
                    $grand_total = 0;
                    if ($o->FreightTotal) {
                        $data['freight_total'] = (string) $o->FreightTotal;
                        $grand_total += (string) $o->FreightTotal;
                    }
                    if ($o->OrderTotal) {
                        $data['order_total'] = (string) $o->OrderTotal;
                        $grand_total += (string) $o->OrderTotal;
                    }

                    $data['grand_total'] = $grand_total;
                    $return[] = $data;
                }
            }

            return $return;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function OrderAddPayment($data)
    {
        try {
            $this->_initSoap();
            $PaymentXML = $this->_doXml(['OrderPayment' => $data], 'OrderPayments');

            $result = self::$soap->OrderAddPayment(['OrderPaymentXML' => $PaymentXML]);
            if (!is_object($result) || !$result->OrderAddPaymentResult || !$result->OrderAddPaymentResult->any) {
                throw new Exception("Incorrect method 'OrderAddPaymentResult' response");
            }

            //save logs

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            $logModel->saveSyncLogs($result->OrderAddPaymentResult->any, $logLastRequest, 'ORDERADDPAYMENT', 'ONDEMAND');

            //end save logs

            $XML = new SimpleXMLElement($result->OrderAddPaymentResult->any);
            if (!$XML) {
                throw new Exception("No XML in method 'OrderAddPaymentResult' response");
            }

            if ($XML->Error) {
                throw new Exception((string) $XML->Error);
            }

            if (!$XML->Payment || !$XML->Payment->Result) {
                throw new Exception('No Result in response');
            }

            return (string) $XML->Payment->Result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function orderCancel($order_id)
    {
        try {
            $this->_initSoap();
            $result = self::$soap->OrderCancel(['OrderId' => $order_id]);
            if (!is_object($result) || !$result->OrderCancelResult || !$result->OrderCancelResult->any) {
                throw new Exception("Incorrect method 'OrderCancelResult' response");
            }

            //save logs

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            $logModel->saveSyncLogs($result, $logLastRequest, 'ORDERCANCEL', 'ONDEMAND');

            //end save logs

            $XML = new SimpleXMLElement($result->OrderCancelResult->any);
            if (!$XML) {
                throw new Exception("No XML in method 'OrderCancelResult' response");
            }

            if (!$XML->Result) {
                throw new Exception('No Result in response');
            }

            return (string) $XML->Result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function OutletsGet()
    {
        $helper = Mage::helper('retailexpress');
        $this->_initSoapZip();
        try {
            try {
                if ($helper->getIsEtaEnabled()) {
                    $result = self::$soap->OutletsGetByChannel(['ChannelId' => $this->_channelId]);
                } else {
                    $result = self::$soap->OutletsGet([]);
                }
            } catch (REX_RetailSoapZipException $e) {
                $result = $e->getXml();
            }

            if ($helper->getIsEtaEnabled()) {
                //save logs
                //get the last sent xml request
                $logLastRequest = $this->__getlastSentSoapRequest();
                $logModel = Mage::getModel('retailexpress/log');
                $logModel->saveSyncLogs($result, $logLastRequest, 'OUTLETSGETBYCHANNEL', 'BULK');
                //end save logs

                if (!$result) {
                    throw new Exception("Incorrect method 'OutletsGetByChannel' response");
                }
            } else {
                //save logs
                //get the last sent xml request
                $logLastRequest = $this->__getlastSentSoapRequest();
                $logModel = Mage::getModel('retailexpress/log');
                $logModel->saveSyncLogs($result, $logLastRequest, 'OUTLETSGET', 'BULK');
                //end save logs

                if (!$result) {
                    throw new Exception("Incorrect method 'OutletsGet' response");
                }
            }

            return $result;
        } catch (Exception $e) {
            Mage::getModel('retailexpress/job')->load(self::$soap->getJobId())
                ->setErrorText($e->getMessage())
                ;
            throw $e;
        }
    }

    /**
     * parse outlet XML.
     */
    public function parseOutletXML($result)
    {
        try {
            $XML = new SimpleXMLElement($result);
            unset($result);
            if (!$XML) {
                throw new Exception("No XML in method 'OutletsGetByChannel' response");
            }

            if (!$XML->Outlets) {
                throw new Exception("No valid XML format in method 'OutletsGetByChannel' response");
            }

            $outlets = [];
            if ($XML->Outlets->Outlet) {
                foreach ($XML->Outlets->Outlet as $outlet) {
                    $outlets[] = $this->_getOutletData($outlet);
                }
            }

            $return = [];
            $return['outlets'] = $outlets;

            return $return;
        } catch (Exception $e) {
            Mage::getModel('retailexpress/job')->load(self::$soap->getJobId())
                ->setErrorText($e->getMessage())
                ;
            throw $e;
        }
    }

    protected function _getOutletData($outlet)
    {
        $fields = [
            'OutletId' => 'fulfilment_outlet_id',
            'OutletName' => 'outlet_name',
            'Address1' => 'address_1',
            'Address2' => 'address_2',
            'Address3' => 'address_3',
            'Suburb' => 'suburb',
            'State' => 'state',
            'Postcode' => 'postcode',
            'Country' => 'country',
        ];
        $outletData = [];
        foreach ($fields as $f => $name) {
            if (isset($outlet->$f) && (string) $outlet->$f && trim((string) $outlet->$f)) {
                $outletData[$name] = trim((string) $outlet->$f);
            }
        }

        return $outletData;
    }

    public function productGetEtaDate($data)
    {
        $this->_initSoapZip();
        $requestXml = $this->_doXml(['Product' => $data], 'Products');
        try {
            try {
                $result = self::$soap->ProductGetETADateByChannel(['ChannelId' => $this->_channelId, 'RequestXML' => $requestXml]);
            } catch (REX_RetailSoapZipException $e) {
                $result = $e->getXml();
            }

            if (!$result) {
                throw new Exception("Incorrect method 'ProductGetETADateByChannel' response");
            }

            //save logs

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            $logModel->saveSyncLogs($result, $logLastRequest, 'PRODUCTGETETADATEBYCHANNEL', 'ONDEMAND');

            //end save logs

            $XML = new SimpleXMLElement($result);
            if (!$XML) {
                throw new Exception("No XML in method 'ProductGetETADateByChannel' response");
            }

            if (!$XML->Products) {
                throw new Exception('No Products in response');
            }

            $products = [];

            if ($XML->Products->Product) {
                foreach ($XML->Products->Product as $product) {
                    $products[] = [
                        'id' => (string) $product->ProductID,
                        'eta' => (string) $product->ETA,
                    ];
                }
            }

            return $products;
        } catch (Exception $e) {
            Mage::getModel('retailexpress/job')->load(self::$soap->getJobId())
                ->setErrorText($e->getMessage())
                ;
            throw $e;
        }
    }

    /**
     * create order from rex.
     *
     * this method creates a new order to magento from rex
     */
    public function createOrderFromRex($oldOrderId, $rawOrderData)
    {
        $orderModel = Mage::getModel('retailexpress/order');

        $oldOrder = Mage::getModel('sales/order')->loadByIncrementId($oldOrderId);

        $newOrderid = $orderModel->createNewOrderFromRex($oldOrder, $rawOrderData);

        return $newOrderid;
    }

    public function OrderDeliveryUpdate($data)
    {
        try {
            $this->_initSoap();

            try {
                $result = self::$soap->OrderDeliveryUpdate([
                    'OrderId' => $data['OrderId'],
                    'ExternalOrderId' => $data['OrderId'],
                    'Reference' => $data['Reference'],
                    'DeliveryDriverName' => $data['DeliveryDriverName'],
                ]);
            } catch (REX_RetailSoapZipException $e) {
                $result = $e->getXml();
            }

            if (!$result) {
                throw new Exception("Incorrect method 'OrderDeliveryUpdate' response");
            }

            //save logs

            //get the last sent xml request
            $logLastRequest = $this->__getlastSentSoapRequest();
            $logModel = Mage::getModel('retailexpress/log');
            $logModel->saveSyncLogs($result, $logLastRequest, 'ORDERDELIVERYUPDATE', 'ONDEMAND');

            //end save logs

            $return = 1;

            return $return;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
