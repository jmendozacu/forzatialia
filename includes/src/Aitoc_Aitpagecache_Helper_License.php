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
class Aitoc_Aitpagecache_Helper_License extends Aitoc_Aitsys_Helper_License
{
	protected $sCacheConfig = '';
	
	public function __construct()
	{
        $this->sCacheConfig = Mage::app()->getConfig()->getOptions()->getBaseDir() . DS . 'magentobooster' . DS . 'use_cache.ser';
    }

    public function uninstallBefore()
    {
    	$allTypes = Mage::app()->useCache();
        $allTypes['aitpagecache'] = 0;
        Mage::app()->saveUseCache($allTypes);
    	
        $this->writeUseCacheData(0);
    	
    	//Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('Please do not forget to restore the original index.php from the backup.'));
    }
    
    public function installBefore()
    {
    	$allTypes = Mage::app()->useCache();
        $allTypes['aitpagecache'] = 0;
        Mage::app()->saveUseCache($allTypes);
        
        if(!function_exists('ait_cache_getFilePath'))
        {
            $error = Mage::helper('adminhtml')->__('Magento Booster: file "/index.php" is not replaced. Back up your index.php file and copy new index.php from the /magentobooster/ folder to the root directory of your Magento installation.' );
            Mage::getSingleton('adminhtml/session')->addError($error);
        }
    }
    
	protected function writeUseCacheData($enabled = 1)
	{
        try
        {
            return file_put_contents($this->sCacheConfig, serialize(
                array(
                    'aitpagecache' => (int)$enabled,
                    Aitoc_Aitpagecache_Mainpage::QUOTE_VALUE => (bool)Mage::getStoreConfig('aitpagecache/config/enable_for_quote'),
                    Aitoc_Aitpagecache_Mainpage::LOGIN_VALUE   => (bool)Mage::getStoreConfig('aitpagecache/config/enable_for_logined')
                )
            ));
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}