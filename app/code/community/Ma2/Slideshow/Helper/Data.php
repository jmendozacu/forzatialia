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
/* $Id: Data.php 15 2013-11-05 07:30:45Z linhnt $ */

class Ma2_Slideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function recursiveReplace($search, $replace, $subject)
	{
	   if (!is_array($subject))
		 return $subject;

	   foreach ($subject as $key => $value)
		 if (is_string($value))
		   $subject[$key] = str_replace($search, $replace, $value);
		 elseif (is_array($value))
		   $subject[$key] = self::recursiveReplace($search, $replace, $value);

	   return $subject;
	}
}