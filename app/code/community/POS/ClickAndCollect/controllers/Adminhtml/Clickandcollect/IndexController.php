<?php

class POS_ClickAndCollect_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /*
     *
     * mass change of Product Pickup Rule Attribute
     *
     * @return void
    **/
    public function massChangePickupRuleAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $pickupRule = (int) $this->getRequest()->getParam('pickuprule');

        try {
            if (!is_array($productIds)) {
                throw new Exception($this->__('Please select product(s).'));
            }

            $this->_validateMassStatus($productIds);
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, ['store_pickup_rule' => $pickupRule], $storeId);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('adminhtml/catalog_product/index');
    }

    /*
     *
     * mass change of Product Delivery Rule Attribute
     *
     * @return void
    **/
    public function massChangeDeliveryRuleAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $deliveryRule = (int) $this->getRequest()->getParam('deliveryrule');

        try {
            if (!is_array($productIds)) {
                throw new Exception($this->__('Please select product(s).'));
            }

            $this->_validateMassStatus($productIds);
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, ['store_delivery_rule' => $deliveryRule], $storeId);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('adminhtml/catalog_product/index');
    }

    /**
     * Validate batch of products before theirs status will be set.
     *
     * @throws Mage_Core_Exception
     *
     * @param array $productIds
     */
    public function _validateMassStatus(array $productIds)
    {
        if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
            throw new Mage_Core_Exception($this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.'));
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
