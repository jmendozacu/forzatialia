<?php

class POS_System_Model_Product_Attribute extends Mage_Core_Model_Abstract
{


	public function toOptionArray()
    {
        $attribute_set = Mage::getModel('retailexpress/conf')->load('attribute_set')->getValue();
        $attribute_set_attributes = array();
        $model  = Mage::getModel('eav/entity_attribute_set')->load($attribute_set);
        if (!$model->getId()) {
            $attribute_set = false;
        } else {
            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($model->getId())
                ->load();

            foreach ($groups as $group) {
                $groupAttributesCollection = Mage::getModel('eav/entity_attribute')
                    ->getResourceCollection()
                    ->setAttributeGroupFilter($group->getId())
                    ->load();

                foreach( $groupAttributesCollection as $attribute ) {
                    $attribute_set_attributes[$attribute->getId()] = true;
                }
            }
        }
    	$collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter();
        $return = array(
        	0 => array(
	        	'value' => '',
	        	'label' => '',
        	)
        );
        foreach ($collection->getItems() as $attribute) {
            if (!isset($attribute_set_attributes[$attribute->getId()])) {
                continue;
            }
            if (($attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
                && $attribute->getIsVisible()
                && $attribute->getIsConfigurable()
                && $attribute->usesSource()
                && $attribute->getIsUserDefined())) {
                $return[] = array(
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getFrontendLabel(),
                );
            }
        }

        usort($return, array($this, "sortValues"));
    	return $return;
    }

    public function sortValues($a, $b)
    {
        if ($a['label'] > $b['label']) {
            return 1;
        } else if ($a['label'] < $b['label']) {
            return -1;
        }

        return 0;
    }


}
