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
class Aitoc_Aitpagecache_Model_Target_Data extends Mage_Core_Model_Abstract
{
    // array of unique product Ids from loaded collections
    protected $productPageData = array();

    public function getProductPageData()
    {
        return $this->productPageData;
    }

    public function addProductPageData($id)
    {
        if($id && !in_array($id, $this->productPageData))
        {
            $this->productPageData[] = $id;
        }
    }
}