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
class Aitoc_Aitpagecache_Model_Config_Rewrite extends Mage_Core_Model_Config_Data
{
	protected function _afterSave()
	{
        parent::_afterSave();
        Mage::dispatchEvent('aitpagecache_config_changed', array('field'=> $this->getField(), 'value'=>$this->getValue()));
        Mage::helper('aitpagecache')->clearCache();
        return $this;
    }
}