<?php

class POS_System_Model_Mysql4_Conf extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Primery key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;


    public function _construct()
    {
        $this->_init('retailexpress/conf', 'conf_id');
    }


}
