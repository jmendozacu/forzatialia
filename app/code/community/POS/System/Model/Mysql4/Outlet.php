<?php

class POS_System_Model_Mysql4_Outlet extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('retailexpress/outlet', 'outlet_id');
    }
}
