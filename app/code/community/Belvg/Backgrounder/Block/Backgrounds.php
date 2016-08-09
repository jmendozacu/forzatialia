<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Backgrounder
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
class Belvg_Backgrounder_Block_Backgrounds extends Mage_Core_Block_Template
{
   
    protected function _construct()
	{
		$this->addData(array(
			'cache_lifetime' => 18000,	
			'cache_key' =>	'backgrounder_'.Mage::helper('core/url')->getCurrentUrl()
			
		));
	}
	
	public function getImageHtml(){
		$html = $this->helper('backgrounder')->getBackground();
		return $html;
	}
	
	public function isActive(){
		if ($this->helper('backgrounder')->getBackground() != '')
			return true;
		return false;	
	}

}
