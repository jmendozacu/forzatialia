<?php

/**
 */

/**
 */
class POS_ETA_Block_Catalog_Product_View_Type_Simple extends Mage_Catalog_Block_Product_View_Type_Simple
{
    public function _construct()
    {
        parent::_construct();
    }

    public function getEtaUrl()
    {
        return $this->getBaseUrl().'poseta/ajax/geteta/';
    }

    public function getEtaMessage()
    {
        return Mage::helper('eta')->getEtaMessage();
    }
}
