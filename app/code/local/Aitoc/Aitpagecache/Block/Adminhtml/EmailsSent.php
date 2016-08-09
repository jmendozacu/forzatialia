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
class Aitoc_Aitpagecache_Block_Adminhtml_EmailsSent extends Aitoc_Aitpagecache_Block_Adminhtml_EmailsAbstract
{
    protected function _prepareCollection()
    {
        $model = Mage::getModel('aitpagecache/emails');
        $collection = $this->getCollection();
        $currVersion = Mage::getVersion();
        if(version_compare($currVersion, '1.4.2.0', 'ge'))
        {
            $collection->getSelect()->joinLeft(array('coupons'=>$model->getResource()->getTable('salesrule/coupon')), 'main_table.rule_id = coupons.rule_id', array('code'));
        }
        else
        {
            $collection->getSelect()->joinLeft(array('salesrules'=>$model->getResource()->getTable('salesrule/rule')), 'main_table.rule_id = salesrules.rule_id', array('code'=>'coupon_code'));
        }

        $collection->getSelect()->where('sent_at IS NOT NULL');
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('sent_at', array(
            'header'    => Mage::helper('aitpagecache')->__('Sent At'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'sent_at',
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('aitpagecache')->__('Coupon Code'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'text',
            'index'     => 'code',
        ));

        return $this;
    }

}