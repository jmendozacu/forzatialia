<?php

class POS_System_Model_Orders extends Varien_Data_Collection
{
    /**
     * Load data.
     *
     * @return Varien_Data_Collection
     */
    public function loadREX()
    {
        $orders = Mage::helper('retailexpress')->getOrdersHistory();
        foreach ($orders as $o) {
            $o['order_currency_code'] = 'AUD';
            $this->addItem(new Varien_Object($o));
        }
    }
}
