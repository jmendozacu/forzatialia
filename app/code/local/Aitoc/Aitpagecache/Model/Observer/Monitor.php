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
class Aitoc_Aitpagecache_Model_Observer_Monitor extends Mage_Core_Model_Abstract
{
    protected $_helper = null;
    protected $_delimeter = "\n";

    protected function _helper() {
        if(is_null($this->_helper)) {
            $this->_helper = Mage::helper('aitpagecache');
        }
        return $this->_helper;
    }
    
    /**
    * Check and setup pause for specific pages if magento load level is exceeded
    */
    public function checkPauseSettings() {
        if(!$this->_helper()->isMonitorEnabled()) {
            //monitor is not installed or disabled
            return false;
        }
        
        $pauseLevel = Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/pause_level');
        if(!$pauseLevel) {
            //configuration is not set - don't use this functional
            return false;
        }
        
        if(!$this->_helper()->getMonitor()->isEmergencyLevel($pauseLevel)) {
            //magento load level is normal and we don't need to do anything now
            return false;
        }
        
        $model = Mage::getModel('aitpagecache/urlGroup');
        if(!$model->checkUrlWithConfig('aitpagecache/aitpagecache_config_aitloadmon/pause_pages')) {
            //current page is not inside pages that need to be paused
            return false;
        }

        $pauseTime = max(0, min(60, (float)Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/pause_time')));
        if($pauseTime == 0) {
            return false;
        }
        $pauseTime = $this->_dynamicSleep($pauseTime);
        if(method_exists('Aitoc_Aitloadmon_Collect','addTimeOffset'))
        {
            Aitoc_Aitloadmon_Collect::addTimeOffset((-1)*$pauseTime);
        }
        $pauseTime = round($pauseTime, 6) * 1000000;
        session_commit();
		usleep($pauseTime);
		session_start();
    }

    /**
     * Check if other pages are on pause now and if they are - extends current sleep time on delta value making page sleep a little longer
     *
     * @param float $pauseTime Old sleep time
     * @param float $delta
     * @return float New sleep time
     */
    protected function _dynamicSleep( $pauseTime, $delta = 0.1 )
    {
        $file = Mage::app()->getConfig()->getOptions()->getBaseDir() . DS . 'var' . DS . 'aitpagecache_sleep.log';
        $now = microtime(true);
        $result = array();
        if(file_exists($file)) {
            $source = file_get_contents($file);
            $source = explode($this->_delimeter, $source);
        } else {
            $source = array();
        }
        if( sizeof($source) > 0 ) {
            foreach($source as $time) {
                if($time > $now) {
                    $result[] = $time;
                }
            }
            $delta = $pauseTime * $delta;
            $pauseTime += $delta * min(sizeof($result), 10);
        }
        $result[] = $now + $pauseTime;
        file_put_contents($file, implode($this->_delimeter, $result));
        return $pauseTime;
    }

    /**
     *
     * BLOCKING AREA
     *
     */
    public function controller_action_predispatch()
    {
        if(!Mage::helper('core')->isModuleEnabled('aitloadmon'))
            return false;
        if(Mage::app()->getStore()->isAdmin())
        {
            Mage::getSingleton('core/cookie')->set('phpsysid',md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
            $adminBlockLoad = Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_customer_admin');
            if($adminBlockLoad && (Mage::helper('aitpagecache')->getMonitor()->getLoadLevel() >= $adminBlockLoad) && !(Mage::getSingleton('adminhtml/session')->getData('aitpagecache_block_admin_warning')=='true'))
            {
                Mage::getSingleton('adminhtml/session')->setData('aitpagecache_block_admin_warning','true');
                Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('aitpagecache')->__('The front end is now blocked to you due to high server load. To change the blocking settings go to System->Configuration->Magento Booster.  Please also do not make any actions in admin panel without critical need during the high server load.'));
            }
            else
            {
                Mage::getSingleton('adminhtml/session')->setData('aitpagecache_block_admin_warning','false');
            }
        }
        else
        {
            if (isset($_COOKIE['adminhtml']) && md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])==Mage::getSingleton('core/cookie')->get('phpsysid'))
            {
                $this->_changeCustomerBlockLoad(Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_customer_admin'));
                return;
            }

            if(Mage::getSingleton('core/session')->getCustomerIsReturning() == true)
            {
                $this->_changeCustomerBlockLoad(Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_customer_returning'));
                return;
            }

            $total = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
            if($total > Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_bigtotal'))
            {
                $this->_changeCustomerBlockLoad(Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_customer_cart_bigtotal'));
                return;
            }
            if($total > 0)
            {
                $this->_changeCustomerBlockLoad(Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_customer_cart'));
                return;
            }

            if(Mage::helper('customer')->isLoggedIn())
            {
                $this->_changeCustomerBlockLoad(Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_customer_login'));
            }
            else
            {
                $this->_changeCustomerBlockLoad(Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_customer_guest'));
            }
        }
    }

    protected function _changeCustomerBlockLoad($blockLoad)
    {
        if(!$this->_helper()->isMonitorEnabled()) {
            //monitor is not installed or disabled
            return false;
        }

        if(!$blockLoad)
        {
            Mage::getSingleton('core/session')->setCustomerBlockLoad(0);
        }
        else//if( $blockLoad > Mage::getSingleton('core/session')->getCustomerBlockLoad())
        {
            Mage::getSingleton('core/session')->setCustomerBlockLoad($blockLoad);
            //echo session_encode(); die();
        }
    }


}