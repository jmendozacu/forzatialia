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
/**
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/
class Aitoc_Aitpagecache_Model_Config_Admin extends Mage_Core_Model_Config_Data
{
	protected function _afterSave()
	{
        parent::_afterSave();
        if($this->getValue()) {
            //booster for admin is enabled
            $event = 'aitpagecache_admin_config_enabled';
        } else {
            //booster for admin is disabled
            $event = 'aitpagecache_admin_config_disabled';
        }
        Mage::dispatchEvent($event, array('field'=> $this->getField(), 'value'=>$this->getValue()));
        return $this;
    }
}