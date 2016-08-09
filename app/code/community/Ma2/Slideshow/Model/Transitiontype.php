<?php
/**
 * MagenMarket.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Edit or modify this file with yourown risk.
 *
 * @category    Extensions
 * @package     Ma2_Slideshow free
 * @copyright   Copyright (c) 2013 MagenMarket. (http://www.magenmarket.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
**/
/* $Id: Transitiontype.php 15 2013-11-05 07:30:45Z linhnt $ */

class Ma2_Slideshow_Model_Transitiontype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'fade',	'label' => Mage::helper('slideshow')->__('Fade')),
			array('value' => 'slide',	'label' => Mage::helper('slideshow')->__('Slide'))
        );
    }
}