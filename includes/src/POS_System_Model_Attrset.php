<?php

class POS_System_Model_Attrset extends Mage_Core_Model_Abstract
{


	public function toOptionArray()
    {
    	$collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId());
        $return = array(
        	0 => array(
	        	'value' => '',
	        	'label' => '',
        	)
        );
        foreach ($collection->getItems() as $v) {
        	$return[] = array(
        		'value' => $v->getAttributeSetId(),
        		'label' => $v->getAttributeSetName(),
        	);
        }

    	return $return;
    }


}
