<?php

class POS_System_Model_Attr extends Mage_Core_Model_Abstract
{


	public function _construct()
    {
        parent::_construct();
        $this->_init('retailexpress/attr');
    }


    public function getItemByRexCode($code, $id)
    {
        return $this->getCollection()
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('rex_id', $id)
            ->setOrder('attr_id', 'DESC')
            ->getFirstItem();
    }


    public function getMagentoIdByRexCodeDirectly($code, $id)
    {
        return $this->getItemByRexCode($code, $id)->getMagentoId();
    }


    public function deleteMagentoIdByRexCodeDirectly($code, $id)
    {
        $item = $this->getItemByRexCode($code, $id);
        if ($item && $item->getId()) {
            $item->delete();
        }
    }


    public function getMagentoIdByRexCode($code, $id)
    {
        $item = $this->getItemByRexCode($code, $id);
        $return = $item->getMagentoId();
        $conn =  Mage::getSingleton('core/resource')->getConnection('core_write');
        $optionTable = Mage::getSingleton('retailexpress/attr')->getResource()->getTable('eav/attribute_option');
        $result = $conn->query("SELECT * FROM $optionTable WHERE option_id = ?", array($return));
        if (!count($result->fetchAll(PDO::FETCH_ASSOC))) {
            $item->delete();
            return 0;
        }

        return $return;
    }


}
