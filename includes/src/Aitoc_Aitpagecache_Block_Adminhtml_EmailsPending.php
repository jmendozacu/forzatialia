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
class Aitoc_Aitpagecache_Block_Adminhtml_EmailsPending extends Aitoc_Aitpagecache_Block_Adminhtml_EmailsAbstract
{
    protected function _prepareCollection()
    {
        $collection = $this->getCollection();
        $collection->getSelect()->where('sent_at IS NULL');
        return parent::_prepareCollection();
    }
}