<?php

class POS_System_Model_Outlet extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('retailexpress/outlet');
    }

    /**
     * Load outlet by system increment identifier.
     *
     * @param int $fulfilmentOutletId
     *
     * @return POS_System_Model_Outlet
     */
    public function loadByFulfilmentOutletId($fulfilmentOutletId)
    {
        return $this->loadByAttribute('fulfilment_outlet_id', $fulfilmentOutletId);
    }

    /**
     * Load oulet by attribute value. Attribute value should be unique.
     *
     * @param string $attribute
     * @param string $value
     *
     * @return POS_System_Model_Outlet
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);

        return $this;
    }
}
