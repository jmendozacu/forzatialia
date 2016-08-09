<?php

class RetailSoapZipExtensions extends Exception
{

    protected $xml;

    public function getXml()
    {
        return $this->xml;
    }

    public function __construct($xml = null)
    {
        parent::__construct('', 0);
        $this->xml = $xml;
    }

}

class RetailSoapClient extends SoapClient
{

    /**
     * @var bool is REX should return zip archive, no XML
     */
    protected $is_zip_response = true;

    /**
     * @var int ID of job
     */
    protected $job_id = null;

    protected $last_request  = null;
    protected $last_response = null;

    public function getJobId()
    {
        return $this->job_id;
    }

    public function setIsZip($is_zip = true)
    {
        $this->is_zip_response = $is_zip;
        return $this;
    }

	public function __doRequest($request, $location, $action, $version, $one_way = 0)
	{

        try {
            $this->last_request = $request;
		    $return = parent::__doRequest($request, $location, $action, $version, $one_way);
            $this->last_response = $request;
            if (!strpos($return, "<faultstring>") && $this->is_zip_response) {
                $return = Mage::helper('retailexpress')->unzip($return, $this->job_id);
                throw new RetailSoapZipExtensions($return);
            }

            return $return;
        } catch (Exception $e) {
            throw $e;
        }
	}


}

class POS_System_Model_Retail extends Mage_Core_Model_Abstract
{


	protected static $soap = null;


	protected static $outlet = null;

    protected static $cache_stock = array();


	protected static $error = null;

    protected function _getUrl()
    {
    	$url =  Mage::getStoreConfig('retailexpress/main/url');
    	if (!$url) {
    		$url = "http://webservicestest.retailexpress.com.au/DOTNET/Admin/WebServices/WebStore/ServiceV2.asmx?WSDL";
    	}

        return $url;
    }

    protected function _getOptions()
    {
        return array(
			'soap_version' => SOAP_1_1,
			'exceptions'   => true,
		);
    }

    protected function _initSoapZip()
    {
		try {
			self::$soap = new RetailSoapClient($this->_getUrl(), $this->_getOptions());
            $this->_initSoapHeaders();
		} catch (Exception $e) {
			$this->setError($e->getMessage());
		}
    }

	protected function _initSoap()
    {
		try {
			self::$soap = new SoapClient($this->_getUrl(), $this->_getOptions());
            $this->_initSoapHeaders();
		} catch (Exception $e) {
			$this->setError($e->getMessage());
		}
    }

    protected function _initSoapHeaders()
    {
		$ns = "http://retailexpress.com.au/";
		$clientId = Mage::getStoreConfig('retailexpress/main/client_id');
		$username = Mage::getStoreConfig('retailexpress/main/username');
		$password = Mage::getStoreConfig('retailexpress/main/password');
		$headerbody = array(
			'ClientID' => $clientId,
            'UserName' => $username,
            'Password' => $password,
		);
        $header = new SOAPHeader($ns, 'ClientHeader', $headerbody);
		self::$soap->__setSoapHeaders($header);
    }


	public function getProductById($id)
	{
		try {
			$result = self::$soap->GetProductDetail(array('ProductID' => $id, "PLU" => "", "ProductTypeID" => "", "BrandID" => "", "SupplierID" => ""));
			if (!is_object($result) || !is_object($result->GetProductDetailResult) || !is_object($result->GetProductDetailResult->Products) || !is_object($result->GetProductDetailResult->Products->Product) || !is_object($result->GetProductDetailResult->Products->Product->Outlets)) {
				throw new Exception('Get Product Details response incorrect');
			}

			$return = array(
                  'price' => $result->GetProductDetailResult->Products->Product->POSPrice
				, 'special_price' => $result->GetProductDetailResult->Products->Product->DiscountPrice
			);
			if (is_object($result->GetProductDetailResult->Products->Product->Outlets->PricingAndInventory)) {
				if ($result->GetProductDetailResult->Products->Product->Outlets->PricingAndInventory->Outlet != self::$outlet['name']) {
					return "Outlet not exists for product";
				} else {
					$return['qty'] = $result->GetProductDetailResult->Products->Product->Outlets->PricingAndInventory->QtyAvailable;
					if ($result->GetProductDetailResult->Products->Product->Outlets->PricingAndInventory->DiscountPrice) {
						$return['special_price'] = $result->GetProductDetailResult->Products->Product->Outlets->PricingAndInventory->DiscountPrice;
					}
					if ($result->GetProductDetailResult->Products->Product->Outlets->PricingAndInventory->POSPrice) {
						$return['price'] = $result->GetProductDetailResult->Products->Product->Outlets->PricingAndInventory->POSPrice;
					}
				}
			} else {
				$found = false;
				foreach ($result->GetProductDetailResult->Products->Product->Outlets->PricingAndInventory as $q) {
					if ($q->Outlet == self::$outlet['name']) {
						$found = true;
						$return['qty'] = $q->QtyAvailable;
						if ($q->DiscountPrice) {
							$return['special_price'] = $q->DiscountPrice;
						}

						if ($q->POSPrice) {
							$return['price'] = $q->POSPrice;
						}

						break;
					}
				}

				if (!$found) {
					return "Outlet not exists for product";
				}
			}

			return $return;
		} catch (Exception $e) {
			return "Not found";
		}
	}


	public function putOrder($data)
	{
		try {
			$data['OrderID'] = 0;
			$data['Balance'] = 0;
			$data['Customer']['CustomerID'] = 0;
			$data['Customer']['DateCreated'] = time();
			$data['Customer']['LastUpdated'] = time();
			$data['Customer']['ReceivesNews'] = 0;
			$data['Customer']['IsStaffMember'] = 0;
			$data['Customer']['LoyaltyEnabled'] = 0;
			$data['Customer']['LoyaltyPointsToDate'] = 0;
			$data['Customer']['LoyaltyPointsAvailable'] = 0;
			$data['Customer']['LoyaltyPointsRedeemed'] = 0;
			$data['Customer']['LoyaltyAutoPriceGroup'] = 0;
			$data['Customer']['Password'] = md5('qwe123');
			$data['OutletID'] = Mage::getStoreConfig('retailexpress/main/outled_id');
			$result = self::$soap->CreateCustomerOrder(
				array(
					"Order" => array(
						"Order" => $data
					)
				)
			);
			if (!$result->CreateCustomerOrder_x0028_OrderXML_x0029_Result) {
				throw new Exception('Incorrect Responce');
			}

			return array('id' => $result->CreateCustomerOrder_x0028_OrderXML_x0029_Result);
		} catch (Exception $e) {
			return "Cannot add order: " . $e->getMessage();
		}
	}


	protected function setError($error)
	{
		self::$error = $error;
	}


	public function getError()
	{
		return self::$error;
	}

	public function getProductStockPriceById($id, $pricegroup_id)
	{
        $this->_initSoap();
        try {
            Mage::log("POS:: Retail.php > getProductStockPriceById > Params: id=$id, pricegroup_id=$pricegroup_id", null, 'possystem.log');
			if (!isset(self::$cache_stock[(string)$pricegroup_id . "-" . (string)$id])) {
                $result = self::$soap->ProductGetDetailsStockPricing(array('ProductId' => $id, 'CustomerId' => 0, 'PriceGroupId' => $pricegroup_id));
				Mage::log("POS:: Retail.php > getProductStockPriceById > ProductGetDetailsStockPricing SOAP: $result", null, 'possystem.log');
                if (!is_object($result) || !$result->ProductGetDetailsStockPricingResult || !$result->ProductGetDetailsStockPricingResult->any) {
                    throw new Exception("Incorrect method 'ProductGetDetailsStockPricingResult' response");
                }

                $ProductXML = new SimpleXMLElement($result->ProductGetDetailsStockPricingResult->any);
                if (!$ProductXML) {
                    throw new Exception("No XML in method 'ProductGetDetailsStockPricingResult' response");
                }

                if (!$ProductXML->Product || !$ProductXML->Product->ProductId) {
                    throw new Exception("No valid XML format in method 'ProductGetDetailsStockPricingResult' response");
                }

                $p = $ProductXML->Product;
                if (isset($ProductXML->Product[0])) {
                    foreach ($ProductXML->Product as $__p) {
                        if (is_object($__p)) {
                            self::$cache_stock[(string)$pricegroup_id . "-" . (string)$__p->ProductId] = $__p;
                            if (((string)$__p->ProductId) == $id) {
                                $p = $__p;
                            }
                        }
                    }
                }
            } else {
                $p = self::$cache_stock[(string)$pricegroup_id . "-" . (string)$id];
            }

            $price_field = Mage::getStoreConfig('retailexpress/price/regular');
            $special_field = Mage::getStoreConfig('retailexpress/price/special');
            $price = (float)$p->DefaultPrice;
            $special_price = '';
            if ($price_field && isset($p->$price_field)) {
                $price = (float)$p->$price_field;
            }

            if ($special_field && isset($p->$special_field)) {
                $special_price = (float)$p->$special_field;
            }

            if ('RRP' == $special_field) {
                $special_price = '';
            }

            if (('RRP' == $price_field) && (!$price)) {
                if (!$special_price) {
                    $special_price = (float)$p->CustomerDiscountedPrice;
                }

                $price = $special_price;
                $special_price = '';
            }

            $return = array(
                  'price' => $price
                , 'special_price' => $special_price
                , 'stock_data' => array(
                      'qty' => (int)$p->StockAvailable
                    , 'manage_stock' => (int)$p->ManageStock
                    , 'is_in_stock' => ((int)$p->ManageStock && ((int)$p->StockAvailable <= 0))? false : true
                    , 'use_config_manage_stock' => 0
                )
            );

            return $return;
        } catch (Exception $e) {
            throw $e;
        }

		return false;
	}

	public function getOrdersBulkDetail($last_update)
	{
        $this->_initSoapZip();
        try {
            try {
                $result = self::$soap->WebOrderGetBulkFulfillment(array('LastUpdated' => $last_update));
            } catch (RetailSoapZipExtensions $e) {
                $result = $e->getXml();
            }

            if (!$result) {
			    throw new Exception("Incorrect method 'WebOrderGetBulkFulfillment' response");
		    }


            $XML = new SimpleXMLElement($result);
            if (!$XML) {
                throw new Exception("No XML in method 'WebOrderGetBulkFulfillment' response");
            }

            $items = array();
            if (isset($XML->OrderItems) && isset($XML->OrderItems->OrderItem)) {
                foreach ($XML->OrderItems->OrderItem as $i) {
                    if (!isset($items[(string)$i->OrderId])) {
                        $items[(string)$i->OrderId] = array();
                    }

                    $items[(string)$i->OrderId][(string)$i->OrderItemId] = (int)$i->ProductId;
                }
            }

            $return = array();
            if (isset($XML->OrderFulfillment) && isset($XML->OrderFulfillment->Fulfillment)) {
                foreach ($XML->OrderFulfillment->Fulfillment as $i) {
                    if (!isset($items[(string)$i->OrderId]) || !isset($items[(string)$i->OrderId][(string)$i->OrderItemId])) {
                        continue;
                    }

                    if (!isset($return[(string)$i->OrderId])) {
                        $return[(string)$i->OrderId] = array();
                    }

                    $return[(string)$i->OrderId][$items[(string)$i->OrderId][(string)$i->OrderItemId]] = array('qty' => (int)$i->QtyFulfilled, 'date' => (string)$i->DateFulfilled);
                }
            }
            if (isset($XML->OrderPayments) && isset($XML->OrderPayments->Payment)) {
                foreach ($XML->OrderPayments->Payment as $i) {
                    if (!isset($return[(string)$i->OrderId])) {
                        $return[(string)$i->OrderId] = array();
                    }

                    $return[(string)$i->OrderId]['payment'] = (string)$i->Payment;
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
     * Get XML with product bulk method
     *
     * @throws Exception - Error in XML
     * @param string $last_update - changes from
     * @return string - XML files
     */
	public function getProductsBulkDetail($last_update)
	{
        $this->_initSoapZip();
        try {
            try {
                $result = self::$soap->ProductsGetBulkDetails(array('LastUpdated' => $last_update));
            } catch (RetailSoapZipExtensions $e) {
                $result = $e->getXml();
            }

            if (!$result) {
			    throw new Exception("Incorrect method 'ProductsGetBulkDetails' response");
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
     * return parsed product XML file
     *
     * @param $result
     * @return void
     */
    public function parseProductXml($result)
    {
        $XML = new SimpleXMLElement($result);
        if (!$XML) {
            throw new Exception("No XML in method 'ProductsGetBulkDetails' response");
        }

        unset($result);
        if (!$XML->Attributes) {
            throw new Exception("No valid XML format in method 'ProductsGetBulkDetails' response");
        }

        $attr_codes = array(
              'rex_sizes' => array(
                  'parent_tag' => 'Sizes'
                , 'child_tag'  => 'Size'
                , 'id_tag'     => 'SizeId'
                , 'name_tag'   => 'SizeName'
                , 'product_tag'=> 'SizeId'
                , 'order_tag' => 'ListOrder'
            )
            , 'rex_colours' => array(
                  'parent_tag' => 'Colours'
                , 'child_tag'  => 'Colour'
                , 'id_tag'     => 'ColourId'
                , 'name_tag'   => 'ColourName'
                , 'product_tag'=> 'ColourId'
            )
            , 'rex_seasons' => array(
                  'parent_tag' => 'Seasons'
                , 'child_tag'  => 'Season'
                , 'id_tag'     => 'SeasonId'
                , 'name_tag'   => 'SeasonName'
                , 'product_tag'=> 'SeasonId'
            )
            , 'rex_product_types' => array(
                  'parent_tag' => 'ProductTypes'
                , 'child_tag'  => 'ProductType'
                , 'id_tag'     => 'ProductTypeId'
                , 'name_tag'   => 'ProductTypeName'
                , 'product_tag'=> 'ProductTypeId'
            )
            , 'rex_brands' => array(
                  'parent_tag' => 'Brands'
                , 'child_tag'  => 'Brand'
                , 'id_tag'     => 'BrandId'
                , 'name_tag'   => 'BrandName'
                , 'product_tag'=> 'BrandId'
            )
        );
        $attributes = array();
        $payments = array();
        if ($XML->Attributes) {
            foreach ($attr_codes as $a_key => $a_data) {
                $parent_tag = $a_data['parent_tag'];
                $child_tag = $a_data['child_tag'];
                $id_tag = $a_data['id_tag'];
                $name_tag = $a_data['name_tag'];
                if ($XML->Attributes->$parent_tag) {
                    $attributes[$a_key] = array();
                    if ($XML->Attributes->$parent_tag->$child_tag) {
                        foreach ($XML->Attributes->$parent_tag->$child_tag as $attr_value) {
                            $___t = array(
                                  'id'   => (string)$attr_value->$id_tag
                                , 'name' => (string)$attr_value->$name_tag
                            );
                            if (isset($a_data['order_tag'])) {
                                $__tag_name = $a_data['order_tag'];
                                $___t['sort_order'] = (int)$attr_value->$__tag_name;
                            }

                            $attributes[$a_key][] = $___t;
                        }
                    }
                }
            }

            if ($XML->Attributes->PaymentMethods) {
                foreach ($XML->Attributes->PaymentMethods->PaymentMethod as $PaymentXML) {
                    if ((string)$PaymentXML->Enabled == 'true') {
                        $payments[(int)$PaymentXML->ID] = array(
                            'rex_id' => (int)$PaymentXML->ID,
                            'name' => (string)$PaymentXML->Name
                        );
                    }
                }
            }
        }

        $disabled_products = array();
        if ($XML->DisabledProducts && $XML->DisabledProducts->Product) {
            foreach ($XML->DisabledProducts->Product as $XML_Disable) {
                $disabled_products[(string)$XML_Disable->ProductId] = true;
            }
        }

        $products = array();
        $conf_products = array();
        $product_exists = array();
        $associated_products = array();
        if ($XML->Products && $XML->Products->Product) {
            foreach ($XML->Products->Product as $XML_Product) {
                $product_id = (string)$XML_Product->ProductId;
                if (isset($product_exists[$product_id])) {
                    continue;
                }

                $product_exists[$product_id] = true;

                $price_field = Mage::getStoreConfig('retailexpress/price/regular');
                $special_field = Mage::getStoreConfig('retailexpress/price/special');
                $price = (float)$XML_Product->DefaultPrice;
                $special_price = '';
                if ($price_field && isset($XML_Product->$price_field)) {
                    $price = (float)$XML_Product->$price_field;
                }

                if ($special_field && isset($XML_Product->$special_field)) {
                    $special_price = (float)$XML_Product->$special_field;
                }

                if ('RRP' == $special_field) {
                    $special_price = '';
                }

                if (('RRP' == $price_field) && (!$price)) {
                    if (!$special_price) {
                        $special_price = (float)$XML_Product->CustomerDiscountedPrice;
                    }

                    $price = $special_price;
                    $special_price = '';
                }

                $product = array(
                      'rex_product_id' => $product_id
                    , 'sku' => 'POS-' . $product_id
                    , 'type_id' => 'simple'
                    , 'name' => (string)$XML_Product->Description
                    , 'freight' => (string)$XML_Product->Freight
                    , 'weight' => (string)$XML_Product->Weight
                    , 'status' => isset($disabled_products[$product_id])?2:1
                    , 'stock_data' => array(
                              'qty' => (int)$XML_Product->StockAvailable
                            , 'is_in_stock' => ((int)$XML_Product->StockAvailable > 0) || !(int)$XML_Product->ManageStock
                            , 'manage_stock' => (int)$XML_Product->ManageStock
                            , 'use_config_manage_stock' => 0
                    )
                    , 'price' => $price
                    , 'tax_class_id' => ((int)$XML_Product->Taxable)?2:0
                    , 'visibility' => 4
                );
                if (isset($XML_Product->Custom1) && Mage::getStoreConfig('retailexpress/attr/rex_custom1')) {
                    $product[Mage::getStoreConfig('retailexpress/attr/rex_custom1')] = (string)$XML_Product->Custom1;
                }
                if (isset($XML_Product->Custom2) && Mage::getStoreConfig('retailexpress/attr/rex_custom2')) {
                    $product[Mage::getStoreConfig('retailexpress/attr/rex_custom2')] = (string)$XML_Product->Custom2;
                }
                if (isset($XML_Product->Custom3) && Mage::getStoreConfig('retailexpress/attr/rex_custom3')) {
                    $product[Mage::getStoreConfig('retailexpress/attr/rex_custom3')] = (string)$XML_Product->Custom3;
                }
                if ($special_price) {
                    $product['special_price'] = $special_price;
                }
                foreach ($attr_codes as $a_code => $a_data) {
                    $product_tag = $a_data['product_tag'];
                    $product[$a_code] = (string)$XML_Product->$product_tag;
                }

                if ((int)$XML_Product->MatrixProduct) {
                    $product['visibility'] = 1;
                    if (!isset($associated_products[(string)$XML_Product->Code])) {
                        $associated_products[(string)$XML_Product->Code] = array();
                    }

                    $associated_products[(string)$XML_Product->Code][] = $product;
                    $products[] = $product;
                    $product['visibility'] = 4;
                    $product['type_id'] = 'configurable';
                    $product['rex_product_id'] = (string)$XML_Product->Code;
//                    $product['sku'] = (string)$XML_Product->SKU;
                    $product['sku'] = 'POS-' . (string)$XML_Product->Code;
                    $product['stock_data']['is_in_stock'] = 1;
                    $conf_products[(string)$XML_Product->Code] = $product;
                } else {
                    if (isset($XML_Product->MatrixProduct)) {
                        $product['visibility'] = 1;
                        if (!isset($associated_products[(string)$XML_Product->Code])) {
                            $associated_products[(string)$XML_Product->Code] = array();
                        }

                        $associated_products[(string)$XML_Product->Code][] = $product;
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

        $return = array();
//        $return['job_id'] = self::$soap->getJobId();
        $return['attributes'] = $attributes;
        $return['payments'] = $payments;
        $return['products'] = array_merge($products, $conf_products);
//            echo "<pre>"; print_r($XML->Products); exit;
        return $return;
    }


    public function VoucherGetBalance($code)
    {
        $this->_initSoap();
        $result = self::$soap->VoucherGetBalance(array('VoucherCode' => $code));
		if (!is_object($result) || !$result->VoucherGetBalanceResult || !$result->VoucherGetBalanceResult->any) {
			throw new Exception("Incorrect method 'VoucherGetBalanceResult' response");
		}

		$XML = new SimpleXMLElement($result->VoucherGetBalanceResult->any);
		if (!$XML) {
			throw new Exception("No XML in method 'VoucherGetBalanceResult' response");
		}

		if (!$XML->Amount || !trim($XML->Amount)) {
			return false;
		}

        return (float)$XML->Amount;
    }

    public function CustomerGetDetails($customer_id)
    {
        $this->_initSoap();
        $result = self::$soap->CustomerGetDetails(array('CustomerId' => $customer_id));
		if (!is_object($result) || !$result->CustomerGetDetailsResult || !$result->CustomerGetDetailsResult->any) {
			throw new Exception("Incorrect method 'CustomerGetDetailsResult' response");
		}

		$XML = new SimpleXMLElement($result->CustomerGetDetailsResult->any);
		if (!$XML) {
			throw new Exception("No XML in method 'CustomerGetDetailsResult' response");
		}

		if (!$XML->Customers || !($XML->Customers->Customer)) {
			throw new Exception("No Customer info in response");
		}

        return $this->_getCustomerData($XML->Customers->Customer);
    }

    public function getCustomerBulkDetails($last_update)
	{
        $this->_initSoapZip();
        try {
            try {
                $result = self::$soap->CustomerGetBulkDetails(array('LastUpdated' => $last_update, 'OnlyCustomersWithEmails' => 1));
            } catch (RetailSoapZipExtensions $e) {
                $result = $e->getXml();
            }

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
     * parse customers XML
     *
     * @throws Exception
     * @param $result string - XML with customers
     * @return array - array with customer data
     */
    public function parseCustomersXML($result)
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

            $customers = array();
            if ($XML->Customers->Customer) {
                foreach ($XML->Customers->Customer as $Customer) {
                    $customers[] = $this->_getCustomerData($Customer);
                }
            }

            $return = array();
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
        $fields = array(
              'CustomerId' => 'rex_id'
            , 'Password' => 'password'
            , 'DelName' => 's_firstname'
            , 'DelAddress' => 's_address'
            , 'DelAddress2' => 's_address2'
            , 'DelCompany' => 's_company'
            , 'DelPhone' => 's_telephone'
            , 'DelPostCode' => 's_postcode'
            , 'DelSuburb' => 's_city'
            , 'DelState' => 's_region'
            , 'DelCountry' => 's_country_id'
            , 'BillFirstName' => 'firstname'
            , 'BillLastName' => 'lastname'
            , 'BillEmail' => 'email'
            , 'BillPhone' => 'b_telephone'
            , 'BillFax' => 'b_fax'
            , 'BillCompany' => 'b_company'
            , 'BillAddress' => 'b_address'
            , 'BillAddress2' => 'b_address2'
            , 'BillSuburb' => 'b_city'
            , 'BillState' => 'b_region'
            , 'BillPostCode' => 'b_postcode'
            , 'BillCountry' => 'b_country_id'
            , 'ReceivesNews' => 'subscription'
            , 'PriceGroupId' => 'rex_group_id'
            , 'PriceGroupName' => 'rex_group_name'
        );
        $cus_data = array();
        foreach ($fields as $f => $name) {
            if (isset($Customer->$f) && (string)$Customer->$f && trim((string)$Customer->$f)) {
                $cus_data[$name] = trim((string)$Customer->$f);
            }
        }

        return $cus_data;
    }

    public function CustomerCreateUpdate($data)
    {
        try {
            Mage::log("POS:: Retail.php > CustomerCreateUpdate > Begin Function, Params: data=$data", 'possystem.log');
			$this->_initSoap();
            $CustomerXML = $this->_doXml(array("Customer" => $data), "Customers");
            $result = self::$soap->CustomerCreateUpdate(array('CustomerXML' => $CustomerXML));
			Mage::log("POS:: Retail.php > CustomerCreateUpdate > XML to send: result=$result", 'possystem.log');
            if (!is_object($result) || !$result->CustomerCreateUpdateResult || !$result->CustomerCreateUpdateResult->any) {
                throw new Exception("Incorrect method 'CustomerCreateUpdateResult' response");
            }

            $XML = new SimpleXMLElement($result->CustomerCreateUpdateResult->any);
            if (!$XML) {
                throw new Exception("No XML in method 'CustomerCreateUpdateResult' response");
            }

            if ($XML->Error) {
                throw new Exception((string)$XML->Error);
            }

            if ($XML->Customer && $XML->Customer->CustomerId) {
                return (int)$XML->Customer->CustomerId;
            }

            throw new Exception("No customer tag in return 'CustomerCreateUpdateResult' response");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function OrderCreate($data)
    {
        try {
            $this->_initSoap();
            $OrderXML = $this->_doXml(array("Order" => $data), "Orders");

            $result = self::$soap->OrderCreate(array('OrderXML' => $OrderXML));
            if (!is_object($result) || !$result->OrderCreateResult || !$result->OrderCreateResult->any) {
                throw new Exception("Incorrect method 'OrderCreateResult' response");
            }

            $XML = new SimpleXMLElement($result->OrderCreateResult->any);
            if (!$XML) {
                throw new Exception("No XML in method 'OrderCreateResult' response");
            }

            if ($XML->Error) {
                throw new Exception((string)$XML->Error);
            }

            $return = array();
            if (!$XML->OrderCreate || !$XML->OrderCreate->Order || !$XML->OrderCreate->Order->OrderId) {
                throw new Exception("No OrderId in response");
            }

            $return['order_id'] = (string)$XML->OrderCreate->Order->OrderId;
            if ($XML->OrderCreate->Customer && $XML->OrderCreate->Customer->CustomerId) {
                $return['customer_id'] = (string)$XML->OrderCreate->Customer->CustomerId;
            }

            return $return;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function _doXml($data, $rootNodeName = 'data', $xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set ('zend.ze1_compatibility_mode', 0);
        }

        if (is_null($xml)) {
            $xml = simplexml_load_string("<$rootNodeName></$rootNodeName>");
        }

        // loop through the data passed in.
        foreach($data as $key => $value) {
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                $this->_doXml($value, $rootNodeName, $xml);
            } else {
                // delete any char not allowed in XML element names
                $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

                // if there is another array found recrusively call this function
                if (is_array($value))
                {
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
                }
                else
                {
                    // add single node.
                    $value = htmlentities($value);
                    $xml->addChild($key,$value);
                }
            }

        }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }

    public function OrdersGetHistory($id, $order_id = false)
    {
        try {
            $this->_initSoap();
            $result = self::$soap->OrdersGetHistory(array('CustomerId' => $id, "WebOrdersOnly" => '1'));
            if (!is_object($result) || !$result->OrdersGetHistoryResult || !$result->OrdersGetHistoryResult->any) {
                throw new Exception("Incorrect method 'OrdersGetHistoryResult' response");
            }

            $any = preg_replace('|<xs:schema.*</xs:schema>|', '', $result->OrdersGetHistoryResult->any);
            $XML = new SimpleXMLElement($any);
            if (!$XML) {
                throw new Exception("No XML in method 'OrdersGetHistoryResult' response");
            }

            if ($XML->Error) {
                throw new Exception((string)$XML->Error);
            }

            $return = array();
            if ($XML->Order) {

                foreach ($XML->Order as $o) {
                    if ($order_id) {
                        if ((string)$o->OrderId != $order_id) {
                            continue;
                        } else {
                            $mapping = array(
                                'OrderId' => 'real_order_id',
                                'DateCreated' => 'created_at',
                                'OrderTotal' => 'grand_total',
                                'FreightTotal' => 'shipping_amount',
                                'OrderStatus' => 'status_label'
                            , 'DelName' => 's_firstname'
                            , 'DelAddress' => 's_address'
                            , 'DelCompany' => 's_company'
                            , 'DelPhone' => 's_telephone'
                            , 'DelPostCode' => 's_postcode'
                            , 'DelSuburb' => 's_city'
                            , 'DelState' => 's_region'
                            , 'DelCountry' => 's_country_id'
                            , 'BillName' => 'b_firstname'
                            , 'BillEmail' => 'email'
                            , 'BillPhone' => 'b_telephone'
                            , 'BillFax' => 'b_fax'
                            , 'BillCompany' => 'b_company'
                            , 'BillAddress' => 'b_address'
                            , 'BillSuburb' => 'b_city'
                            , 'BillState' => 'b_region'
                            , 'BillPostCode' => 'b_postcode'
                            , 'BillCountry' => 'b_country_id'
                            );
                            foreach ($mapping as $k => $v) {
                                if (isset($o->$k) && $o->$k) {
                                    $return[$v] = (string)$o->$k;
                                }
                            }
                            $return['items'] = array();
                            foreach ($XML->OrderDetail as $d) {
                                if ((string)$d->OrderId != $order_id) {
                                    continue;
                                }

                                $return['items'][] = array(
                                    'product_id' => (string)$d->ProductId,
                                    'price' => (string)$d->UnitPrice,
                                    'qty' => (string)$d->QtyOrdered,
                                    'name' => (string)$d->ItemDescription,
                                    'qty_fulfilled' => (string)$d->QtyFulfilled,
                                );
                            }
                            foreach ($XML->OrderPayments as $d) {
                                if ((string)$d->OrderId != $order_id) {
                                    continue;
                                }

                                $return['pay'] = (string)$d->MethodId;
                            }
                            return $return;
                        }
                    }
                    $data = array(
                        'order_id' => '',
                        'created_at' => '',
                        'billing_name' => '',
                        'shipping_name' => '',
                        'grand_total' => '',
                    );
                    if ($o->OrderId) {
                        $data['order_id'] = (string)$o->OrderId;
                    }
                    if ($o->DateCreated) {
                        $data['created_at'] = str_replace('T', ' ', $o->DateCreated);
                    }
                    if ($o->BillName) {
                        $data['billing_name'] = (string)$o->BillName;
                    }
                    if ($o->DelName) {
                        $data['shipping_name'] = (string)$o->DelName;
                    }
                    if ($o->OrderStatus) {
                        $data['order_status'] = (string)$o->OrderStatus;
                    }
                    if ($o->PublicComments) {
                        $data['public_comment'] = (string)$o->PublicComments;
                    }
                    $grand_total = 0;
                    if ($o->FreightTotal) {
                        $data['freight_total'] = (string)$o->FreightTotal;
                        $grand_total += (string)$o->FreightTotal;
                    }
                    if ($o->OrderTotal) {
                        $data['order_total'] = (string)$o->OrderTotal;
                        $grand_total += (string)$o->OrderTotal;
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
            $PaymentXML = $this->_doXml(array("OrderPayment" => $data), "OrderPayments");

            $result = self::$soap->OrderAddPayment(array('OrderPaymentXML' => $PaymentXML));
            if (!is_object($result) || !$result->OrderAddPaymentResult || !$result->OrderAddPaymentResult->any) {
                throw new Exception("Incorrect method 'OrderAddPaymentResult' response");
            }

            $XML = new SimpleXMLElement($result->OrderAddPaymentResult->any);
            if (!$XML) {
                throw new Exception("No XML in method 'OrderAddPaymentResult' response");
            }

            if ($XML->Error) {
                throw new Exception((string)$XML->Error);
            }

            if (!$XML->Payment || !$XML->Payment->Result) {
                throw new Exception("No Result in response");
            }

            return (string)$XML->Payment->Result;
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function orderCancel($order_id)
    {
        try {
            $this->_initSoap();
            $result = self::$soap->OrderCancel(array('OrderId' => $order_id));
            if (!is_object($result) || !$result->OrderCancelResult || !$result->OrderCancelResult->any) {
                throw new Exception("Incorrect method 'OrderCancelResult' response");
            }

            $XML = new SimpleXMLElement($result->OrderCancelResult->any);
            if (!$XML) {
                throw new Exception("No XML in method 'OrderCancelResult' response");
            }

            if (!$XML->Result) {
                throw new Exception("No Result in response");
            }

            return (string)$XML->Result;
        } catch (Exception $e) {
            throw $e;
        }
    }

}