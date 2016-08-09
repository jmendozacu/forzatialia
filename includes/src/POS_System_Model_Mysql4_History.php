<?php

class POS_System_Model_Mysql4_History extends Mage_Core_Model_Mysql4_Abstract
{


    public function _construct()
    {  
        $this->_init('retailexpress/history', 'history_id');
    }


    /**
     * Prepare data for save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt(now());
        }

        return parent::_prepareDataForSave($object);
    }
}
