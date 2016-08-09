<?php

/**
 * overwritten Import class for REX Import adapter.
 */
class POS_System_Model_Import_Product_Adapter extends Mage_ImportExport_Model_Import_Entity_Product
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_dataSourceModel = POS_System_Model_Import_Product_Source::getDataSourceModelREX();
    }

    /**
     * Initialize product type models.
     *
     * @throws Exception
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product
     */
    protected function _initTypeModels()
    {
        parent::_initTypeModels();
        $this->_productTypeModels['simple'] = Mage::getModel('retailexpress/import_product_simple', [$this, 'simple']);
        $this->_productTypeModels['downloadable'] = Mage::getModel('retailexpress/import_product_simple', [$this, 'simple']);
        $this->_productTypeModels['configurable'] = Mage::getModel('retailexpress/import_product_configurable', [$this, 'configurable']);

        return $this;
    }

    public function resetImportedCounters()
    {
        $this->_errors = [];
        $this->_errorsCount = 0;
        $this->_errorsLimit = 10000;
        $this->_processedRowsCount = 0;
        $this->_processedEntitiesCount = 0;
        $this->_rowsToSkip = [];
//        $this->_newSku = array();
        $this->_validatedRows = [];
    }

    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased.
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array                                    $indexValAttrs OPTIONAL Additional attributes' codes with index values.
     *
     * @return array
     */
    public function getAttributeOptions(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $indexValAttrs = [])
    {
        if (!Mage::getConfig()->getModuleConfig('Infinity_Allcooks')->is('active', 'true') || ('brand' != $attribute->getAttributeCode())) {
            return parent::getAttributeOptions($attribute, $indexValAttrs);
        }

        $options = [];

        if ($attribute->usesSource()) {
            // merge global entity index value attributes
            $indexValAttrs = array_merge($indexValAttrs, $this->_indexValueAttributes);

            // should attribute has index (option value) instead of a label?
            $index = in_array($attribute->getAttributeCode(), $indexValAttrs) ? 'value' : 'label';

            // only default (admin) store values used
            $attribute->setStoreId(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);

            try {
                foreach ($attribute->getSource()->getAllOptions(false) as $value => $label) {
                    if ($value) { // skip ' -- Please Select -- ' option
                        $options[strtolower($label)] = $value;
                    }
                }
            } catch (Exception $e) {
                // ignore exceptions connected with source models
            }
        }

        return $options;
    }

    /**
     * Stock item saving.
     *
     * @todo check the compatibility with magento 1.5+
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product
     */
    protected function _saveStockItem()
    {
        if (!Mage::helper('retailexpress')->getPosSystemEnabled()) {
            return parent::_saveStockItem();
        }

        // field 'stock_status_changed_automatically' have changed to 'stock_status_changed_auto' on 1.6.0.0 version
        if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
            // 1.6.0.0 or greater
            $defaultStockData = [
                'manage_stock' => 1,
                'use_config_manage_stock' => 1,
                'qty' => 0,
                'min_qty' => 0,
                'use_config_min_qty' => 1,
                'min_sale_qty' => 1,
                'use_config_min_sale_qty' => 1,
                'max_sale_qty' => 10000,
                'use_config_max_sale_qty' => 1,
                'is_qty_decimal' => 0,
                'backorders' => 0,
                'use_config_backorders' => 1,
                'notify_stock_qty' => 1,
                'use_config_notify_stock_qty' => 1,
                'enable_qty_increments' => 0,
                'use_config_enable_qty_inc' => 1,
                'qty_increments' => 0,
                'use_config_qty_increments' => 1,
                'is_in_stock' => 0,
                'low_stock_date' => null,
                'stock_status_changed_auto' => 0,
                'is_decimal_divided' => 0,
                'allow_check_availability' => 0,
                'qty_on_order' => 0,
            ];
        } else {
            // less then 1.6.0.0
            $defaultStockData = [
                'manage_stock' => 1,
                'use_config_manage_stock' => 1,
                'qty' => 0,
                'min_sale_qty' => 1,
                'use_config_min_sale_qty' => 1,
                'max_sale_qty' => 10000,
                'use_config_max_sale_qty' => 1,
                'is_qty_decimal' => 0,
                'backorders' => 0,
                'use_config_backorders' => 1,
                'notify_stock_qty' => 1,
                'use_config_notify_stock_qty' => 1,
                'enable_qty_increments' => 0,
                'use_config_enable_qty_increments' => 1,
                'qty_increments' => 0,
                'use_config_qty_increments' => 1,
                'is_in_stock' => 0,
                'low_stock_date' => null,
                'stock_status_changed_automatically' => 0,
                'allow_check_availability' => 0,
                'qty_on_order' => 0,
            ];
        }

        //'min_qty'                            => 0,
        //'use_config_min_qty'                 => 1,
        $entityTable = Mage::getResourceModel('cataloginventory/stock_item')->getMainTable();
        $helper = Mage::helper('catalogInventory');

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $stockData = [];

            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->isRowAllowedToImport($rowData, $rowNum)) {
                    continue;
                }

                // only SCOPE_DEFAULT can contain stock data
                if (self::SCOPE_DEFAULT == $this->getRowScope($rowData)) {
                    $row = array_merge(
                        $defaultStockData,
                        array_intersect_key($rowData, $defaultStockData)
                    );
                    $row['product_id'] = $this->_newSku[$rowData[self::COL_SKU]]['entity_id'];
                    $row['stock_id'] = 1;
                    /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                    $stockItem = Mage::getModel('cataloginventory/stock_item', $row);

                    if ($helper->isQty($this->_newSku[$rowData[self::COL_SKU]]['type_id'])) {
                        if ($stockItem->verifyNotification()) {
                            $stockItem->setLowStockDate(Mage::app()->getLocale()
                                ->date(null, null, null, false)
                                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                            );
                        }
                        $stockItem->setStockStatusChangedAutomatically((int) !$stockItem->verifyStock());
                    } else {
                        $stockItem->setQty(0);
                    }
                    if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
                        $stockItem->unsetData('stock_status_changed_automatically');
                        $stockItem->unsetData('use_config_enable_qty_increments');
                    }
                    $stockData[] = $stockItem->getData();
                }
            }
            if ($stockData) {
                $this->_connection->insertOnDuplicate($entityTable, $stockData);
            }
        }

        return $this;
    }
}
