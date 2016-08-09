<?php

class POS_System_Model_Mysql4_Attr_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('retailexpress/attr');
    }

}