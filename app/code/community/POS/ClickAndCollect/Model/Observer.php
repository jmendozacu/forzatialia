<?php

class POS_ClickAndCollect_Model_Observer extends Mage_Core_Block_Abstract
{
    /**
     * Set Order Comment and FulfilmentOutletId.
     */
    public function setCustomerOrderData($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $shippingMethod = $order->getShippingMethod();
        $shippingMethod = explode('_', $shippingMethod);
        if (isset($shippingMethod[0]) && $shippingMethod[0] == 'clickandcollect' && isset($shippingMethod[4])) {
            $fulfilmentouletid = $shippingMethod[4];
        } else {
            $fulfilmentouletid = '';
        }
        $orderComment = $this->getRequest()->getPost('clickandcollect_order_comment');
        $orderComment = trim($orderComment);
        $order->setClickandcollectOrderFulfilmentouletid($fulfilmentouletid);
        $order->setClickandcollectOrderComment($orderComment);

        $orderComment = nl2br($orderComment);
        $order->addStatusHistoryComment($orderComment);
    }

    public function sendTrackingCode($observer)
    {
        $shipment = $observer->getEvent()->getTrack();
        Mage::helper('retailexpress')->OrderDeliveryUpdate($shipment);
    }
}
