<?php

class POS_System_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_View
{

	protected $_already_sync = false;


	public function getProduct()
	{
		$Product = parent::getProduct();
		if (!$this->_already_sync) {
			$this->_already_sync = true;
			$productId = $Product->getId();
			try {
		        if (Mage::helper('retailexpress')->needSyncPrice($productId)) {
		        	Mage::helper('retailexpress')->syncProductStockById($productId);
		        }
			} catch (Exception $e) {
			}
		}

		return $Product;
	}


}