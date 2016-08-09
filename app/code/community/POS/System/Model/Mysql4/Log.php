<?php

class POS_System_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('retailexpress/log', 'log_id');
    }

    /*
     * Prepare data for save
     *
     * @param  Mage_Core_Model_Abstract $object
     * @return array
     */
}
