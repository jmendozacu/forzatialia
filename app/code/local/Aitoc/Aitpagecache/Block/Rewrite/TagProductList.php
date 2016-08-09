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
class Aitoc_Aitpagecache_Block_Rewrite_TagProductList extends Mage_Tag_Block_Product_List
{   
    public function _toHtml()
    {
       // echo "<pre>"; 
       $html = parent::_toHtml();
       $html = preg_replace("/<script[^>]+>.+<\/script>/ismU", "", $html);
       return $html;
    }
}