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
class Aitoc_Aitpagecache_Model_Config_Monitor_Server
{
    /**
     * Returns total numbers of allowed servers as an array for settings
     *
     * @return array
     */
    public function toOptionArray()
    {
        $servers = Mage::getStoreConfig('aitpagecache/server_configuration/backend_amount');
        if($servers <= 1) {
            return array(
                array('value' => 1, 'label'=>Mage::helper('aitpagecache')->__('Feature can\'t be used, at least 2 servers required')),
            );
        }

        $array = array(
            array('value' => 0, 'label'=>Mage::helper('aitpagecache')->__('Select a server to use for this user')),
        );

        for($i=1;$i<=$servers;$i++)
        {
            $array[] = array('value' => $i, 'label'=>'Server '. $i);
        }

        return $array;
    }

}