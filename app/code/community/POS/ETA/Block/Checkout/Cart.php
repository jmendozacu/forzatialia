<?php

class POS_ETA_Block_Checkout_Cart extends Mage_Checkout_Block_Cart
{
    protected $_etaDataReady = null;

    /**
     * Prepare ETA data.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getOrderEta()
    {
        if (Mage::registry('etaData')) {
            $etaData = Mage::registry('etaData');

            return isset($etaData['combined']) ? $etaData['combined'] : '';
        }

        return false;
    }

    public function prepareCartEtaData()
    {
        $model = Mage::getModel('eta/eta');
        $model->prepareCartEtaData();
    }

    public function getUnavailableMessage()
    {
        return Mage::helper('eta')->getUnavailableMessage();
    }
}
