<?php
/**
 * MagenMarket.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Edit or modify this file with yourown risk.
 *
 * @category    Extensions
 * @package     Ma2_Slideshow free
 * @copyright   Copyright (c) 2013 MagenMarket. (http://www.magenmarket.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
**/
/* $Id: Slideshow.php 15 2013-11-05 07:30:45Z linhnt $ */

class Ma2_Slideshow_Block_Slideshow extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
	protected function _toHtml()     
    {
		$slideshow_ids = $this->getData('slideshow_ids');
		
		$collection = Mage::getModel('slideshow/slideshow')
			->getCollection()
			->addFieldToFilter('slideshow_id', array('in' => explode(',', $slideshow_ids)));
		
		$this->assign('items', $collection);
		$this->assign('options', json_decode(json_encode(Mage::getStoreConfig('slideshow_options/general'))));
		
		
		return parent::_toHtml();
	}
}