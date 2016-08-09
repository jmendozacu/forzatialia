<?php

/**
 * POS_System_Block_Adminhtml_Log_Grid.
 *
 * This class creates the grid table on the enhanced sync log page
 *
 * @author chris@retailexpress.com.au
 */
class POS_System_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('retailexpressGrid1');

        $this->setDefaultSort('log_id');

        $this->setDefaultDir('DESC');
    }

    /**
     * _prepareCollection.
     *
     * this method gets data from the table/model
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('retailexpress/log')->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('log_id', [
                    'header' => Mage::helper('retailexpress/data')->__('ID'),
                    'align' => 'left',
                    'width' => '50px',
                    'index' => 'log_id',
                ]
             )
            ->addColumn('method', [
                    'header' => Mage::helper('retailexpress/data')->__('Method'),
                    'align' => 'left',
                    'width' => '50px',
                    'index' => 'method',
//					'type'      => 'options',
//					'options'   => array(
//										'PRODUCTSGETBULKDETAILS'=>'PRODUCTSGETBULKDETAILS',
//										'PRODUCTSGETBULKDETAILSEXTENDED'=>'PRODUCTSGETBULKDETAILSEXTENDED',
//										'WEBORDERGETBULKFULFILLMENT'=>'WEBORDERGETBULKFULFILLMENT',
//										'CUSTOMERGETBULKDETAILS'=>'CUSTOMERGETBULKDETAILS',
//										'PRODUCTGETETADATEBYCHANNEL'=>'PRODUCTGETETADATEBYCHANNEL',
//										'OUTLETSGETBYCHANNEL'=>'OUTLETSGETBYCHANNEL',
//										)
                ]
             )
             ->addColumn('created_date', [
                    'header' => Mage::helper('retailexpress/data')->__('Date'),
                    'align' => 'left',
                    'width' => '150px',
                    'index' => 'created_date',
                    'type' => 'datetime',
                ]
             )
            ->addColumn('sync_request', [
                    'header' => Mage::helper('retailexpress/data')->__('Sync Request'),
                    'width' => '100',
                    'align' => 'center',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                        [
                            'caption' => Mage::helper('retailexpress/data')->__('Click to View'),
                            'url' => [
                                            'base' => '*/*/viewSyncLogXml',
                                            'params' => ['xmltype' => 'sync_request'],
                                            ],
                            'field' => 'id',
                            'onclick' => 'Retail_createPopup(this.href);return false;',
                        ],
                    ],
                    'sortable' => false,
                    'index' => 'sync_request',
                    'is_system' => true,
                ]
                )
            ->addColumn('sync_response', [
                    'header' => Mage::helper('retailexpress/data')->__('Sync Response'),
                    'width' => '100',
                    'align' => 'center',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                        [
                            'caption' => Mage::helper('retailexpress/data')->__('Click to View'),
                            'url' => [
                                            'base' => '*/*/viewSyncLogXml',
                                            'params' => ['xmltype' => 'sync_response'],
                                            ],
                            'field' => 'id',
                            'onclick' => 'Retail_createPopup(this.href);return false;',
                        ],
                    ],
                    'sortable' => false,
                    'index' => 'sync_response',
                    'is_system' => true,
                ]
             );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }
}
