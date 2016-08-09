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
class Aitoc_Aitpagecache_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_config = null;
    
    //equal to system.xml fields
    const QUOTE_VALUE = 'enable_for_quote';
    const LOGIN_VALUE = 'enable_for_logined';
    //cookie ids, used in Observer|helper
    const COOKIE_CART_ID = 'aitpagecartitems';
    const COOKIE_CHECKOUT_ID = 'aitpagecartcheckout';
    const DEFAULT_COOKIE_ID = 'aitpagecache';
    
    public function setCacheCookie($cookieName, $cookieValue) {
        Mage::app()->getCookie()->set($cookieName, $cookieValue);
        return $this;
    }

    public function delCacheCookie($cookieName) {
        Mage::app()->getCookie()->delete($cookieName);
        return $this;
    }

    public function resetConfig()
    {
        $this->_config = null;
        return $this;
    }
    
    public function getBoosterConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = array(
                'enable_for_quote' => (bool)Mage::getStoreConfig('aitpagecache/config/enable_for_quote'),
                'enable_for_logined' => (bool)Mage::getStoreConfig('aitpagecache/config/enable_for_logined'),
                'enable_for_admin' => (bool)Mage::getStoreConfig('aitpagecache/config/enable_for_admin'),
            );
        }
        return $this->_config;
    }
    
    public function isEnabledForQuote() {
        $this->getBoosterConfig();
        return $this->_config['enable_for_quote'];
    }
    public function isEnabledForLogined() {
        $this->getBoosterConfig();
        return $this->_config['enable_for_logined'];
    }
    public function isEnabledForAdmin() {
        $this->getBoosterConfig();
        return $this->_config['enable_for_admin'];
    }

    /**
     *
     * @param Mage_Sales_Model_Quote $quote
     */
    public function countQuoteItems($quote) {
        $amount = 0;//items count may not be updated here and collection amount is not correct also
        foreach($quote->getItemsCollection() as $item) {
            if(!$item->isDeleted()) { //removed items is not deleted from collection
                $amount += $item->getQty(); //adding correct amount if items to cart
            }
        }
        return $amount;
    }


    public function emptyDir($dirname = null, $isCron)
    {
        
        if (is_dir($dirname)) {
            if ($handle = @opendir($dirname)) {
                while (($file = readdir($handle)) !== false) {
                    $this->_emptyFullPath($file, $dirname,  $isCron);
                }
                closedir($handle);
            }
        }
    }

     /**
    * 
    * @param str $fullpath
    * @param bool $isCron;
    * @return Aitoc_Aitpagecache_Helper_Data
    */ 
    protected function _emptyFullPath($file, $dirname, $isCron = false)
    {
        if ($file != "." && $file != "..") {
            $fullpath = $dirname . '/' . $file;
            if (is_dir($fullpath)) {
                $this->emptyDir($fullpath, $isCron);
                //@rmdir($fullpath);
            }
            else {
                $this->_unlinkFile($fullpath, $isCron);
            }
        }
        return $this;
    }
    
    /**
    * 
    * @param str $fullpath
    * @param bool $isCron ('true' if unlink old fail)
    * @return Aitoc_Aitpagecache_Helper_Data
    */ 
    protected function _unlinkFile($fullpath, $isCron = false)
    {
        $_time = time();
        $doDelete = true;
        if($isCron) {
            $period = ceil( ($_time - filemtime($fullpath)) / 60);
            if($period <= 15) {
                $doDelete = false;
            }
        }
        if($doDelete) {
            @unlink($fullpath);
        }
        return $this;
    }


    public function clearCache($isCron = false) {
        #Mage::log("refreshing cache");

        // clear cache only if aitpagecache enabled in admin
        if(Mage::getSingleton('core/cache')->canUse('aitpagecache')) {
            $mediaDir = Mage::getConfig()->getOptions()->getMediaDir();
            $pagesDir = $mediaDir . '/pages';
            $this->emptyDir($pagesDir, $isCron);

            Mage::getModel('aitpagecache/target')->removeAllPages();            
/*
            if(Mage::getStoreConfig('aitpagecache/config/flush_media_cache')) {
                // flush js cache
                $jsDir = $mediaDir . '/js';
                $this->emptyDir($jsDir);
                // flush css cache
                $cssDir = $mediaDir . '/css';
                $this->emptyDir($cssDir);
            }
*/
        }
    }


    public function saveContentToCache($cacheFilePath, &$fileContents)
    {        
        if (!$this->aitMaySaveCache())
        {
            return 'CACHING: FALSE. CACHE DISABLED';
        }

            // @param filePath like 'media/pages/[0-9a-f]/filename.html'

        $subPagesDir = dirname($cacheFilePath);
        $pagesDir    = dirname($subPagesDir);
        
        if(!$pagesDir) {
            return  'CACHING: FALSE. DYNAMIC PAGE LOAD';
        }
        
        if(!is_dir($pagesDir)) {
            mkdir($pagesDir, 0777, true);//set $recursive = true because the dir 'page/mobail' not create if dir 'page' not exists
        }

        if(!is_dir_writeable($pagesDir)) {
            return  'CACHING: FALSE. CHECK PERMISSIONS ON DIRECTORIES ON `' . $pagesDir . '`';
        }
        
        if(!is_dir($subPagesDir)) {
            mkdir($subPagesDir, 0777, true);
        }

        if(!is_dir_writeable($subPagesDir)) {
            return  'CACHING: FALSE. CHECK PERMISSIONS ON DIRECTORIES ON `' . $subPagesDir . '`';
        }

        //see headers, find 404
        $is404 = false;
        $codes = Mage::app()->getResponse()->getHeaders();
        foreach($codes as $header)
            if ($header['name'] == 'Status' or $header['name'] == 'HTTP/1.1')
            {
                if(substr($header['value'],0,3) == '404')
                {
                    $is404 = true;
                    $cacheFilePath = Aitoc_Aitpagecache_Mainpage::get404Name($cacheFilePath);;
                    break;
                }
            }

        file_put_contents($cacheFilePath, $fileContents, LOCK_EX);
        chmod($cacheFilePath, 0777);
        return 'CACHING: TRUE';
    }



    public function isCacheEnabled()
    {
            $return = false;

            foreach (Mage::app()->getCacheInstance()->getTypes() as $type)
            {
                    if ('aitpagecache' == $type->getId())
                    {

                            $return = $type->getStatus();
                    }
            }

            return (bool) $return;
    }
    
    public function getDisabledCacheBlocks($toJson = true)
    {
        $result = array_map('trim', preg_split("/\n|,/", Mage::getStoreConfig('aitpagecache/config/disabled_blocks_cache')));
        return $toJson ? json_encode($result) : $result;    	
    }

    public function getDisabledAdminSession($toJson = true)
    {
        $result = array_map('trim', preg_split("/\n|,/", Mage::getStoreConfig('aitpagecache/config/disabled_admin_session')));
        return $toJson ? json_encode($result) : $result;
    }
    public function isJSLoaderAllowed()
    {		
        return $this->isCacheEnabled() && $this->aitMayCachePage();
    }
    
    public function getBooster()
    {
        return Aitoc_Aitpagecache_Mainpage::getInstance(Mage::getBaseDir());
    }

    public function getMonitor()
    {
        $booster = $this->getBooster();
        if(!$booster->getMonitor()->isEnabled()) {
            return false;
        }
        return $booster->getMonitor();
    }
    
    public function isMonitorEnabled()
    {
        if(!$this->getMonitor()) {
            return false;
        }
        return $this->isModuleEnabled('Aitoc_Aitloadmon');
    }

    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
            return false;
        }

        $isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }
    
    public function aitMayCachePage()
    {
        $booster = $this->getBooster();
        return $booster->canCachePage();
    }

    public function checkQuoteItems($defaultUrl) {
        $booster = $this->getBooster();
        return $booster->checkQuoteItems($defaultUrl);
    }

    /** 
        * Checks if module have permissions to save pages to cache
        * according to Layered Navigation request parameters
        * 
        * @return boolean
        */

    final public function aitMaySaveCache()
    {
        if (!Mage::registry('adjustware_layered_navigation_view'))
        {
            return true;
        }

        $helper = Mage::helper('adjnav');

        // If Layered Navigation extension is not installed
        // we should allow caching

        if (!$helper instanceof AdjustWare_Nav_Helper_Data)
        {
                return true;		
        }		

        $params = (array) $helper->getParams();		

        if (isset($params['dir']))
        {
                unset($params['dir']);
        }	

        if ($params)
        {
                return false;		
        }

        return true;
    }

    public function isValidCronExpr($value = '')
    {
        if(!strstr($value, ' '))
            return false;
            
        $parts = explode(' ', trim($value));
        if(count($parts) !== 5)
            return false;
        
        
            
        foreach($parts as $value)
        {
            if(preg_match("/[^\*,\d,\/,\,,\-,W,L]/", $value))
                return false;                
        }

        if (!$this->_ifMaskPregMatch($parts[0], '(([0-9])|([1-5][0-9]?)|60)', '(([1-9])|([1-5][0-9]?)|60)'))
            return false;
        
        if (!$this->_ifMaskPregMatch($parts[1], '(([0-9])|(1[0-9])|2[0-3])', '(([1-9])|(1[0-9])|2[0-3])'))
            return false;
        
        if (!$this->_ifMaskPregMatch($parts[2], '(([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?)'))
            return false;
        
        if (!$this->_ifMaskPregMatch($parts[3], '((1[0-2]?)|([2-9]{1}))'))
            return false;
        
        if (!$this->_ifMaskPregMatch($parts[4], '[0-7]L?'))
            return false;
        /*if($parts[0] == '*' or 
            preg_match("/^(([0-9])|([1-5][0-9]?)|60)$/", $parts[0]) or 
            preg_match("/^\*\/(([1-9])|([1-5][0-9]?)|60)$/", $parts[0]) or 
            preg_match("/^(([0-9])|([1-5][0-9]?)|60)(\,(([0-9])|([1-5][0-9]?)|60))*$/", $parts[0]) or 
            preg_match("/^(([0-9])|([1-5][0-9]?)|60)\-(([0-9])|([1-5][0-9]?)|60)(\,(([0-9])|([1-5][0-9]?)|60)(\-(([0-9])|([1-5][0-9]?)|60))*)*$/", $parts[0]))
            {}
        else return false;
        if($parts[1] == '*' or 
            preg_match("/^(([0-9])|(1[0-9])|2[0-3])$/", $parts[1]) or 
            preg_match("/^\*\/(([1-9])|(1[0-9])|2[0-3])$/", $parts[1]) or 
            preg_match("/^(([0-9])|(1[0-9])|2[0-3])(\,(([0-9])|(1[0-9])|2[0-3]))*$/", $parts[1]) or 
            preg_match("/^(([0-9])|(1[0-9])|2[0-3])\-(([0-9])|(1[0-9])|2[0-3])(\,(([0-9])|(1[0-9])|2[0-3])(\-(([0-9])|(1[0-9])|2[0-3]))*)*$/", $parts[1]))
            {}
        else return false;
        if($parts[2] == '*' or 
            preg_match("/^(([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?)$/", $parts[2]) or 
            preg_match("/^\*\/((([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?))$/", $parts[2]) or 
            preg_match("/^((([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?))(\,((([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?)))*$/", $parts[2]) or 
            preg_match("/^((([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?))\-((([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?))(\,((([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?))(\-((([1-9])|(1[0-9])|2[0-9]|3[0-1])(W?|L?)))*)*$/", $parts[2]))
            {}
        else return false;
        if($parts[3] == '*' or 
            preg_match("/^((1[0-2]?)|([2-9]{1}))$/", $parts[3]) or 
            preg_match("/^\*\/((1[0-2]?)|([2-9]{1}))$/", $parts[3]) or 
            preg_match("/^((1[0-2]?)|([2-9]{1}))(\,((1[0-2]?)|([2-9]{1})))*$/", $parts[3]) or 
            preg_match("/^((1[0-2]?)|([2-9]{1}))\-((1[0-2]?)|([2-9]{1}))(\,((1[0-2]?)|([2-9]{1}))(\-((1[0-2]?)|([2-9]{1})))*)*$/", $parts[3]))
            {}
        else return false;
         if($parts[4] == '*' or 
            preg_match("/^[0-7]L?$/", $parts[4]) or 
            preg_match("/^\*\/([0-7]L?)$/", $parts[4]) or 
            preg_match("/^([0-7]L?)(\,([0-7]L?))*$/", $parts[4]) or 
            preg_match("/^([0-7]L?)\-([0-7]L?)(\,([0-7]L?)(\-([0-7]L?))*)*$/", $parts[4]))
            {}
        else return false;*/

        return true;
    }
    
    /**
     * 
     * Use preg_match() on $part
     * 
     * @param str $part String for scan
     * @param str $mask Regular expression
     * @param str $maskToIteration Regular expression (if not null use in 4 preg_match
     * @return bool 
     */
    protected function _ifMaskPregMatch($part, $mask, $maskToIteration = null )
    {
        if(empty($part) or empty($mask))
            return false;
        
        if($part == '*')
            return true;
        if (preg_match("/^".$mask."$/", $part))
            return true;
        if (empty($maskToIteration))
        {
            if (preg_match("/^\*\/(".$mask.")$/", $part))
                return true;
        }
        else
        {
            if (preg_match("/^\*\/(".$maskToIteration.")$/", $part))
                return true;
        }
        if (preg_match("/^(".$mask.")(\,(".$mask."))*$/", $part))
            return true;
        if (preg_match("/^(".$mask.")\-(".$mask.")(\,(".$mask.")(\-(".$mask."))*)*$/", $part))
            return true;
        
        return false;
    }

        // check copied or not native Magento Booster index.php file
    public function hasAitpagecacheIndexFile()
    {
        if(!function_exists('ait_cache_getFilePath'))
        {
            return false;
        }
        return true;
    }
    
    //only for adminhtml area
    public function throwUnrequiredIndexFileError()
    {
        $error = Mage::helper('aitpagecache')->__('Magento Booster: file "/index.php" is not replaced. Back up your index.php file and copy new index.php from the /magentobooster/ folder to the root directory of your Magento installation.' );
        Mage::getSingleton('adminhtml/session')->addError($error);
    }        
}