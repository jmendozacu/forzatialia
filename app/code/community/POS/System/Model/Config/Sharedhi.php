<?php

class POS_System_Model_Config_Sharedhi extends Varien_Object
{
    public function __construct()
    {
        $this->_data['products_per_iteration'] = 200;
        $this->_data['max_execution_time'] = 280;
    }
}
