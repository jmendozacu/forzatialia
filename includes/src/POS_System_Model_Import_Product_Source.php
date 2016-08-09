<?php

/**
 * overwritten Import class for REX Import resource
 */

class POS_System_Model_Import_Product_Source extends Mage_ImportExport_Model_Import
{

    /**
     * DB data source model getter.
     *
     * @static
     * @return Mage_ImportExport_Model_Mysql4_Import_Data
     */
    public static function getDataSourceModelREX()
    {
        return Mage::getSingleton('retailexpress/import_product_resource');
    }

    /**
     * Import source structure to DB.
     *
     * @return bool
     */
    public function importSource()
    {
        $this->setData(array(
            'entity'   => self::getDataSourceModelREX()->getEntityTypeCode(),
            'behavior' => self::getDataSourceModelREX()->getBehavior()
        ));
        $result = $this->_getEntityAdapter()->importData();
        return $result;
    }

    /**
     * Create instance of entity adapter and returns it.
     *
     * @throws Mage_Core_Exception
     * @return Mage_ImportExport_Model_Import_Entity_Abstract
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $this->_entityAdapter = Mage::getModel('retailexpress/import_product_adapter');
            $this->_entityAdapter->setParameters($this->getData());
        }
        return $this->_entityAdapter;
    }

    public function resetImportedCounters()
    {
        $this->_getEntityAdapter()->resetImportedCounters();
    }
}
