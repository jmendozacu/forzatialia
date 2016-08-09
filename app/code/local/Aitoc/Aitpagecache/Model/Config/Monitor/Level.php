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
class Aitoc_Aitpagecache_Model_Config_Monitor_Level
{

    /**
     * Returns load levels as a simple array
     *
     * @return array
     */
    public function getLoadArray()
    {
        return array(
            1   =>  'green',
            2   =>  'yellow',
            3   =>  'red',
            4   =>  'black'
        );
    }


    /**
     * Returns load levels as an array for settings
     *
     * @return array
     */
    public function toOptionArray()
    {
        $array = array(
            array('value' => 0, 'label'=>Mage::helper('aitpagecache')->__('Specify the zone to enable this feature')),
        );

        $levels = $this->getLoadArray();

        foreach($levels as $key=>$value)
        {
            $array[] = array('value' => $key, 'label'=>Mage::helper('aitpagecache')->__(ucfirst($value)));
        }

        return $array;
    }

    /**
     * Returns load levels as an array for settings
     *
     * @return array
     */
    public function toArray()
    {
        $array = array(
            0 => Mage::helper('aitpagecache')->__('Specify the zone to enable this feature'),
        );

        $levels = $this->getLoadArray();

        foreach($levels as $key=>$value)
        {
            $array[$key] = Mage::helper('aitpagecache')->__(ucfirst($value));
        }

        return $array;
    }
}