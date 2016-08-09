<?php

/**
 * Types of Import method.
 */
class POS_System_Model_System_Method extends Mage_Core_Model_Abstract
{
    const IMPORT_TYPE_MODEL = 'model'; // saving models
    const IMPORT_TYPE_IMPORT = 'import'; // using import/export module

    /**
     * @var array - options of model
     */
    protected $_items = [
        self::IMPORT_TYPE_MODEL => 'Compatibility Mode',
        self::IMPORT_TYPE_IMPORT => 'Fast Mode',
    ];

    protected $_default = self::IMPORT_TYPE_MODEL;

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
