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
class Aitoc_Aitpagecache_Model_Config_Monitor_Usergroups extends Varien_Object
{
    public function toOptionArray()
    {
        $vals = array(
            'guest'    => Mage::helper('salesrule')->__('Guest'),
            'logined'  => Mage::helper('salesrule')->__('Logged in Customer'),
            'catalog'  => Mage::helper('salesrule')->__('Customer at the product page'),
            'cart'     => Mage::helper('salesrule')->__('Customer in the shopping cart'),
            'checkout' => Mage::helper('salesrule')->__('Customer at the checkout'),
            'bigtotal' => Mage::helper('salesrule')->__('High cost customer at the checkout'),
            'admin'    => Mage::helper('salesrule')->__('Admin'),
        );
        $options = array();
        foreach ($vals as $k => $v)
            $options[] = array(
                'value' => $k,
                'label' => $v
            );

        return $options;
    }
}