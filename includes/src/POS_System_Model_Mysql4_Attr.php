<?php

class POS_System_Model_Mysql4_Attr extends Mage_Core_Model_Mysql4_Abstract
{


    public function _construct()
    {  
        $this->_init('retailexpress/attr', 'attr_id');
    }


}
