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
class Aitoc_Aitpagecache_Model_Target extends Mage_Core_Model_Abstract
{
	public function _construct() 
	{
		parent::_construct();
        $this->_init('aitpagecache/target_page');
	}
	
	public function saveData($data)
    {
        $localPath = Mage::helper('aitpagecache/target')->getPageLocalPath($data['page_url']);

        if ($localPath)
        {
            if (!$this->getPageId($data))
            {
                $targetPage = Mage::getModel('aitpagecache/target_page');
                $targetPage->setData(array('page_path'=>$localPath));
                $targetPage->save();
                $pageId = $targetPage->getPageId();
            }
            else
            {
                $pageId = $this->getPageId($data);
            }

            $targetPageProduct = Mage::getModel('aitpagecache/target_page_product');

            if (is_array($data['product_id']))
            {
                $targetPageProduct->saveData($data['product_id'], $pageId);
            }
            else
            {
                $targetPageProduct->setData(array('product_id'=>$data['product_id'], 'page_id'=>$pageId));
                $targetPageProduct->save();
            }
        }

        return true;
    }

	public function getPageId($data)
	{
		$localPath = Mage::helper('aitpagecache/target')->getPageLocalPath($data['page_url']);
		
		$targetPage = Mage::getModel('aitpagecache/target_page');
		$collection = $targetPage->getCollection();
		$collection
	        ->getSelect()
	        ->where('main_table.page_path = "'.$localPath.'"');
			
	    if ($collection->getSize() > 0)
	    {
			$pageData = $collection->getFirstItem()->getData();
	    	return $pageData['page_id'];
	    }
	    return false;
	}
	
	public function isProductTracked($data)
	{
		$localPath = Mage::helper('aitpagecache/target')->getPageLocalPath($data['page_url']);
		
		$targetPage = Mage::getModel('aitpagecache/target_page');
		$collection = $targetPage->getCollection();
		$collection
	        ->getSelect()
	        ->joinLeft(array('tpp' => Mage::getSingleton('core/resource')->getTableName('aitpagecache/target_page_product')), 'tpp.page_id = main_table.page_id', array('product_id'))
	        ->where('main_table.page_path = "'.$localPath.'"')
	        ->where('tpp.product_id = '.$data['product_id']);
			
	    if ($collection->getSize() > 0)
	    {
	    	return true;
	    }
	    return false;
	}
	
	public function getPagesByProductId($productId)
	{
		$targetPage = Mage::getModel('aitpagecache/target_page');
		$collection = $targetPage->getCollection();
		$collection
	        ->getSelect()
	        ->joinLeft(array('tpp' => Mage::getSingleton('core/resource')->getTableName('aitpagecache/target_page_product')), 'tpp.page_id = main_table.page_id', array('product_id'))
	        ->where('tpp.product_id = '.$productId)
			->group('tpp.page_id');
	        
	    if ($collection->getSize() > 0)
	    {
	    	return $collection->getData();
	    }
	    else 
	    {
	    	return array();
	    }
	}
	
	public function removePagesByProductId($productId)
	{
		if (Mage::getSingleton('core/cache')->canUse('aitpagecache'))
		{
			$targetPage = Mage::getModel('aitpagecache/target_page');

			$collection = $targetPage->getCollection();
			$collection
		        ->getSelect()
		        ->joinLeft(array('tpp' => Mage::getSingleton('core/resource')->getTableName('aitpagecache/target_page_product')), 'tpp.page_id = main_table.page_id', array('product_id'))
		        ->where('tpp.product_id = '.$productId)
		        ->group('tpp.page_id');
				
		    if ($collection->getSize() > 0)
		    {
                $resource = Mage::getSingleton('core/resource');
                $tableProduct = $resource->getTableName('aitpagecache/target_page_product');
                $connection = $resource->getConnection('core_write');

		    	foreach($collection->getItems() as $key=>$item)
				{
					$targetPage->load($key);
	        		$targetPage->delete();
                    $connection->delete($tableProduct, 'page_id = ' . $key . '');
				}
		    }
		}
		
		return true;
	}
	
	public function removeAllPages()
	{
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_write');
		
		$tablePage = $resource->getTableName('aitpagecache/target_page');
        $tableProduct = $resource->getTableName('aitpagecache/target_page_product');
		
		if ($connection->delete($tablePage, '1') && $connection->delete($tableProduct, '1'))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}