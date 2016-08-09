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
class Aitoc_Aitpagecache_Helper_Target extends Aitoc_Aitpagecache_Helper_Data
{
    public function getCacheFilePath($pageUrl)
    {
        $booster = Mage::helper('aitpagecache')->getBooster();
        $booster->_cacheFileParams['requestUri'] = $pageUrl;
        $path = $booster->getCacheFilePath();
		return $path;
    }
    
    public function getPageLocalPath($pageUrl)
    {
    	$path = $this->getCacheFilePath($pageUrl);
    	if ($path)
    	{
	    	$isMobile = basename(dirname(dirname($path))) == 'mobile' || basename(dirname(dirname($path))) == 'tablet' ? true : false;
	    	if ($isMobile)
	    	{
				$localPath = basename(dirname(dirname($path))).DS.basename(dirname($path)).DS.basename($path);
	    	}
	    	else 
	    	{
	    		$localPath = basename(dirname($path)).DS.basename($path);
	    	}
			return $localPath;
    	}
    	else 
    	{
    		return '';
    	}
    }
    
    public function clearCacheTarget($pages)
    {
    	foreach ($pages as $pageUrl)
    	{
    		$path = $this->getCacheFilePath($pageUrl);

    		Mage::helper('aitpagecache')->_emptyFullPath(basename($path), dirname($path));
    		
    		if (strpos($path, '/media/pages/'))
    		{
	    		$pathMobile = str_replace('/media/pages/', '/media/pages/mobile/', $path);
	    		Mage::helper('aitpagecache')->_emptyFullPath(basename($pathMobile), dirname($pathMobile));
	    		
	    		$pathTablet = str_replace('/media/pages/', '/media/pages/tablet/', $path);
	    		Mage::helper('aitpagecache')->_emptyFullPath(basename($pathTablet), dirname($pathTablet));
    		}
    	}    		
    }
    
    public function clearCacheTargetByProductId($productId)
    {
    	$pages = Mage::getModel('aitpagecache/target')->getPagesByProductId($productId);
    	
    	$booster = Mage::helper('aitpagecache')->getBooster();
    	$booster->_cacheFileParams['requestUri'] = '';
    	$cachePath = dirname(dirname($booster->getCacheFilePath()));
    	
    	$isMobile = basename($cachePath) == 'mobile' || basename($cachePath) == 'tablet' ? true : false;
    	if ($isMobile)
    	{
            $cachePath = dirname($cachePath);
    	}
    	
    	foreach ($pages as $page)
    	{
	    	$filePath = $cachePath.DS.$page['page_path'];
	    	Mage::helper('aitpagecache')->_emptyFullPath(basename($filePath), dirname($filePath));
    	}
    	
    	Mage::getModel('aitpagecache/target')->removePagesByProductId($productId);
    }
}