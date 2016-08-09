<?php

class POS_System_Model_Config_Sharedlo extends Varien_Object
{
    public function __construct()
    {
        $this->_data['products_per_iteration'] = 50;
        $this->_data['max_execution_time'] = 240;
    }
}
