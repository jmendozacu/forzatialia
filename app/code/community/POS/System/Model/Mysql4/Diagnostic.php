<?php

class POS_System_Model_Mysql4_Diagnostic extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('retailexpress/diagnostic', 'list_id');
    }

    /*
     * Prepare data for save
     *
     * @param  Mage_Core_Model_Abstract $object
     * @return array
     */
}
