<?php

class POS_ETA_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    public function getItemEta($productId)
    {
        if (Mage::registry('etaData')) {
            $etaData = Mage::registry('etaData');
            if (isset($etaData[$productId])) {
                return $etaData[$productId];
            } else {
                return false;
            }
        }

        return '';
    }
}
