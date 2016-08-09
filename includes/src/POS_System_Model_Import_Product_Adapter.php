<?php
/**
 * overwritten Import class for REX Import adapter
 */

class POS_System_Model_Import_Product_Adapter extends Mage_ImportExport_Model_Import_Entity_Product
{

    /**
     * Constructor.
     *
     * @return void
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
     * @return Mage_ImportExport_Model_Import_Entity_Product
     */
    protected function _initTypeModels()
    {
        parent::_initTypeModels();
        $this->_productTypeModels['simple'] = Mage::getModel('retailexpress/import_product_simple', array($this, 'simple'));
        $this->_productTypeModels['configurable'] = Mage::getModel('retailexpress/import_product_configurable', array($this, 'configurable'));
        return $this;
    }

    public function resetImportedCounters()
    {
        $this->_errors = array();
        $this->_errorsCount = 0;
        $this->_errorsLimit = 10000;
        $this->_processedRowsCount = 0;
        $this->_processedEntitiesCount = 0;
        $this->_rowsToSkip = array();
        $this->_newSku = array();
        $this->_validatedRows = array();
    }


    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased.
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $indexValAttrs OPTIONAL Additional attributes' codes with index values.
     * @return array
     */
    public function getAttributeOptions(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $indexValAttrs = array())
    {
        if (!Mage::getConfig()->getModuleConfig('Infinity_Allcooks')->is('active', 'true') || ("brand" != $attribute->getAttributeCode())) {
                return parent::getAttributeOptions($attribute, $indexValAttrs);
        }

        $options = array();

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


}
 
