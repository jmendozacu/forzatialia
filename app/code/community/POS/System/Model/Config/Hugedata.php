<?php

class POS_System_Model_Config_Hugedata extends Varien_Object
{
    public function __construct()
    {
        $this->_data['products_per_iteration'] = 500;
        $this->_data['max_execution_time'] = 3600; //1 hour
        $this->_data['config_bulk_time'] = 1200;
    }
}
