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
class Aitoc_Aitpagecache_Model_UrlGroup extends Mage_Core_Model_Abstract
{
    /**
    * @return int group id of current URL. If page is unknown or id is not set, zero is returned
    * 
    */
    public function getUrlGroup()
    {
        if( !Mage::helper('aitpagecache')->isMonitorEnabled() ) {
            return 0;
        }
        return (int)Mage::helper('aitloadmon')->getGroupIdOfCurrentPage();
    }
    
    /**
    * Get curretn request url and validate it with required groups
    * 
    * @param array $group_id Array containing group ids
    * @return bool 
    */
    public function isUrlInGroup( $group_ids = array() )
    {
        $id = $this->getUrlGroup();
        if(!$id) {
            return false;
        }
        return in_array($id, $group_ids);
    }
    
    /**
    * Get URL configuration from database and validate if current page is in it
    * 
    * @param string $configKey
    * @return bool
    */
    public function checkUrlWithConfig( $configKey ) {
        $config = Mage::getStoreConfig($configKey);
        if(is_string($config)) {
            $config = explode(',', $config);
        }
        if(!is_array($config) || sizeof($config) == 0) {
            return false;
        }
        return $this->isUrlInGroup($config);
    }
}