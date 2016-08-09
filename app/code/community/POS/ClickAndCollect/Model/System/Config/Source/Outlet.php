<?php

class POS_ClickAndCollect_Model_System_Config_Source_Outlet
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $outletCollection = $this->_getOutletCollection();
        $option = [];
        foreach ($outletCollection as $outlet) {
            $option[] = [
                'value' => $outlet->getFulfilmentOutletId(),
                'label' => $outlet->getOutletName(),
            ];
        }

        return $option;
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        $outletCollection = $this->_getOutletCollection();
        $option = [];
        foreach ($outletCollection as $outlet) {
            $option[$outlet->getFulfilmentOutletId()] = $outlet->getOutletName();
        }

        return $option;
    }

    /**
     * retreive Outlet collection.
     *
     * @return POS_System_Outlet
     */
    protected function _getOutletCollection()
    {
        $model = Mage::getModel('retailexpress/outlet');
        if ($model) {
            return $model->getCollection()->addAttributeToSort('outlet_name', 'ASC');
        }

        return [];
    }
}
