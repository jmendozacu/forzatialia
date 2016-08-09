<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class POS_System_CartController extends Mage_Checkout_CartController
{
    /**
     * Initialize coupon.
     */
    public function couponPostAction()
    {
        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') != 1) {
            Mage::helper('retailexpress')->checkVoucher($couponCode);
        }

        return parent::couponPostAction();
    }
}
