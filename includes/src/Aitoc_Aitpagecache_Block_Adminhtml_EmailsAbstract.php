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
class Aitoc_Aitpagecache_Block_Adminhtml_EmailsAbstract extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('email_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);

        $this->_initCollection();
    }

    protected function _initCollection()
    {
        $customer = Mage::getModel('customer/customer');
        /* @var $customer Mage_Customer_Model_Customer */
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');

        $collection = Mage::getModel('aitpagecache/emails')->getCollection();
        $collection->getSelect()
            ->joinLeft(
                array('customer_lastname_table'=>$lastname->getBackend()->getTable()),
                'customer_lastname_table.entity_id=main_table.customer_id
                     AND customer_lastname_table.attribute_id = '.(int) $lastname->getAttributeId() . '
                     ',
                array('customer_lastname'=>'value')
            )
            ->joinLeft(
                array('customer_firstname_table'=>$firstname->getBackend()->getTable()),
                'customer_firstname_table.entity_id=main_table.customer_id
                     AND customer_firstname_table.attribute_id = '.(int) $firstname->getAttributeId() . '
                     ',
                array('customer_firstname'=>'value')
            );
        $this->setCollection($collection);
    }


    protected function _prepareColumns()
    {
        $this->addColumn('email_id', array(
            'header'    => Mage::helper('aitpagecache')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'email_id',
        ));


        $this->addColumn('customer_firstname', array(
            'header'    => Mage::helper('aitpagecache')->__('Customer Firstname'),
            'align'     =>'left',
            'index'     => 'customer_firstname',
            'width'     => '150px',
            'filter'    => false,
        ));

        $this->addColumn('customer_lastname', array(
            'header'    => Mage::helper('aitpagecache')->__('Customer Lastname'),
            'align'     =>'left',
            'index'     => 'customer_lastname',
            'width'     => '150px',
            'filter'    => false,
        ));

        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('aitpagecache')->__('Customer Email'),
            'align'     =>'left',
            'index'     => 'customer_email',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return 'javascript:void(0);';
    }

}