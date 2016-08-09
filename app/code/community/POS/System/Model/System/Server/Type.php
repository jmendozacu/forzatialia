<?php

/**
 * Types of servers.
 */
class POS_System_Model_System_Server_Type extends Mage_Core_Model_Abstract
{
    const TYPE_SHARED_LO = 'sharedlo';
    const TYPE_SHARED_HI = 'sharedhi';
    const TYPE_VPS = 'dedicated';
    const TYPE_HUGEDATA = 'hugedata';

    /**
     * @var array - options of model
     */
    protected $_items = [
        self::TYPE_SHARED_LO => 'Shared Low Resources',
        self::TYPE_SHARED_HI => 'Shared High Resources',
        self::TYPE_HUGEDATA => 'Shared / Huge Data (> 20k products)',
        self::TYPE_VPS => 'VPS / VDS / Dedicated',
    ];

    protected $_default = self::TYPE_SHARED_HI;

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
