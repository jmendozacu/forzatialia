<?php

class POS_System_Model_Config_Dedicated extends Varien_Object
{
    public function __construct()
    {
        $this->_data['products_per_iteration'] = 500;
        $this->_data['max_execution_time'] = 3600;
    }
}
