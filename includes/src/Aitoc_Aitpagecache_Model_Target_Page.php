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
class Aitoc_Aitpagecache_Model_Target_Page extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('aitpagecache/target_page');
    }
}