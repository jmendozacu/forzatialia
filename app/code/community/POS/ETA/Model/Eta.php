<?php

class POS_ETA_Model_Eta extends Mage_Checkout_Model_Cart
{
    protected $_helper = null;
    protected $_etaDataReady = null;

    public function __construct()
    {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('eta');
        }
    }

    public function prepareCartEtaData()
    {
        if (!$this->_etaDataReady) {
            $this->_prepareCartEtaData();
        }
        $this->_etaDataReady = true;
    }

    protected function _prepareCartEtaData()
    {
        $data = [];
        $products = [];
        $stockItemModel = Mage::getModel('cataloginventory/stock_item');
        /* @var $item Mage_Sales_Model_Quote_Item */

        foreach ($this->getItems() as $item) {
            $product = $item->getProduct();
            $option = $item->getOptionByCode('product_type');
            if ($option) {
                $product = $option->getProduct();
            }
            $stockItem = $stockItemModel->loadByProduct($product);

            $product = Mage::getModel('catalog/product')->load($product->getId());

            if ($stockItem->getAllowCheckAvailability()) {
                //store the configurable product qty ordered into an array to be picked up by simple products
                if ($product->isConfigurable()) {
                    $confProductQty[$item->getItemId()] = $item->getQty() + $stockItem->getMinQty();
                }

                if ($product->getRexProductId() && !$product->isConfigurable()) {
                    $data[] = [
                        'magentoId' => $product->getId(),
                        'ProductID' => (int) $product->getRexProductId(),
                        //'QtyOrdered' => (int)$stockItem->getMinQty() > 0 ? (int)$stockItem->getMinQty() : 1,
                        'QtyOrdered' => (int) (isset($confProductQty[$item->getParentItemId()])) ? $confProductQty[$item->getParentItemId()] : $item->getQty() + $stockItem->getMinQty(),
                    ];
                    $products[$product->getRexProductId()] = $product->getId();
                }
            }
        }

        if (empty($products)) {
            return;
        }

        $model = Mage::getModel('eta/eta');
        if (!Mage::registry('etaProducts')) {
            Mage::register('etaProducts', $products);
        }
        $etaData = $this->_productGetEtaDateCombined($data);

        if (!Mage::registry('etaData')) {
            Mage::register('etaData', $etaData);
        }
    }

    public function _productGetEtaDateCombined($data)
    {
        try {
            $result = Mage::getModel('retailexpress/retail')->productGetEtaDate($data);

            $etaData = [];

            if (!Mage::registry('etaProducts')) {
                throw new Exception('Error Getting ETA');
            }
            $products = Mage::registry('etaProducts');

            $combinedEta = $result[0]['eta'];
            $combinedEtaSuccess = false;

            foreach ($result as $item) {
                if ($item['eta'] == '' || $item['eta'] == 0) {
                    $etaData[$products[$item['id']]] = 'Unknown';
                } else {
                    /*
                    todo: this is only a containment for the same day ETA returned date.
                    if eta was the same date on server, removing the node of eta so it should show available on cart
                    */
                    if ($this->_convertDate($item['eta']) == date('d/m/Y')) {
                        unset($etaData[$products[$item['id']]]);
                    } else {
                        //get the most nearest date of eta for the combined ETA
                        if ($combinedEta < $item['eta']) {
                            $combinedEta = $item['eta'];
                        }
                        $etaData[$products[$item['id']]] = $this->_convertDate($item['eta']);
                        $combinedEtaSuccess = true;
                    }
                }
            }

            if ($combinedEtaSuccess) {
                $etaData['combined'] = $this->_convertDate($combinedEta);
            } else {
                unset($etaData['combined']);
            }

            return $etaData;
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
    }

    public function productGetEtaDate($data)
    {
        $result = Mage::getSingleton('retailexpress/retail')->productGetEtaDate($data);

        if (!isset($result[0]['eta']) || $result[0]['eta'] == '') {
            return [
                'error' => true,
                'success' => false,
                'eta' => $this->_helper->getUnavailableMessage(),
            ];
        }

        return [
            'error' => false,
            'success' => true,
            'eta' => $this->_convertDate($result[0]['eta']),
        ];
    }

    public function _convertDate($date)
    {
        return $this->_helper->formatEtaDate($date);
    }
}
