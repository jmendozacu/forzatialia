<?php

/**
 *
 */
class POS_ClickAndCollect_Model_System_Config_Source_Availabledelivery
{
    /**
     * @var array - options of model
     */
    protected $_items = [];

    public function toOptionArray($isMultiSelect = false)
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

        $options = [];

        foreach ($methods as $_code => $_method) {
            if (!$_title = Mage::getStoreConfig("carriers/$_code/title")) {
                $_title = $_code;
            }
            if ($_code != 'clickandcollect') {
                $options[] = ['value' => $_code, 'label' => $_title." ($_code)"];
            }
        }

        //if($isMultiSelect)
        //{
            //array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        //}

        return $options;
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
