<?php

class POS_System_Model_Config extends Varien_Object
{
    /* default variables */
    protected $_data = [
        'product_trust_time' => 200,
        'sync_bulk_timeout' => 3,
        'debug_in_history' => false,
        'config_bulk_time' => 240,
        'products_per_iteration' => 200,
        'max_wait_iterations' => 35,
        'max_memory_consume' => 3072,
        'max_execution_time' => 0,
    ];

    public function __construct()
    {
    }
}
