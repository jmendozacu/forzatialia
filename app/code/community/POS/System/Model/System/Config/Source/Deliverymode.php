<?php

/**
 *
 */
class POS_System_Model_System_Config_Source_Deliverymode
{
    const DELIVERY_MODE_HOME = 'home';
    const DELIVERY_MODE_STORE = 'store';
    const DELIVERY_MODE_WAREHOUSE = 'warehouse';

    /**
     * @var array - options of model
     */
    protected $_items = [
        self::DELIVERY_MODE_HOME => 'Home Delivery',
        self::DELIVERY_MODE_STORE => 'Store Pickup',
        self::DELIVERY_MODE_WAREHOUSE => 'Warehouse Pickup',
    ];

    protected $_default = self::DELIVERY_MODE_STORE;

    public function toOptionArray()
    {
        $array = [];
        foreach ($this->_items as $key => $value) {
            $array[] = [
                'value' => $key,
                'label' => $value,
            ];
        }

        return $array;
    }

    /**
     * get text label for id.
     *
     * @param $name string - id
     *
     * @return string|null - label text for config
     */
    public function getValueById($name)
    {
        if (!isset($this->_items[$name])) {
            $name = $this->_default;
        }

        if (!isset($this->_items[$name])) {
            return;
        }

        return $this->_items[$name];
    }
}
