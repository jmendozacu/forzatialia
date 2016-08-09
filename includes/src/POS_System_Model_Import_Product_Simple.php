<?php

class POS_System_Model_Import_Product_Simple extends Mage_ImportExport_Model_Import_Entity_Product_Type_Simple
{

    /**
     * Have we check attribute for is_required? Used as last chance to disable this type of check.
     *
     * @param string $attrCode
     * @return bool
     */
    protected function _isAttributeRequiredCheckNeeded($attrCode)
    {
        if (in_array($attrCode, array('short_description', 'description'))) {
            return false;
        }

        return parent::_isAttributeRequiredCheckNeeded($attrCode);
    }

}
 
