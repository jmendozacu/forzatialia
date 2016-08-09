<?php

class POS_ClickAndCollect_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_product_Grid
{
    protected function _prepareCollection()
    {
        $imageAttribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'image');
        $imageAttributeId = $imageAttribute->getId();

        $tPrefix = (string) Mage::getConfig()->getTablePrefix();

        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );

        $collection->calculateSizeWithoutGroupClause = true;
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
            // add store pickup rule attrubte to collection
            $collection->joinAttribute('store_pickup_rule', 'catalog_product/store_pickup_rule', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
            // add store pickup rule attrubte to collection
            $collection->joinAttribute('store_pickup_rule', 'catalog_product/store_pickup_rule', 'entity_id', null, 'left');
        }

        // add at least one product category to collection
        $collection->joinTable('catalog/category_product', 'product_id=entity_id',
            ['single_category_id' => new Zend_Db_Expr('IF(category_id IS NOT NULL, 1, 0)'), 'single_category_id_filter' => 'category_id'],
            null, 'left');
        // add image data to collection
        $collection->joinTable(['attributes' => $tPrefix.'catalog_product_entity_varchar'], 'entity_id=entity_id',
            ['image_boolean' => new Zend_Db_Expr("IF(attributes.value='no_selection' OR attributes.value IS NULL, 0, 1)"), 'image_filter' => 'value'],
            'attributes.attribute_id='.$imageAttributeId, 'left');
        // add last sync to collection
        $collection->joinTable($tPrefix.'sync_product', 'product_id=entity_id',
            ['last_date' => 'last_date'],
            'customer_id = 0', 'left');
        $collection->getSelect()->group(['e.entity_id']);

        $this->setCollection($collection);
        $this->_prepareWidgetCollection();

        $this->getCollection()->addWebsiteNamesToResult();

        return $this;
    }

    /**
     * Sets sorting order by some column.
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _setCollectionOrder($column)
    {
        if ($column->getId() == 'single_category_id') {
            $collection = $this->getCollection();
            if ($collection) {
                $columnIndex = $column->getFilterIndex() ?
                    $column->getFilterIndex() : $column->getIndex();
                $collection->getSelect()->order($columnIndex.' '.$column->getDir());
            }
        } elseif ($column->getId() == 'image_boolean') {
            $collection = $this->getCollection();
            if ($collection) {
                $columnIndex = $column->getFilterIndex() ?
                    $column->getFilterIndex() : $column->getIndex();
                $collection->getSelect()->order($columnIndex.' '.$column->getDir());
            }
        } elseif ($column->getId() == 'store_pickup_rule') {
            $collection = $this->getCollection();
            if ($collection) {
                $columnIndex = $column->getFilterIndex() ?
                    $column->getFilterIndex() : $column->getIndex();
                $collection->getSelect()->order($columnIndex.' '.$column->getDir());
            }
        } else {
            $collection = $this->getCollection();
            if ($collection) {
                $columnIndex = $column->getFilterIndex() ?
                    $column->getFilterIndex() : $column->getIndex();
                $collection->setOrder($columnIndex, $column->getDir());
            }
        }

        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'single_category_id' && ($column->getFilter()->getValue() == 1 || $column->getFilter()->getValue() == 0)) {
            $val = $column->getFilter()->getValue();
            $condition = $val == 1 ? 'notnull' : 'null';
            $this->getCollection()->addFieldToFilter('single_category_id_filter', [$condition => true]);
        } elseif ($column->getId() == 'image_boolean' && ($column->getFilter()->getValue() == 1 || $column->getFilter()->getValue() == 0)) {
            $val = $column->getFilter()->getValue();
            if ($val == 1) {
                $this->getCollection()->addFieldToFilter('image_filter', ['notnull' => true]);
                $this->getCollection()->addFieldToFilter('image_filter', ['neq' => 'no_selection']);
            } else {
                $this->getCollection()
                    ->addAttributeToFilter([
                    [
                        'attribute' => 'image_filter',
                        'null' => true,
                    ],
                    [
                        'attribute' => 'image_filter',
                        'eq' => 'no_selection',
                    ],
                ]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _prepareWidgetCollection()
    {
        if ($this->getCollection()) {
            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            } elseif ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            } elseif (0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir) == 'desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            [
                'header' => Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type' => 'number',
                'index' => 'entity_id',
        ]);
        $this->addColumn('name',
            [
                'header' => Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ]);

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                [
                    'header' => Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ]);
        }

        $this->addColumn('type',
            [
                'header' => Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ]);

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            [
                'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
        ]);

        $this->addColumn('sku',
            [
                'header' => Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ]);

        $store = $this->_getStore();
        $this->addColumn('price',
            [
                'header' => Mage::helper('catalog')->__('Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ]);

        $this->addColumn('qty',
            [
                'header' => Mage::helper('catalog')->__('Qty'),
                'width' => '100px',
                'type' => 'number',
                'index' => 'qty',
        ]);

        $this->addColumn('visibility',
            [
                'header' => Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type' => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ]);

        $this->addColumn('store_pickup_rule',
            [
                'header' => Mage::helper('catalog')->__('Pickup Rule'),
                'width' => '70px',
                'index' => 'store_pickup_rule',
                'type' => 'options',
                'options' => Mage::getSingleton('clickandcollect/product_attribute_source_pickuprule')->getOptionArray(),
        ]);

        $this->addColumn('single_category_id',
            [
                'header' => Mage::helper('catalog')->__('Category'),
                'width' => '70px',
                'index' => 'single_category_id',
                'type' => 'options',
                'options' => [
                    0 => 'No',
                    1 => 'Yes',
                ],
                'sort_index' => 'single_category_id',
                'filter_index' => 'single_category_id',
        ]);

        $this->addColumn('image_boolean',
            [
                'header' => Mage::helper('catalog')->__('Image'),
                'width' => '70px',
                'index' => 'image_boolean',
                'type' => 'options',
                'options' => [
                    0 => 'No',
                    1 => 'Yes',
                ],
                'sort_index' => 'image_boolean',
                'filter_index' => 'image_boolean',
        ]);

        $this->addColumn('last_date',
            [
                'header' => Mage::helper('catalog')->__('Last Sync'),
                'width' => '70px',
                'index' => 'last_date',
                'type' => 'datetime',
        ]);

        $this->addColumn('status',
            [
                'header' => Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type' => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                [
                    'header' => Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    'options' => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ]);
        }

        $this->addColumn('action',
            [
                'header' => Mage::helper('catalog')->__('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')],
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
        ]);

        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        $pickupRules = Mage::getSingleton('clickandcollect/product_attribute_source_pickuprule')->getOptionArray();

        $deliveryRules = Mage::getSingleton('clickandcollect/product_attribute_source_deliveryrule')->getOptionArray();

        $this->getMassactionBlock()->addItem('pickuprule', [
             'label' => Mage::helper('catalog')->__('Set Pickup Rule'),
             'url' => $this->getUrl('clickandcollect/adminhtml_index/massChangePickupRule', ['_current' => true]),
             'additional' => [
                'visibility' => [
                    'name' => 'pickuprule',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Pickup Rule'),
                    'values' => $pickupRules,
                ],
             ],
        ]);
        $this->getMassactionBlock()->addItem('deliveryrule', [
             'label' => Mage::helper('catalog')->__('Set Delivery Rule'),
             'url' => $this->getUrl('clickandcollect/adminhtml_index/massChangeDeliveryRule', ['_current' => true]),
             'additional' => [
                'visibility' => [
                    'name' => 'deliveryrule',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Delivery Rule'),
                    'values' => $deliveryRules,
                ],
            ],
        ]);

        return $this;
    }
}
