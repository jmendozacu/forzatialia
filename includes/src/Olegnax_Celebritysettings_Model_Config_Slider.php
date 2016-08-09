<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Celebritysettings_Model_Config_Slider
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'latest',
	            'label' => Mage::helper('celebritysettings')->__('Latest Arrivals')),
            array(
	            'value'=>'latest_sale',
	            'label' => Mage::helper('celebritysettings')->__('Latest Arrivals  &  On Sale')),
        );
    }

}
