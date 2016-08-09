<?php

class POS_ETA_AjaxController extends Mage_Core_Controller_Front_Action
{
    protected $_productId;
    protected $_rexProductId;

    public function _construct()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest()) {
            echo 'non ajax request';
            exit;
        }

        $this->_productId = $request->getParam('productId');
        $this->_rexProductId = $this->_getRexProductId();
    }

    public function getetaAction()
    {
        if (!$this->_productId || $this->_productId == '' || !$this->_rexProductId || $this->_rexProductId == '') {
            echo json_encode([
                'error' => true,
                'success' => false,
                'eta' => 'Please select product',
            ]);
            exit;
        }
        $eta = $this->_getEta();
        echo json_encode($eta);
    }

    protected function _getEta()
    {
        $model = Mage::getModel('eta/eta');
        $minStockLevel = $this->_getMinStockLevel() > 0 ? $this->_getMinStockLevel() : 1;
        $result = $model->productGetEtaDate($this->_compileProductData($this->_rexProductId, $minStockLevel));
        $result['productId'] = $this->_productId;

        return $result;
    }

    protected function _getMinStockLevel()
    {
        return Mage::helper('eta')->getMinStockLevel($this->_productId);
    }

    protected function _compileProductData($productId, $minStockLevel)
    {
        return [
            'ProductID' => $productId,
            'QtyOrdered' => $minStockLevel,
        ];
    }

    protected function _getRexProductId()
    {
        $product = Mage::getModel('catalog/product')->load($this->_productId);

        return $product->getRexProductId();
    }
}
