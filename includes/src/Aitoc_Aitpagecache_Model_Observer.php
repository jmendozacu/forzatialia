<?php
/**
 * Magento Booster 1.4+
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpagecache
 * @version      4.0.5
 * @license:     AACcewAJ3nZYMUsItZcwugZ3g4HsbQPMHWb0Pv6oyc
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpagecache_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_cookieId = 'aitpagecache';
    protected $_cookieAdminId = 'aitadmpagecache';
    protected $_helper = null;

    protected $sCacheConfig = '';
    protected $_cookieConfigPath = '';
    protected $_overrideConfig = null; //used after config values is updated in backend
    protected $_noRequiredConfigValues = array(
        'disallow_clear_cache_level',
        'disallow_bots_level',
        'block_exclude',
    );
    protected $_allowedFieldsFromConfig = array(
        'cookie_restriction'
    );

    public function __construct() {
        $this->sCacheConfig = Mage::app()->getConfig()->getOptions()->getBaseDir() . DS . 'magentobooster' . DS . 'use_cache.ser';
        $this->_cookieConfigPath = Mage::app()->getConfig()->getOptions()->getBaseDir() . DS . 'magentobooster' . DS . 'cookie_config.ser';
        parent::__construct();
    }

    protected function _helper() {
        if(is_null($this->_helper)) {
            $this->_helper = Mage::helper('aitpagecache');
        }
        return $this->_helper;
    }

    protected function _getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    public function customer_login($observer) {
        if(false == $this->_helper()->isEnabledForLogined()) {//if booster for logined is disabled
            $this->_helper()->setCacheCookie($this->_cookieId, $this->_cookieId);
        }
        if($this->_helper()->isEnabledForQuote()) {
            //checking amount of items in cart and setting cookie is needed
            $this->_helper()->checkQuoteItems('');
        }
        return $this;
    }

    public function customer_logout($observer) {
        $this->_helper()->delCacheCookie($this->_cookieId)
            ->delCacheCookie( Aitoc_Aitpagecache_Mainpage::COOKIE_CART_ID )
            ->delCacheCookie( Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID );

        //for magento 1.6+ persistent cart
        if(isset($_COOKIE[Aitoc_Aitpagecache_Mainpage::PERSISTENT_COOKIE_ID]) && $_COOKIE[Aitoc_Aitpagecache_Mainpage::PERSISTENT_COOKIE_ID])
        {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $amount = $this->_helper()->countQuoteItems($quote);
            if($amount > 0)
            {
                $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CART_ID, $amount);
            }
        }

        return $this;
    }

    public function controller_action_predispatch($observer) {
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            if(false == $this->_helper()->isEnabledForLogined()) {
                $this->set_cache_cookie();
                return ;
            }
        }
        $moduleName = $observer->getControllerAction()->getRequest()->getModuleName();
        $controllerName = $observer->getControllerAction()->getRequest()->getControllerName();
        $actionName = $observer->getControllerAction()->getRequest()->getActionName();
        /**
         * set cookie on any dynamic action (add to wishlist, add to compare, newsletter, poll)
         */
        if($moduleName == 'poll' && $actionName == 'add') {
            $this->set_cache_cookie();
        }
        else if($moduleName == 'newsletter' && $actionName == 'new') {
            $this->set_cache_cookie();
        }
        else if($moduleName == 'directory' && $controllerName = 'currency' && $actionName == 'switch') {
            $this->set_cache_cookie();
        }
        return $this;
    }

    public function set_cache_cookie($observer = false) {
        $this->_helper()->setCacheCookie($this->_cookieId, $this->_cookieId);
    }


    public function recalculateQuoteItems($observer) {
        if($observer && ( ($observer->getQuoteItem() && $observer->getQuoteItem()->getQuote()) || $observer->getQuote()  )  ) {
            if(false == $this->_helper()->isEnabledForQuote()) {
                return $this;
            }
            $quote = $observer->getQuoteItem() ? $observer->getQuoteItem()->getQuote() : $observer->getQuote();
            $amount = $this->_helper()->countQuoteItems($quote);
            if(isset($_COOKIE[Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID])) {
                $this->_helper()->delCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID);
            }
            $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CART_ID, $amount);
        }
        return $this;
    }

    public function checkout_cart_product_add_after($observer) {
        if($this->_helper()->isEnabledForQuote()) {
            $this->recalculateQuoteItems($observer);
        } else {
            $this->_helper()->setCacheCookie($this->_cookieId, $this->_cookieId);
        }
        return $this;
    }

    public function admin_session_user_login_success($observer) {
        if(false == $this->_helper()->isEnabledForAdmin() || ($observer->getField()=='enable_for_admin' && $observer->getValue()==0)) {
            $this->_helper()->setCacheCookie($this->_cookieAdminId, $this->_cookieAdminId);
        }
        return $this;
    }

    public function controller_action_postdispatch_adminhtml_index_logout($observer) {
        $this->_helper()->delCacheCookie($this->_cookieAdminId);
    }

    public function addNoBoosterCookie($observer) {
        //observer to disable booster after quote is converted to order to prevent caching some payment pages, used until success pages is opened or other any allowed booster pageopened
        $this->_helper()->setCacheCookie(Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID,Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID);
    }

    public function checkNoBoosterCookie($observer) {
        if(!isset($_COOKIE[Aitoc_Aitpagecache_Mainpage::COOKIE_CHECKOUT_ID])) {
            return false;
        }
        $moduleName = $observer->getControllerAction()->getRequest()->getModuleName();
        //checking that we are still on checkout
        if(in_array($moduleName, array('checkout', 'paypal', 'twocheckout'/*paypall redirect*/, 'sagepaysuite', 'paypaluk', 'paygate')) ) { // update from config
            return false;
        }
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if($quote != null) {
            $observer->setQuote($quote);
            $this->recalculateQuoteItems($observer);
        }
    }

    public static function clearCache() {
        Mage::helper('aitpagecache')->clearCache(true);
    }

    public static function controller_action_postdispatch_adminhtml_cache_flushSystem() {
        Mage::helper('aitpagecache')->clearCache(false);
    }

    /**
    * added in version 2.0.1
    * admin cache manipulation
    */
    public function controller_action_predispatch_adminhtml_cache_massEnable($observer)
    {
        if(!$this->_helper()->hasAitpagecacheIndexFile())
        {
            foreach($_POST['types'] as $key=>$name)
            {
                if($name =='aitpagecache')
                {
                    unset($_POST['types'][$key]);
                }
            }
            $this->_helper()->throwUnrequiredIndexFileError();
            return $this;
        }
        $this->_changeConfigValue(1);
        $this->_updateCookieConfig();
        return $this;
    }

    public function controller_action_predispatch_adminhtml_cache_massDisable($observer)
    {
        return $this->_changeConfigValue(0);
    }

    /**
     * Used to update booster cache.ser file on some config changes in backend.
     * @param type $observer
     */
    public function magentoConfigChanged($observer)
    {
        $data = $observer->getConfigData();
        if(!$data) {
            $data = $observer->getDataObject();
        }
        if(!is_object($data) || $data->getField() == '' || $data->getScope()!='default') {
            //can't find any config object
            return;
        }
        if(!in_array($data->getField(), $this->_allowedFieldsFromConfig)) {
            //we need to update config only on some fields, don't need it on every config value in magento
            return;
        }
        if(is_null($this->_overrideConfig)) {
            //saving default booster values if they are not set
            $this->_overrideConfig = $this->_helper()->getBoosterConfig();
        }
        //saving new data to overrideArray, because it's may not be saved in database yet and Mage::getConfig() will return old value
        $this->_overrideConfig[$data->getField()] = $data->getValue();
        //updating cache file
        $this->_saveConfigvalue();
    }

    public function aitpagecacheConfigChanged($observer)
    {
        if($observer->getField()) {
            if(!$this->_saveConfigvalue( array($observer->getField() => /*(int)*/$observer->getValue() ))) {
                Mage::getSingleton('adminhtml/session')->addError($this->_helper()->__('Error while saving Magento Booster config. File %s is not writable.', $this->sCacheConfig));
            }
        }
    }

    protected function _changeConfigValue($toValue)
    {
        $cacheTypes = Mage::app()->getRequest()->getParam('types');

        if($cacheTypes)
        {
            if(in_array('aitpagecache', $cacheTypes))
            {
                // write use_cache.ser file like in Magento 1.3.* versions
                //if(!$this->_writeFileData($this->sCacheConfig, serialize(array('aitpagecache' => (int)$toValue))))
                if(!$this->_saveConfigValue( array('aitpagecache' => (int)$toValue) ) )
                {
                    Mage::getSingleton('adminhtml/session')->addError($this->_helper()->__('Error while disabling Magento Booster cache. File %s is not writable.', $this->sCacheConfig));

                    foreach($cacheTypes as $key => $value)
                    {
                        if($value == 'aitpagecache') {
                            unset($cacheTypes[$key]);
                        }
                    }
                    Mage::app()->getRequest()->setParam('types', $cacheTypes);
                }
            }
        }
        return $this;
    }

    protected function _saveConfigValue( $data = array() )
    {
        if(is_null($this->_overrideConfig)) {
            $this->_overrideConfig = $this->_helper()->getBoosterConfig();
        }
        if(!isset($data['aitpagecache'])) {
            $cache = $this->_helper()->getBooster()->getCacheConfigFile();
            if($cache !== null && isset($cache['aitpagecache'])) {
                $data['aitpagecache'] = $cache['aitpagecache'];
            }
        }
        $data = $this->_checkConfig($data, Aitoc_Aitpagecache_Mainpage::QUOTE_VALUE);
        $data = $this->_checkConfig($data, Aitoc_Aitpagecache_Mainpage::LOGIN_VALUE);
        $data = $this->_checkConfig($data, Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES);

        //Checking if cookie restrictions are set and used in magento. If they are set - another cache flag will be used on frontend
        $data = $this->_checkConfig($data, Aitoc_Aitpagecache_Mainpage::RESTRICTION_COOKIE);

        if(!isset($data[Aitoc_Aitpagecache_Mainpage::RESTRICTION_COOKIE])) {
            $cookie_restriction = (int)Mage::getStoreConfig('web/cookie/cookie_restriction');
            $data[Aitoc_Aitpagecache_Mainpage::RESTRICTION_COOKIE] = $cookie_restriction;
        }
        if( !empty($data[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES]) && !is_array($data[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES]) ) {
            //$disabled_admin_session = Mage::helper('aitpagecache')->getDisabledAdminSession(false);//array_map('trim', preg_split("/\n|,/", Mage::getStoreConfig('aitpagecache/config/disabled_admin_session')))
            $data[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES] = array_map('trim', preg_split("/\n|,/",$data[Aitoc_Aitpagecache_Mainpage::ADMIN_AREA_NAMES]));
        }
        //Checking some not required variables
        foreach($this->_noRequiredConfigValues as $key) {
            $data = $this->_checkConfig($data, $key);
        }

        return $this->_writeFileData($this->sCacheConfig, serialize($data));
    }

    protected function _checkConfig($data, $value) {
        if(isset($data[$value])) {
            //value is taken from function params and not changed
            $this->_overrideConfig[$value] = $data[$value];
        } elseif ( isset($this->_overrideConfig[$value]) ) {
            //taking value from config or previously updated config
            $data[$value] = $this->_overrideConfig[$value];
        }
        return $data;
    }

    protected function _writeFileData($file, $data)
    {
        try
        {
            return file_put_contents($file, $data);
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function controller_action_predispatch_adminhtml_cache_massRefresh($observer)
    {
        $cacheTypes = Mage::app()->getRequest()->getParam('types');
        if(in_array('aitpagecache', $cacheTypes))
        {
            // manually refresh cache data
            $this->_helper()->clearCache();
        }
        $this->_updateCookieConfig();
        return $this;
    }

    /**
     * clear cache if product out of stock
     */
    public function clearCurrentCache($observer) 
    {
        $bClearCache = false;
        $items = $observer->getOrder()->getAllItems();
        foreach ($items as $itemId => $item)
        {
            $product = Mage::getModel('catalog/product')->load($item->getData('product_id')); 
            if ($product->getStockItem()->getIsInStock()==0)
            {
                $bClearCache = true;
            }
        }
        if ($bClearCache === true)
        {
            $this->_helper()->clearCache(); 
        }
    }
    
    /**
     * clear cache when product or cms page edit or delete in adminhtml 
     */
    public function clearAdminCurrentCache()
    {
        $this->_helper()->clearCache(); 
    }
    
    public function blockRendered($observer)
    {
        $helper = $this->_helper();
        
        if (!$helper->isJSLoaderAllowed())
        {
            return;
        }
        
        $transport = $observer->getData('transport');
        $block = $observer->getData('block');
        
        $className = get_class($block);
        
        $disabledBlocks = (array) $helper->getDisabledCacheBlocks(false);

        if (in_array($className, $disabledBlocks))
        {        
            $transport->setData('html', '<span class="aitoc-aitpagecache-loadable-block" id="aitoc-aitpagecache-loadable-block-'. $className . '">' . $transport->getData('html') .  '</span>');
        }
    }
    
    /**
     * retrieve cookie domain data from magento config and put into .ser file like baseurl => cookie domain
     */
    protected function _updateCookieConfig()
    {
        //prepare data
        $data = array();
        $stores = Mage::app()->getStores();

        foreach ($stores as $store)
        {
            $unsecureBaseUrl = $store->getConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
            if(!key_exists($unsecureBaseUrl, $data))
            {
                $data[$unsecureBaseUrl] = $store->getConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN);
            }
            
            $secureBaseUrl = $store->getConfig(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL);
            if(!key_exists($secureBaseUrl, $data))
            {
                $data[$secureBaseUrl] = $store->getConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN);
            }
        }

        if(!$this->_writeFileData($this->_cookieConfigPath, serialize($data)))
        {
            Mage::getSingleton('adminhtml/session')->addNotice($this->_helper()->__('Error while refreshing Magento Booster cookies cache. File %s is not writable.', $this->_cookieConfigPath));
        }
        
        return $this;
    }
    
    public function webConfigSectionChanged()
    {
        return $this->_updateCookieConfig();
    }
    
    public function afterInstall($observer)
    {
        $isModuleInstalled = $observer->getAitocAitpagecache();
        if(!$isModuleInstalled)
        {
            return false;
        }    
        if(!$this->_helper()->hasAitpagecacheIndexFile())
        {
            $this->_helper()->throwUnrequiredIndexFileError();
        }     
    }
    
}