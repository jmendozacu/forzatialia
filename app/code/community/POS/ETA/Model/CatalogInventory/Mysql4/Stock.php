<?php

/**
 */

/**
 */
class POS_ETA_Model_CatalogInventory_Mysql4_Stock extends Mage_CatalogInventory_Model_Mysql4_Stock
{
    protected $_isConfigAllowCheckAvailability;
    protected $_etaCalculationStatus;

    protected function _construct()
    {
        return parent::_construct();
    }

    /**
     * Load some inventory configuration settings.
     */
    protected function _initConfig()
    {
        if (!$this->_isConfig) {
            $this->_isConfigAllowCheckAvailability = (int) Mage::helper('eta')->getGlobalAllowCheckAvailabilityStatus();
            $this->_etaCalculationStatus = (int) Mage::helper('eta')->getEtaCalculationEnabled();
        }

        return parent::_initConfig();
    }

    public function updateSetOutOfStock()
    {
        $this->_initConfig();

        // field 'stock_status_changed_automatically' have changed to 'stock_status_changed_auto' on 1.6.0.0 version
        if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
            // 1.6.0.0 or greater
            $this->_getWriteAdapter()->update($this->getTable('cataloginventory/stock_item'),
                ['is_in_stock' => 0, 'stock_status_changed_auto' => 1],
                sprintf('stock_id = %d
                    AND (is_in_stock = 1 OR is_in_stock = 2)
                    AND (use_config_manage_stock = 1 AND 1 = %d OR use_config_manage_stock = 0 AND manage_stock = 1)
                    AND (use_config_backorders = 1 AND %d = %d OR use_config_backorders = 0 AND backorders = %d)
                    AND (
                            (
                                %d = %d
                            )
                                OR
                            (
                                allow_check_availability = 0
                            )
                                OR
                            (
                                use_config_allow_check_availability_status = 1 AND %d = %d
                                OR use_config_allow_check_availability_status = 0 AND allow_check_availability_status = 0
                            )
                                OR
                            ((
                                use_config_allow_check_availability_status = 1 AND %d = %d
                                OR use_config_allow_check_availability_status = 0 AND allow_check_availability_status = 1
                            ) AND (
                                allow_check_availability = 1
                            ) AND (
                                use_config_min_qty = 1 AND ((qty + qty_on_order) <= %d) OR use_config_min_qty = 0 AND ((qty + qty_on_order) <= min_qty)
                            ))
                        )
                    AND (use_config_min_qty = 1 AND qty <= %d OR use_config_min_qty = 0 AND qty <= min_qty)
                    AND product_id IN (SELECT entity_id FROM %s WHERE type_id IN (%s))',
                    $this->_stock->getId(),
                    $this->_isConfigManageStock,
                    Mage_CatalogInventory_Model_Stock::BACKORDERS_NO, $this->_isConfigBackorders, Mage_CatalogInventory_Model_Stock::BACKORDERS_NO,
                    $this->_etaCalculationStatus, 0,
                    $this->_isConfigAllowCheckAvailability, POS_ETA_Model_CatalogInventory_Stock::GLOBAL_ALLOW_CHECK_AVAILABILITY_STATUS_NO,
                    $this->_isConfigAllowCheckAvailability, POS_ETA_Model_CatalogInventory_Stock::GLOBAL_ALLOW_CHECK_AVAILABILITY_STATUS_YES,
                    $this->_configMinQty,
                    $this->_configMinQty,
                    $this->getTable('catalog/product'), $this->_getWriteAdapter()->quote($this->_configTypeIds)
            ));
        } else {
            // less then 1.6.0.0
            $this->_getWriteAdapter()->update($this->getTable('cataloginventory/stock_item'),
                ['is_in_stock' => 0, 'stock_status_changed_automatically' => 1],
                sprintf('stock_id = %d
                    AND (is_in_stock = 1 OR is_in_stock = 2)
                    AND (use_config_manage_stock = 1 AND 1 = %d OR use_config_manage_stock = 0 AND manage_stock = 1)
                    AND (use_config_backorders = 1 AND %d = %d OR use_config_backorders = 0 AND backorders = %d)
                    AND (
                            (
                                %d = %d
                            )
                                OR
                            (
                                allow_check_availability = 0
                            )
                                OR
                            (
                                use_config_allow_check_availability_status = 1 AND %d = %d
                                OR use_config_allow_check_availability_status = 0 AND allow_check_availability_status = 0
                            )
                                OR
                            ((
                                use_config_allow_check_availability_status = 1 AND %d = %d
                                OR use_config_allow_check_availability_status = 0 AND allow_check_availability_status = 1
                            ) AND (
                                allow_check_availability = 1
                            ) AND (
                                use_config_min_qty = 1 AND ((qty + qty_on_order) <= %d) OR use_config_min_qty = 0 AND ((qty + qty_on_order) <= min_qty)
                            ))
                        )
                    AND (use_config_min_qty = 1 AND qty <= %d OR use_config_min_qty = 0 AND qty <= min_qty)
                    AND product_id IN (SELECT entity_id FROM %s WHERE type_id IN (%s))',
                    $this->_stock->getId(),
                    $this->_isConfigManageStock,
                    Mage_CatalogInventory_Model_Stock::BACKORDERS_NO, $this->_isConfigBackorders, Mage_CatalogInventory_Model_Stock::BACKORDERS_NO,
                    $this->_etaCalculationStatus, 0,
                    $this->_isConfigAllowCheckAvailability, POS_ETA_Model_CatalogInventory_Stock::GLOBAL_ALLOW_CHECK_AVAILABILITY_STATUS_NO,
                    $this->_isConfigAllowCheckAvailability, POS_ETA_Model_CatalogInventory_Stock::GLOBAL_ALLOW_CHECK_AVAILABILITY_STATUS_YES,
                    $this->_configMinQty,
                    $this->_configMinQty,
                    $this->getTable('catalog/product'), $this->_getWriteAdapter()->quote($this->_configTypeIds)
            ));
        }
    }

    /**
     * Set items in stock basing on their quantities and config settings.
     */
    public function updateSetInStock()
    {
        // field 'stock_status_changed_automatically' have changed to 'stock_status_changed_auto' on 1.6.0.0 version
        if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
            // 1.6.0.0 or greater
            $this->_initConfig();
            $this->_getWriteAdapter()->update($this->getTable('cataloginventory/stock_item'),
                ['is_in_stock' => 1],
                sprintf('stock_id = %d
                    AND is_in_stock = 0
                    AND stock_status_changed_auto = 1
                    AND (use_config_manage_stock = 1 AND 1 = %d OR use_config_manage_stock = 0 AND manage_stock = 1)
                    AND ((%d = %d AND (use_config_allow_check_availability_status = 1 AND %d = %d OR use_config_allow_check_availability_status = 0 AND allow_check_availability_status = 1) AND allow_check_availability = 1 AND (qty + qty_on_order) > min_qty)
                    OR (use_config_min_qty = 1 AND qty > %d OR use_config_min_qty = 0 AND qty > min_qty))
                    AND product_id IN (SELECT entity_id FROM %s WHERE type_id IN (%s))',
                    $this->_stock->getId(),
                    $this->_isConfigManageStock,
                    $this->_etaCalculationStatus, 1,
                    POS_ETA_Model_CatalogInventory_Stock::GLOBAL_ALLOW_CHECK_AVAILABILITY_STATUS_YES, $this->_isConfigAllowCheckAvailability,
                    $this->_configMinQty,
                    $this->getTable('catalog/product'), $this->_getWriteAdapter()->quote($this->_configTypeIds)
            ));
        } else {
            // less then 1.6.0.0
            $this->_initConfig();
            $this->_getWriteAdapter()->update($this->getTable('cataloginventory/stock_item'),
                ['is_in_stock' => 1],
                sprintf('stock_id = %d
                    AND is_in_stock = 0
                    AND stock_status_changed_automatically = 1
                    AND (use_config_manage_stock = 1 AND 1 = %d OR use_config_manage_stock = 0 AND manage_stock = 1)
                    AND ((%d = %d AND (use_config_allow_check_availability_status = 1 AND %d = %d OR use_config_allow_check_availability_status = 0 AND allow_check_availability_status = 1) AND allow_check_availability = 1 AND (qty + qty_on_order) > min_qty)
                    OR (use_config_min_qty = 1 AND qty > %d OR use_config_min_qty = 0 AND qty > min_qty))
                    AND product_id IN (SELECT entity_id FROM %s WHERE type_id IN (%s))',
                    $this->_stock->getId(),
                    $this->_isConfigManageStock,
                    $this->_etaCalculationStatus, 1,
                    POS_ETA_Model_CatalogInventory_Stock::GLOBAL_ALLOW_CHECK_AVAILABILITY_STATUS_YES, $this->_isConfigAllowCheckAvailability,
                    $this->_configMinQty,
                    $this->getTable('catalog/product'), $this->_getWriteAdapter()->quote($this->_configTypeIds)
            ));
        }
    }
}
