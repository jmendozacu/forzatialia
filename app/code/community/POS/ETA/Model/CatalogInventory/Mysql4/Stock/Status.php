<?php

/**
 * Stock status model override.
 *
 * Override Mage_CatalogInventory_Model_Mysql4_Stock_Status for prepareCatalogProductIndexSelect and addIsInStockFilterToCollection
 */
class POS_ETA_Model_CatalogInventory_Mysql4_Stock_Status extends Mage_CatalogInventory_Model_Mysql4_Stock_Status
{
    /**
     * Resource model initialization.
     */
    protected function _construct()
    {
        return parent::_construct();
    }

    /**
     * Add stock status limitation to catalog product price index select object.
     *
     * @param Varien_Db_Select    $select
     * @param string|Zend_Db_Expr $entityField
     * @param string|Zend_Db_Expr $websiteField
     *
     * @return Mage_CatalogInventory_Model_Mysql4_Stock_Status
     */
    public function prepareCatalogProductIndexSelect(Varien_Db_Select $select, $entityField, $websiteField)
    {
        $select->join(
            array('ciss' => $this->getMainTable()),
            "ciss.product_id = {$entityField} AND ciss.website_id = {$websiteField}",
            array()
        );
        //$select->where('ciss.stock_status=?', Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);
        $select->where('ciss.stock_status=1 OR ciss.stock_status=2');

        return $this;
    }

    /**
     * Add only is in stock products filter to product collection.
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     *
     * @return Mage_CatalogInventory_Model_Stock_Status
     */
    public function addIsInStockFilterToCollection($collection)
    {
        $websiteId = Mage::app()->getStore($collection->getStoreId())->getWebsiteId();
        $collection->getSelect()
            ->join(
                ['stock_status_index' => $this->getMainTable()],
                'e.entity_id = stock_status_index.product_id AND stock_status_index.website_id = '.$websiteId
                    .' AND stock_status_index.stock_id = '.Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
                [])
            //->where('stock_status_index.stock_status=?', Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK);
            ->where('stock_status_index.stock_status=1 OR stock_status_index.stock_status=2');

        return $this;
    }
}
