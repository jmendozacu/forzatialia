<?php

class POS_System_Model_Import_Product_Configurable extends Mage_ImportExport_Model_Import_Entity_Product_Type_Configurable
{
    /**
     * Attributes' codes which will be allowed anyway, independently from its visibility property.
     *
     * @var array
     */
    protected $_forcedAttributesCodes = [
        'store_pickup_rule',
    ];

    /**
     * Have we check attribute for is_required? Used as last chance to disable this type of check.
     *
     * @param string $attrCode
     *
     * @return bool
     */
    protected function _isAttributeRequiredCheckNeeded($attrCode)
    {
        if (in_array($attrCode, ['short_description', 'description'])) {
            return false;
        }

        return parent::_isAttributeRequiredCheckNeeded($attrCode);
    }
}
