<?php

/**
 * POS_System_Model_Diagnostic.
 *
 * Model and DB Related methods for sync_diagnostic_list table
 *
 * @author chris@retailexpress.com.au
 */
class POS_System_Model_Diagnostic extends Mage_Core_Model_Abstract
{
    protected $configCollection = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('retailexpress/diagnostic');
    }

    /**
     * Get Section method.
     *
     * Gets the different configuration sections from sync_diagnostic table
     *
     * @return array()
     */
    public function _getDiagnosticSections()
    {
        $collection = $this->getCollection();
        $collection->addFieldToSelect('section_name')
                        ->addFieldToSelect('section')
                        ->getSelect()
                        ->distinct(true);

        return $collection;
    }

    /**
     * Get Diagnostic data.
     *
     * This method gets data from sync_diagnostic table and joins from core_config_data table
     * to get the values of the core config data. Then process the data grouped by sections
     *
     * @return array()
     */
    public function _getDiagnosticData()
    {
        $result = array();
        $data = $this->_loadData();
        foreach ($data as $c) {
            $result[$c->section][] = array(
                'name' => $c->name,
                'path' => $c->path,
                'value' => $c->value,
                'id' => $c->list_id,
                'last_status' => $c->last_status,
//                'name'  => $c['name'],
//                'path'  => $c['path'],
//                'value' => $c['value'],
//                'id'    => $c['list_id'],
            );
        }

        return $result;
    }

    public function _getDiagnosticAjaxData()
    {
        $result = array();
        $data = $this->_loadData();
        foreach ($data as $c) {
            $result[] = $c->list_id;
        }

        return $result;
    }

    protected function _loadData()
    {
        if ($this->configCollection === null) {
            $collection = $this->getCollection();
//            $collection->addFieldToSelect(array('list_id','section','section_name','name','type','path','last_status'));
            $collection->addFieldToSelect('*');
            $collection->getSelect()
                ->joinLeft(['c' => 'core_config_data'], 'main_table.path = c.path', 'c.*');
            $collection->addOrder('main_table.order', Varien_Data_Collection::SORT_ORDER_ASC);
            $this->configCollection = $collection;
        }

        return $this->configCollection;
    }

    public function runDiagnostic($type, $path = null, $value = null)
    {
        return $this->_getStatus($type, $path, $value);
    }
    /**
     * get Status method.
     *
     * checks the status of each config path and returns the appropriate status
     *
     * There are 3 types of $status => success, fail and warning
     *
     * There will be a parameter $type that will be used to determine the methods to be used
     * for checking/verifying the data
     * yes_no -> for checking that can be checked by 1 or 0 only.
     * custom -> for data that needs customized function ex. wsdl verification
     * has_value -> if the data can be checked if the data is blank or not
     * dropdown_attributes -> for attributes checking
     *
     * @param string $path, $value, $type
     *
     * @return string
     */
    protected function _getStatus($type, $path, $value)
    {
        //status values => success, fail, warning
        $status = '';

        switch ($type) {
            case 'yes_check':
                $status = $this->_checkYesValue($value);
                break;

            case 'no_check':
                $status = $this->_checkNoValue($value);
                break;

            case 'has_value':
                $status = $this->_checkIfHasValues($value);
                break;

            case 'soap_request':
                $methodToCall = 'ProductGetDetailsStockPricing';
                $params = array(
                    'ProductId' => '124001',
                    'CustomerId' => 0,
                    'PriceGroupId' => '',
                );

                $status = $this->_callSoapRequest($methodToCall, $params);

                break;

            case 'check_attribute_count':

                $attributes = array(
                    'pos_sizes' => Mage::getStoreConfig('retailexpress/attr/rex_sizes'),
                    'pos_colours' => Mage::getStoreConfig('retailexpress/attr/rex_colours'),
                    'pos_seasons' => Mage::getStoreConfig('retailexpress/attr/rex_seasons'),
                    'pos_product_types' => Mage::getStoreConfig('retailexpress/attr/rex_product_types'),
                    'pos_brands' => Mage::getStoreConfig('retailexpress/attr/rex_brands'),
                );

                $status = $this->_checkPosAttributeCount($attributes);

                break;

            case 'check_payment_methods':

                $status = $this->_checkPaymentMethod();

                break;

            case 'get_magento_version':

                list($version, $major) = explode('.', Mage::getVersion());

                $status = ($version == 1 && $major == 7) ? 'success' : 'fail';

                break;

            case 'get_cron':

                $status = $this->_getCronStatus();

                break;

            case 'check_permissions':

                //check if var/retail and all its sub folders has 777 permissions

                $directory = Mage::getBaseDir().'/var/retail/';
                $status = (is_dir_writeable($directory)) ? 'success' : 'fail';

                break;

            case 'check_permissions_tmp':

                //check if var/retail and all its sub folders has 777 permissions
                //check if var/retail and all its sub folders has 777 permissions
                if (function_exists('sys_get_temp_dir')) {
                    $directory = sys_get_temp_dir();
                } else {
                    $directory = '/tmp/';
                }

                $directory = '/tmp/';
                $status = (is_dir_writeable($directory)) ? 'success' : 'fail';

                break;

            case 'check_soap':

                //check if soap extension is enabled or installed on PHP
                $extension = 'soap';

                $status = $this->_checkExtension($extension);

                break;

            case 'check_gz':

                $status = $this->_checkGzip();

                break;

            case 'check_url_redirect':

                $status = $this->_checkUrlRedirect();

                break;

            case 'check_ssl':

                $status = $this->_verifySsl();

                break;

            case 'store_information':

                $storeInfoArray = array(
                    'storeName' => Mage::getStoreConfig('general/store_information/name'),
                    'storeTelephone' => Mage::getStoreConfig('general/store_information/phone'),
                    'storeAddress' => Mage::getStoreConfig('general/store_information/address'),
                );

                $status = $this->_getStoreInfo($storeInfoArray);

                break;

            case 'check_store_email_general':

                $storeInfoArray = array(
                    'senderName' => Mage::getStoreConfig('trans_email/ident_general/name'),
                    'senderEmail' => Mage::getStoreConfig('trans_email/ident_general/email'),
                );

                $status = $this->_checkArrayValues($storeInfoArray);

                break;

            case 'check_store_email_sales':

                $storeInfoArray = array(
                    'senderName' => Mage::getStoreConfig('trans_email/ident_sales/name'),
                    'senderEmail' => Mage::getStoreConfig('trans_email/ident_sales/email'),
                );

                $status = $this->_checkArrayValues($storeInfoArray);

                break;

            case 'check_store_email_support':

                $storeInfoArray = array(
                    'senderName' => Mage::getStoreConfig('trans_email/ident_support/name'),
                    'senderEmail' => Mage::getStoreConfig('trans_email/ident_support/email'),
                );

                $status = $this->_checkArrayValues($storeInfoArray);

                break;

            case 'check_shipping_settings':

                $shippingInfoArray = array(
                    'countryId' => Mage::getStoreConfig('shipping/origin/country_id'),
                    'regionId' => Mage::getStoreConfig('shipping/origin/region_id'),
                    'postcode' => Mage::getStoreConfig('shipping/origin/postcode'),
                    'city' => Mage::getStoreConfig('shipping/origin/city'),
                    'checkoutMultiple' => Mage::getStoreConfig('shipping/option/checkout_multiple'),
                    'checkoutMultipleMax' => Mage::getStoreConfig('shipping/option/checkout_multiple_maximum_qty'),
                );

                $status = $this->_checkArrayValues($shippingInfoArray);

                break;

            case 'check_active_payment_method':

                $status = $this->_countActivePaymentMethods();

                break;

            case 'get_product_count':

                $status = $this->_getTotalProducts();

                break;

            case 'get_product_image':

                $status = $this->_getMaxImageSize();

                break;

            case 'get_compilation_status':

                $status = $this->_getCompilationStatus();

                break;

            case 'custom':
                switch ($path) {
                    case 'retailexpress/main/url':
                        $status = ($this->_checkWsdl($value)) ? 'success' : 'fail';
                        break;

                    case 'retailexpress/main/sync_type':
                        $status = ($value == 'import') ? 'success' : 'warning';
                        break;

                    case 'retailexpress/main/sync_new':
                        $status = ($value) ? 'success' : 'warning';
                        break;

                    case 'retailexpress/attr/rex_sizes':
                        $status = $this->_checkDropdownValues($value, 'pos_sizes');
                        break;

                    case 'retailexpress/attr/rex_colours':
                        $status = $this->_checkDropdownValues($value, 'pos_colours');
                        break;

                    case 'retailexpress/attr/rex_seasons':
                        $status = $this->_checkDropdownValues($value, 'pos_seasons');
                        break;

                    case 'retailexpress/attr/rex_product_types':
                        $status = $this->_checkDropdownValues($value, 'pos_product_types');
                        break;

                    case 'retailexpress/attr/rex_brands':
                        $status = $this->_checkDropdownValues($value, 'pos_brands');
                        break;

                    case 'checkout/options/guest_checkout':
                        $status = $value == null ? ($this->_checkYesValue($value) ? 'success' : 'fail') : 'fail';
                        break;

                    case 'payment/ccsave/active':
                        $status = $value == null ? ($this->_checkYesValue($value) ? 'success' : 'fail') : 'fail';
                        break;

                    default:
                        $status = 'fail';
                        break;

                }
        }

        return $status;
    }

    /**
     * _checkWsdl method.
     *
     *
     * checks the URL of the given WSDL is existing or valid
     *
     * @param String $url
     *
     * @return Boolean
     */
    protected function _checkWsdl($url)
    {
        //check the url using soap method
        try {
            $soap = new SoapClient($url, ['exceptions' => true]);
        } catch (SoapFault $e) {
            return false;
        }

        return true;
    }

    /**
     * _checkYesNoValues method.
     *
     * Validates the parameter sent if 1 or 0
     *
     * @param bool $value
     *
     * @return string
     */
    protected function _checkYesValue($value)
    {
        return ($value == 1) ? 'success' : 'fail';
    }

    /**
     * _checkYesNoValues method.
     *
     * Validates the parameter sent if 1 or 0
     *
     * @param bool $value
     *
     * @return string
     */
    protected function _checkNoValue($value)
    {
        return ($value == 0) ? 'success' : 'fail';
    }

    /**
     * _checkIfHasValues method.
     *
     * Validates the parameter sent if blank or not
     *
     * @param string $value
     *
     * @return string
     */
    protected function _checkIfHasValues($value)
    {
        return (trim($value) != '') ? 'success' : 'fail';
    }

    /**
     * _checkDropdownValues method.
     *
     * Validates the parameter if the sent attributes are selected correctly
     *
     * @param string $value
     *
     * @return string
     */
    protected function _checkDropdownValues($value, $attribute)
    {
        if ($value == $attribute) {
            $status = 'success';
        } elseif (trim($value) != '' && $value != $attribute) {
            $status = 'fail';
        } else {
            $status = 'warning';
        }

        return $status;
    }

    /**
     * _callSoapRequest.
     *
     * This method tries to call a soap request and check if the soap request is valid or not
     *
     * @param $method String
     * @param $params array
     *
     * @return string
     */
    protected function _callSoapRequest($method, $params)
    {
        $url = Mage::getStoreConfig('retailexpress/main/url');

        $options = [
                    'soap_version' => SOAP_1_1,
                    'exceptions' => true,
                    'trace' => true,
                    ];

        if (!$url) {
            return 'fail';
        }

        try {
            $soap = new SoapClient($url, $options);

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

            $soap->__setSoapHeaders($header);

            $result = $soap->$method($params);

            if (!is_object($result)) {
                return 'fail';
            } else {
                return 'success';
            }
        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return 'fail';
        }
    }

    /**
     * _checkPosAttributeCount.
     *
     *
     * This method checks each one of the POS attributes (size, color, product type, season, brand), every attribute assigned must be unique
     * if one attribute has duplicate values, this method will return false
     *
     * @param $attributesArray array
     *
     * @return string
     */
    protected function _checkPosAttributeCount($attributesArray)
    {
        //check for duplicated values  from the array.
        $attributesCount = array_count_values($attributesArray);

        foreach ($attributesCount as $key => $value) {
            if ($value > 1) {
                return 'fail';
            }
        }

        return 'success';
    }

    /**
     * _checkPaymentMethod.
     *
     *
     * This method checks all payment methods in the POS configuration
     * if one of the payment method is null or has no value, this will return warning
     *
     * @return string
     */
    protected function _checkPaymentMethod()
    {
        $paymentMethods = [
                            'free' => Mage::getStoreConfig('retailexpress/payments/free'),
                            'googlecheckout' => Mage::getStoreConfig('retailexpress/payments/googlecheckout'),
                            'paypal_billing_agreement' => Mage::getStoreConfig('retailexpress/payments/paypal_billing_agreement'),
                            'paypal_mep' => Mage::getStoreConfig('retailexpress/payments/paypal_mep'),
                            'secureXml' => Mage::getStoreConfig('retailexpress/payments/secureXml'),
                        ];
        foreach ($paymentMethods as $payment) {
            if ($payment == null || trim($payment) == '') {
                return 'fail';
            }
        }

        return 'success';
    }

    /**
     * _getFolderPermissions.
     *
     *
     * This method checks the folder permissions of the  given directory folder and returns the folder permissions on octal value (ex: 0777)
     *
     * @param $directory string
     *
     * @return int
     */
    protected function _getFolderPermissions($directory)
    {
        return substr(sprintf('%o', fileperms($directory)), -4);
    }

    /**
     * _checkExtension.
     *
     *
     * This method checks the php extension if installed or not
     *
     * @param $extension string
     *
     * @return string
     */
    protected function _checkExtension($extension)
    {
        if (extension_loaded($extension)) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    /**
     * _checkGzip.
     *
     *
     * This method checks if the gzip module is installed and enabled for PHP
     *
     * @return string
     */
    protected function _checkGzip()
    {
        //if(function_exists('ob_gzhandler') && ini_get('zlib.output_compression'))
        if (function_exists('ob_gzhandler')) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    protected function _verifySsl()
    {
        if (isset($_SERVER['HTTPS'])) {
            return 'success';
        } else {
            //check if payment method other than direct deposit is enabled
            $payments = Mage::getSingleton('payment/config')->getActiveMethods();

            foreach ($payments as $paymentCode => $paymentModel) {
                $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');

                if ($paymentTitle != 'directdeposit_au') {
                    $methods[$paymentCode] = [
                        'label' => $paymentTitle,
                        'value' => $paymentCode,
                    ];
                }
            }

            if (count($methods) > 0) {
                return 'fail';
            } else {
                return 'warning';
            }
        }
    }

    /**
     * _getStoreInfo.
     *
     *
     * Checks the store info from System > Configuration > General > General > Store Information
     * If all of the given array values are not null, return success, if all values are null return fail,
     * then if one of the values is null return warning
     *
     * @return string
     */
    protected function _getStoreInfo($storeInfoArray)
    {
        $nullcount = 0;

        foreach ($storeInfoArray as $key => $value) {
            if (trim($value) == '') {
                ++$nullcount;
            }
        }

        if ($nullcount == 0) {
            return 'success';
        } elseif ($nullcount == 3) {
            return 'fail';
        } else {
            return 'warning';
        }
    }

    /**
     * _checkArrayValues.
     *
     *
     * This method checks the values of the given array,
     * if one of the values is null or blank, it will return fail else will return success
     *
     * @param $storeInfoArray array
     *
     * @return string
     */
    protected function _checkArrayValues($storeInfoArray)
    {
        if (empty($storeInfoArray)) {
            return 'fail';
        }

        foreach ($storeInfoArray as $key => $value) {
            if (trim($value) == '') {
                return 'fail';
            }
        }

        return 'success';
    }

    /**
     * _countActivePaymentMethods.
     *
     *
     * This method checks the active payment methods
     * if at least one payment method is enabled, this will return success
     *
     * @return string
     */
    protected function _countActivePaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');

            $methods[$paymentCode] = [
                'label' => $paymentTitle,
                'value' => $paymentCode,
            ];
        }

        if (count($methods) > 0) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    /**
     * _getTotalProducts.
     *
     *
     * This method gets the total number of products and validates
     * if total products < 15000, return success
     * if total products > 20000, return fail
     * if total products > 15k and < 20k , return warning
     *
     * @return String
     */
    protected function _getTotalProducts()
    {
        $count = Mage::getModel('catalog/product')->getCollection()->getSize();

        if ($count < 15000) {
            return 'success';
        } elseif ($count > 20000) {
            return 'fail';
        } else {
            return 'warning';
        }
    }

    /**
     * _getMaxImageSize.
     *
     * This method gets the individual images and checks the filesize
     * if the image size > 500KB return fail
     * if the image size > 300KB return warning
     * else return success
     */
    protected function _getMaxImageSize()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */

        /*
         * TODO: what to do with this code. How to improove it?
         */

        $collection = Mage::getModel('catalog/product')->getCollection();

        foreach ($collection as $product) {
            /** @var Mage_Catalog_Model_Product $p */
            $p = $product->load($product->getId());

            if ($p->getImage() && is_file(Mage::getBaseDir('media').'/catalog/product/'.$p->getImage())) {
                //get the file size of the image and convert to KB format
                $imagesize = round(filesize(Mage::getBaseDir('media').'/catalog/product/'.$product->getImage()) / 1024, 2);

                if ($imagesize > 500) {
                    return 'fail';
                } elseif ($imagesize > 300) {
                    return 'warning';
                }
            }
            $product = null;
        }

        return 'success';
    }

    /**
     * get Cron Status.
     *
     *
     * This method checks for cron logs, returns success if log is present
     *
     * @return String
     */
    protected function _getCronStatus()
    {
        $file = Mage::getBaseDir('var').DS.'log'.DS.'cron.log';

        if (file_exists($file)) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    /**
     * Check URL Redirect.
     *
     *
     * Check if the base URL has a redirect method
     */
    protected function _checkUrlRedirect()
    {
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        //$url  = 'http://rentnet.com';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, true);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_URL, $url);

        $out = curl_exec($ch);

        // line endings is the wonkiest piece of this whole thing
        $out = str_replace("\r", '', $out);

        // only look at the headers
        $headers_end = strpos($out, "\n\n");
        if ($headers_end !== false) {
            $out = substr($out, 0, $headers_end);
        }

        $headers = explode("\n", $out);
        foreach ($headers as $header) {
            if (substr($header, 0, 10) == 'Location: ') {
                $target = substr($header, 10);

                return 'fail';

                continue;
            }
        }

        return  'success';
    }

    /**
     * get Compilation Status.
     *
     *
     * this method checks the compilation status on magento
     *
     * @return String
     */
    protected function _getCompilationStatus()
    {
        $compilerConfig = Mage::getBaseDir('base').DS.'includes/config.php';

        if (file_exists($compilerConfig)) {
            include $compilerConfig;

            $status = defined('COMPILER_INCLUDE_PATH') ? 'fail' : 'success';
        } else {
            $status = 'success';
        }

        return $status;
    }
}
