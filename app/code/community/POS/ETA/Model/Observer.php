<?php

class POS_ETA_Model_Observer
{
    public function hookIntoCatalogProductPrepareSave($observer)
    {
        // get initial data
        $product = $observer->getProduct();
        $request = $observer->getRequest();
        $event = $observer->getEvent();
        $postData = $request->getPost();

        $stockStatus = $postData['product']['stock_data']['is_in_stock'];
        $allowCheckAvailabilityStatus = isset($postData['product']['stock_data']['allow_check_availability_status']) ? $postData['product']['stock_data']['allow_check_availability_status'] : 0;
        $useConfigAllowCheckAvailabilityStatus = isset($postData['product']['stock_data']['use_config_allow_check_availability_status']) && 1 == $postData['product']['stock_data']['use_config_allow_check_availability_status'] ? 1 : 0;
        // retreive prepared product stock data
        $stockData = $product->getStockData();

        $stockData['use_config_allow_check_availability_status'] = $useConfigAllowCheckAvailabilityStatus;

        if ($stockStatus == 1) {
            $stockData['allow_check_availability'] = 0;
        } elseif ($stockStatus == 2) {
            $stockData['is_in_stock'] = 1;
            $stockData['allow_check_availability'] = 1;
        }
        $product->addData(array('stock_data' => $stockData));

        return $this;
    }

    public function hookPageLoadBefore($observer)
    {
        $moduleDisabled = Mage::getStoreConfig('advanced/modules_disable_output/POS_System');
        if (!$moduleDisabled) {
            Mage::getConfig()->setNode('global/blocks/catalog/rewrite/product_view_type_configurable', 'POS_ETA_Block_Catalog_Product_View_Type_Configurable');
        }
    }

    public function catalogProductIsSalableAfter($observer)
    {
        // retreive observer data
        $product = $observer->getProduct();
        $salable = $observer->getSalable();

        $stockItem = $product->getStockItem();

      //  if ($stockItem->getAllowCheckAvailability()) {
            if (!Mage::registry('allow_check_stock_status')) {
                Mage::register('allow_check_stock_status', '1');
            }
       // }
    }

    protected function _getStockModel()
    {
        return Mage::getModel('cataloginventory/stock');
    }

    /**
     * Set Order Eta.
     */
    public function setOrderEta($observer)
    {
        $model = Mage::getModel('eta/eta');
        $model->prepareCartEtaData();
        if (Mage::registry('etaData')) {
            $etaData = Mage::registry('etaData');
            $etaCombined = $etaData['combined'];
            $order = $observer->getEvent()->getOrder();
            if ($etaCombined) {
                $order->setOrderEtaCombined($etaCombined);
            } else {
                $order->setOrderEtaCombined('unavailable');
            }
        }
    }
}
