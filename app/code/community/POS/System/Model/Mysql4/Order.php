<?php

class POS_System_Model_Mysql4_Order extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Primery key auto increment flag.
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Use is object new method for save of object.
     *
     * @var bool
     */
    protected $_useIsObjectNew = true;

    public function _construct()
    {
        $this->_init('retailexpress/order', 'order_id');
    }
}
