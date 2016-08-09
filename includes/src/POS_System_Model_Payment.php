<?php

class POS_System_Model_Payment extends Mage_Core_Model_Abstract
{


	public function _construct()
    {
        parent::_construct();
        $this->_init('retailexpress/payment');
    }

	public function toOptionArray()
    {
        $return = array(
        	0 => array(
	        	'value' => '',
	        	'label' => '',
        	)
        );
        foreach ($this->getCollection()->getItems() as $i) {
            $return[] = array(
                'value' => $i->getId(),
                'label' => $i->getName(),
            );
        }

        usort($return, array($this, 'sortValues'));
    	return $return;
    }

    public function sortValues($a, $b)
    {
        if (strtolower($a['label']) > strtolower($b['label'])) {
            return 1;
        } else if (strtolower($a['label']) < strtolower($b['label'])) {
            return -1;
        }

        return 0;
    }

}
 
