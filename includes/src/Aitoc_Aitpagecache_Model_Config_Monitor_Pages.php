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
class Aitoc_Aitpagecache_Model_Config_Monitor_Pages
{

    /**
     * Returns page levels as a simple array
     *
     * @return array
     */
    public function getLoadArray()
    {
        if (Mage::helper('aitpagecache')->isMonitorEnabled()) {
            $levels = Mage::helper('aitloadmon')->getGroupsArray();
            $levels = array_flip($levels);
            $firstElem = $levels[1];
            unset($levels[1]);
            $levels[1] = $firstElem;
            return $levels;
        } else {
            return array();
        }
    }


    /**
     * Returns pages levels as an array for settings
     *
     * @return array
     */
    public function toOptionArray()
    {
        $levels = $this->getLoadArray();
        if(sizeof($levels) == 0) {
            return array(
                array('value' => 0, 'label'=>Mage::helper('aitpagecache')->__('Feature can\'t be used')),
            );
        }

        $array = array(
            array('value' => 0, 'label'=>Mage::helper('aitpagecache')->__('No pages selected')),
        );

        foreach($levels as $key=>$value)
        {
            $array[] = array('value' => $key, 'label'=>Mage::helper('aitpagecache')->__(ucfirst($value)));
        }

        return $array;
    }

    /**
     * Returns page levels as an array for settings
     *
     * @return array
     */
    public function toArray()
    {
        $array = array(
            0 => Mage::helper('aitpagecache')->__('No pages selected'),
        );

        $levels = $this->getLoadArray();

        foreach($levels as $key=>$value)
        {
            $array[$key] = Mage::helper('aitpagecache')->__(ucfirst($value));
        }

        return $array;
    }
}