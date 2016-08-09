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
class Aitoc_Aitpagecache_Model_Target_Observer extends Mage_Core_Model_Abstract
{
	public function clearCmsPageCache($observer)
    {
    	$pages = array();
    	$request = Mage::app()->getRequest();
    	$requestParams = $request->getParams();
    	$eventData = $observer->getData('event')->getData();

    	if ($eventData)
    	{
	    	if ((isset($requestParams['page_id']) && $eventData['name'] == 'cms_page_prepare_save') || (isset($requestParams['page_id']) && $eventData['name'] == 'model_delete_before' && $request->getControllerName() == 'cms_page' && $request->getActionName() == 'delete'))
	    	{
	    		$pageUrl = Mage::helper('cms/page')->getPageUrl($requestParams['page_id']);

	    		$pages[] = $pageUrl;
	    		
	    		strpos($pageUrl, '/index.php') ? $pages[] = str_replace('/index.php', '', $pageUrl) : false ;
	    		
	    		Mage::helper('aitpagecache/target')->clearCacheTarget($pages);
	    	}
    	}
    }
    
    public function saveLoadedProductPage($observer)
    {
    	$productData = $observer->getData('product')->getData();
    	
    	if ($productData && Mage::helper('aitpagecache')->aitMayCachePage() && Mage::getSingleton('core/cache')->canUse('aitpagecache'))
    	{
    		$product = Mage::getModel('catalog/product')->load($productData['entity_id']);
			$pageUrl = Mage::helper('core/url')->getCurrentUrl();
    		$productId = $product->getId();
    		if ($pageUrl && $productId)
    		{
    			Mage::getModel('aitpagecache/target')->saveData(array('page_url'=>$pageUrl, 'product_id'=>$productId));
    		}
    	}
    }
    
    public function saveLoadedProductCollectionPages($observer)
    {
        if (Mage::helper('aitpagecache')->aitMayCachePage() && Mage::getSingleton('core/cache')->canUse('aitpagecache'))
        {
            $products = $observer->getCollection()->getData();
            $productPageData = Mage::getSingleton('Aitoc_Aitpagecache_Model_Target_Data');

            foreach ($products as $product)
            {
                $productPageData->addProductPageData($product['entity_id']);
            }
        }
        return true;
    }

    public function saveProductPageData($observer)
    {
        $pageUrl = Mage::helper('core/url')->getCurrentUrl();
        $productIds = Mage::getSingleton('Aitoc_Aitpagecache_Model_Target_Data')->getProductPageData();

        if(!empty($productIds))
        {
            return Mage::getModel('aitpagecache/target')->saveData(array('page_url'=>$pageUrl, 'product_id'=>$productIds));
        }

        return true;
    }
    
    public function clearProductPagesCache($observer)
    {
    	$productId = $observer->getEvent()->getProduct()->getId();
    	
    	if ($productId)
    	{
    		Mage::helper('aitpagecache/target')->clearCacheTargetByProductId($productId);
    	}
    }
    
    public function clearProductPagesCacheOnEdit($observer)
    {
    	$productId = $observer->getEvent()->getProduct()->getId();
    	$product = Mage::getModel('catalog/product')->load($productId);
		$post = Mage::app()->getRequest()->getPost();

		$oldStatus = $product->getStatus();
		$newStatus = $post['product']['status'];
		
		$oldIsInStock = $product->getIsInStock();
		$newIsInStock = $post['product']['stock_data']['is_in_stock'];

		$clearAll = false;
		$clearTarget = false;
		
		if ($productId)
		{
			if ($oldStatus != $newStatus && $newStatus == 1)
			{
				$clearAll = true;
			}
			elseif ($oldStatus == $newStatus || $newStatus == 2)
			{
				$clearTarget = true;
			}
			
			if ($oldIsInStock != $newIsInStock && $newIsInStock == 1)
			{
				$clearAll = true;
			}
			elseif ($oldIsInStock == $newIsInStock || $newIsInStock == 0)
			{
				$clearTarget = true;
			}
		}
		elseif ($newStatus == 1) 
		{
			$clearAll = true;
		}
		
		if ($clearTarget && !$clearAll)
		{
			$productId = $observer->getEvent()->getProduct()->getId();
			
			if ($productId)
    		{
    			Mage::helper('aitpagecache/target')->clearCacheTargetByProductId($productId);
    		}
		}
		elseif ($clearAll)
		{
			Mage::helper('aitpagecache')->clearCache(); 
		}
    }
    
    public function clearProductPagesCacheOnMassStatusChange($observer)
    {
    	$post = Mage::app()->getRequest()->getPost();
    	$productIds = $post['product'];
    	
    	if ($productIds)
    	{
	    	if ($post['status'] == 2)
	    	{
	    		foreach ($productIds as $productId)
		    	{
		    		if ($productId)
		    		{
		    			Mage::helper('aitpagecache/target')->clearCacheTargetByProductId($productId);
		    		}
		    	}  
	    	}
	    	elseif ($post['status'] == 1) 
	    	{
	    		Mage::helper('aitpagecache')->clearCache();
	    	}
    	}
    }
    
    public function clearProductPagesCacheOnOrderSave($observer) 
    {
    	$data = $observer->getEvent()->getData();
    	$item = $data['item'];
    	$itemData = $item->getData();
    	
    	if ($itemData['product_id'] && !$itemData['qty'] && !$itemData['is_in_stock'])
    	{
    		Mage::helper('aitpagecache/target')->clearCacheTargetByProductId($itemData['product_id']);
    	}
    }
}