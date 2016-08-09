<?php

class POS_System_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('retailexpressGrid');
        $this->setDefaultSort('history_id');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getModel('retailexpress/history')->getCollection());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('history_id', array(
			        'header'    => Mage::helper('retailexpress/data')->__('ID'),
			        'align'     => 'right',
			        'width'     => '50px',
			        'index'     => 'history_id',
        		)
        	 )
        	 ->addColumn('created_at', array(
		            'header'    => Mage::helper('retailexpress/data')->__('Date'),
		            'align'     => 'right',
		            'width'     => '150px',
		            'index'     => 'created_at',
		        	'type'      => 'datetime',
        		)
        	 )
        	 ->addColumn('type', array(
		            'header'    => Mage::helper('retailexpress/data')->__('Type'),
		            'align'     => 'right',
		            'width'     => '50px',
		            'index'     => 'type',
		        	'type'      => 'options',
        	 		'options'   => array(
        	 			  'Cron'   => 'Cron'
        	 			, 'Manual' => 'Manual'
        	 			, 'Online' => 'Online'
        	 		)
        		)
        	 )
			 ->addColumn('comment', array(
                     'header'    => Mage::helper('retailexpress/data')->__('Comment'),
			         'align'     => 'left',
			         'index'     => 'comment',
		        	 'type'      => 'text',
                     'truncate'  => 10000,
			 		 'nl2br'     => true,
        		)
        	 );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }
}