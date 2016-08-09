<?php

/**
 *
 */
class POS_System_Model_CatalogRule_Rule_Applyrule extends Mage_Core_Model_Config_Data
{
    const XML_PATH_APPLY_TO_SPECIAL_PRICE = 'retailexpress/coupon_support/apply_catalog_price_rules_to_product_with_special_price';

    protected function _afterSave()
    {
        if (!$this->isValueChanged()) {
            return $this;
        }
        $requestValue = $this->getValue();
        Mage::register('apply_catalog_rules_to_product_with_special_price', $requestValue);

        // apply catalog rules
        try {
            if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                // 1.7.0.0 or greater
                // @todo: needs to be tested for magento 1.7
                Mage::getModel('catalogrule/rule')->applyAll();
                Mage::getModel('catalogrule/flag')->loadSelf()
                    ->setState(0)
                ->save();
            } else {
                $rules = Mage::getModel('catalogrule/rule')->getCollection();
                foreach ($rules as $rule) {
                    $rule->save();
                }
                Mage::getModel('catalogrule/rule')->applyAll();
                Mage::app()->removeCache('catalog_rules_dirty');
            }
            Mage::getSingleton('adminhtml/session')->addSuccess('Catalog Price Rules are Applied');
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return $this;
    }
}
