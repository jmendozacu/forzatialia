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
class Belvg_Backgrounder_Helper_Data extends Mage_Core_Helper_Abstract
{
	

	public function getBackground(){
		$_data = $this->_initType();		
		$backgroundCollection = Mage::getModel('backgrounder/backgrounds')->loadByType($_data);			
		$image = $this->_renderImage($backgroundCollection);
		return $image;
	}
	
	protected function _renderImage($bgColl){
		$imgHtml = ''; $imgIndex = 0;
		foreach ($bgColl as $_bg){
			$imgHtml .= '<img src="'.Mage::getBaseUrl('media').'backgrounds/'.$_bg->getImage().'" ';			
			if ($imgIndex != 0)
				$imgHtml .='class="bg-slide"';
			else		
				$imgHtml .='class="bg-slide active" style="display:block"';
			$imgHtml .= ' />';	
			$imgIndex++;
		}	
		return $imgHtml;
	}
	
	protected function _initType(){
		$module = Mage::app()->getRequest()->getModuleName();		
		if ($module == 'cms'){
			$page = Mage::registry('current_page');
			if (!$page)
				$id = 0;
			else
				$id = $page->getPageId();
			$type = 0;			
		}elseif($module == 'catalog'){
			
			if (Mage::app()->getRequest()->getControllerName() == 'product'){
				$type = 1;
				$id = Mage::registry('product')->getId();	
			}else{
				$type = 2;
				$id = Mage::registry('current_category')->getId();
			}
		}		
		return array('id'=>$id,'type'=>$type);
	}
	 
	public function getMultiUploadHtml($elName){
		
		$scriptHtml = '<button style="" onclick="removeBg($(this))" class="scalable " type="button" id=""><span>remove</span></button>';
		$scriptHtml .= '<script type="text/javascript">function addBg(){var element = $("btnaddbg").up().up().previous(); var elbtn = $("btnaddbg").up().up(); var clone = element.cloneNode(true);  if (typeof(clone.down("img")) != "undefined") { clone.down("img").next().remove(); clone.down("img").remove();} clone.down("input").value=""; elbtn.parentNode.insertBefore(clone, elbtn); }</script>';		
		$scriptHtml .= '<script type="text/javascript">function removeBg(element){element.up().up().remove()}</script>';		
		return $scriptHtml;
	}
	
	public function getMultiButtonHtml(){
		$buttonHtml = '</br></br><button style="" onclick="addBg()" class="scalable " type="button" id="btnaddbg"><span>Add more backgrounds</span></button>';
		return $buttonHtml;
	}
	
	public function getBgForCms($cmsId){
		if (!$cmsId)
			return array();
		$collection = Mage::getModel('backgrounder/backgrounds')->getCollection()->addFieldToFilter('type',0)->addFieldToFilter('obj_id',$cmsId);
		return $collection;
	}
	
	public function getBgForProduct($proId){
		if (!$proId)
			return array();
		$collection = Mage::getModel('backgrounder/backgrounds')->getCollection()->addFieldToFilter('type',1)->addFieldToFilter('obj_id',$proId);
		return $collection;
	}
	
	public function getBgForCat($catId){
		if (!$catId)
			return array();
		$collection = Mage::getModel('backgrounder/backgrounds')->getCollection()->addFieldToFilter('type',2)->addFieldToFilter('obj_id',$catId);
		return $collection;
	}
	
	
	public function getAdminBg($image){ 
		$img = '<img src="'.Mage::getBaseUrl('media').'backgrounds/'.$image.'" width="50" height="50"/><input type="hidden" name="bgsv[]" value="'.$image.'"/>';
		return  $img;
	}
	
    
}


