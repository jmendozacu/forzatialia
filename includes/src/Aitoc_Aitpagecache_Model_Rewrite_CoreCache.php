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
class Aitoc_Aitpagecache_Model_Rewrite_CoreCache extends Mage_Core_Model_Cache
{
    public function __construct(array $options = array())
    {
        $this->_defaultBackendOptions['cache_dir'] = Mage::getBaseDir('cache');
        /**
         * Initialize id prefix
         */
        $this->_idPrefix = isset($options['id_prefix']) ? $options['id_prefix'] : '';
        if (!$this->_idPrefix && isset($options['prefix'])) {
            $this->_idPrefix = $options['prefix'];
        }
        if (empty($this->_idPrefix)) {
            $this->_idPrefix = substr(md5(Mage::getConfig()->getOptions()->getEtcDir()), 0, 3).'_';
        }

        $backend    = $this->_getBackendOptions($options);
        $frontend   = $this->_getFrontendOptions($options);

        // <<< AITOC UPDATE START
        $class = 'Varien_Cache_Core';
        if (class_exists('Aitoc_Aitpagecache_Mainpage',false))
        {
            $mainpage = Aitoc_Aitpagecache_Mainpage::getInstance(Mage::getBaseDir());
            if($mainpage->isModuleEnabled()) {
                $class = 'Aitoc_Aitpagecache_Varien_Cache_Core';
            }
        }    
        
        $this->_frontend = Zend_Cache::factory($class, $backend['type'], $frontend, $backend['options'],
            true, true, true
        );
        // >>> AITOC UPDATE END

        if (isset($options['request_processors'])) {
            $this->_requestProcessors = $options['request_processors'];
        }

        if (isset($options['disallow_save'])) {
            $this->_disallowSave = $options['disallow_save'];
        }
    }

}