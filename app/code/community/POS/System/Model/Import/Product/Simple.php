<?php

class POS_System_Model_Import_Product_Simple extends Mage_ImportExport_Model_Import_Entity_Product_Type_Simple
{
    /**
     * Attributes' codes which will be allowed anyway, independently from its visibility property.
     *
     * @var array
     */
    protected $_forcedAttributesCodes = [
        'related_tgtr_position_behavior', 'related_tgtr_position_limit',
        'upsell_tgtr_position_behavior', 'upsell_tgtr_position_limit',
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
