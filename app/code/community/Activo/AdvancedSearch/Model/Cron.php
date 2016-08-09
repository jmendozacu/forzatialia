<?php

class Activo_AdvancedSearch_Model_Cron extends Mage_Core_Model_Abstract
{
    const XML_PATH_CRON_ENABLED             = 'advancedsearch/global/cron_enabled';

    protected $_errors = array();
    
    public function index()
    {
        if (!Mage::getStoreConfig(self::XML_PATH_CRON_ENABLED)) {
            return $this;
        }
        
        Mage::getModel('advancedsearch/dictionary')->build();
        
        return $this;
    }
}
