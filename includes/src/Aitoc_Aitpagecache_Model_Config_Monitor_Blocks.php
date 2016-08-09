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
class Aitoc_Aitpagecache_Model_Config_Monitor_Blocks extends Mage_Eav_Model_Entity_Attribute
{
    protected $_options = null;

    public function toOptionArray()
    {
        if ($this->_options === null)
        {
            $this->_options = array(
                array(
                    'value' => 0,
                    'label' => 'Off'
                ));

            $collection = Mage::getModel('cms/block')->getCollection()->addFieldToFilter('is_active', 1);

            if (!Mage::app()->isSingleStoreMode())
            {
                if (Mage::app()->getRequest()->getParam('store'))
                {
                    $storeId = Mage::getModel('core/store')
                        ->load(Mage::app()->getRequest()->getParam('store'))
                        ->getId();

                    $collection->addStoreFilter($storeId);
                }
                else
                {
                    $collection->addStoreFilter(Mage::app()->getStore()->getStoreId());
                }
            }

            $this->_options = array_merge(
                $this->_options,
                $collection->toOptionArray()
            );
        }
        return $this->_options;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}