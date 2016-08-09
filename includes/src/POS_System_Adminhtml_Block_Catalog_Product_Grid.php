<?php

class POS_System_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{


	protected function _prepareMassaction()
	{
		parent::_prepareMassaction();
		$this->getMassactionBlock()->addItem('retail_sync', array(
			'label' => Mage::helper('retailexpress')->__('Synchronise POS Stock'),
        	'url'   => $this->getUrl('retailexpress/adminhtml_retailexpress/syncproduct', array('_current'=>true))
        ));
        return $this;
	}


}