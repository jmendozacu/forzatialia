<?php
class Aitoc_Aitpagecache_Varien_Cache_Core extends Varien_Cache_Core
{
    protected $_messageSend = false;

    public function clean($mode = 'all', $tags = array())
    {
        $booster = Mage::helper('aitpagecache')->getBooster();
        //checking if Monitor module is used
        if($booster && $booster->getMonitor()->isEnabled()) {
            if ( $booster->getMonitor()->isEmergencyLevelByKey('disallow_clear_cache_level') )
            {
                //server is under heavy zone on which we disallow to clear cache
                try {
                    if($this->_messageSend == false && is_object(Mage::getSingleton('adminhtml/session'))) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitpagecache')->__('Server is overloaded with requests, clearing cache is not allowed.'));
                        $this->_messageSend = true;
                    }
                } catch( Exception $e) {
                }
                return false;
            }
        }

        return parent::clean($mode, $tags);
    }    
}    