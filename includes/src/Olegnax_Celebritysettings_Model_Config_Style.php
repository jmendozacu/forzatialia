<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Celebritysettings_Model_Config_Style
{

    public function toOptionArray()
    {
        return array(
            array(
	            'value'=>'dark',
	            'label' => Mage::helper('celebritysettings')->__('dark')),
            array(
	            'value'=>'light',
	            'label' => Mage::helper('celebritysettings')->__('light')),
        );
    }

}
