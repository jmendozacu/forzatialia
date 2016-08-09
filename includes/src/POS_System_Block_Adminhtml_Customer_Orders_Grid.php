<?php

class POS_System_Block_Adminhtml_Customer_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{


    /**
     * Pager visibility
     *
     * @var boolean
     */
    protected $_pagerVisibility = false;

    /**
     * Filter visibility
     *
     * @var boolean
     */
    protected $_filterVisibility = false;

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_rex_orders_grid');
        $this->_emptyText = Mage::helper('adminhtml')->__('No sales orders found...');
        $this->setUseAjax(true);
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('retailexpress/orders');
        $collection->loadREX();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header'    => Mage::helper('customer')->__('POS Sales Order #'),
            'width'     => '100',
            'index'     => 'order_id',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('customer')->__('Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('billing_name', array(
            'header'    => Mage::helper('customer')->__('Bill To'),
            'index'     => 'billing_name',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('shipping_name', array(
            'header'    => Mage::helper('customer')->__('Deliver To'),
            'index'     => 'shipping_name',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('public_comment', array(
            'header'    => Mage::helper('customer')->__('Public Comments'),
            'index'     => 'public_comment',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('order_total', array(
            'header'    => Mage::helper('customer')->__('Order Total'),
            'index'     => 'order_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('freight_total', array(
            'header'    => Mage::helper('customer')->__('Freight Total'),
            'index'     => 'freight_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Grand Total'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
            'filter'    => false,
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }

}
