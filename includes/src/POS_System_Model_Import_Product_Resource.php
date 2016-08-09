<?php

/**
 * resource class for REX product import
 */
class POS_System_Model_Import_Product_Resource extends  Mage_ImportExport_Model_Mysql4_Import_Data //Mage_ImportExport_Model_Resource_Import_Data
{

    protected $_bunch = array();
    protected $_bunched = true;

    /**
     * Clean all bunches from table.
     *
     * @return Varien_Db_Adapter_Interface
     */
    public function cleanBunches()
    {
        return $this->_getWriteAdapter();
    }

    /**
     * Return behavior from import data table.
     *
     * @throws Exception
     * @return string
     */
    public function getBehavior()
    {
        return Mage_ImportExport_Model_Import::BEHAVIOR_APPEND;
    }

    /**
     * Return entity type code from import data table.
     *
     * @throws Exception
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'catalog_product';
    }

    /**
     * set bunch for import
     *
     * @return void
     */
    public function setBunch($bunch)
    {
        $this->_bunched = false;
        $this->_bunch = $bunch;
    }

    /**
     * Get next bunch of validated rows.
     *
     * @return array|null
     */
    public function getNextBunch()
    {
        if ($this->_bunched) {
            $this->_bunched = false;
            return false;
        }

        $this->_bunched = true;
        return $this->_bunch;
    }

}
