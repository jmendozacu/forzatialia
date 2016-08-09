<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_ProductVideo
 * @version    1.0.0
 * @copyright  Copyright (c) 2012 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
class Magecon_ProductVideo_Model_System_Config_Source_Theme {

    public function toOptionArray() {
        return array(
            array('value' => 'dark', 'label' => Mage::helper('productvideo')->__('Dark')),
            array('value' => 'light', 'label' => Mage::helper('productvideo')->__('Light')),
        );
    }

}

?>
