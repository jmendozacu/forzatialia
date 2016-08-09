<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Celebrityslider_Block_Slider
    extends Mage_Catalog_Block_Product_Abstract
    implements Mage_Widget_Block_Interface
{

	/**
	 * Get latest products collection
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection|Object
	 */
	public function getLatestCollection()
	{
				return false;
		$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

		$collection = Mage::getResourceModel('catalog/product_collection');
		$collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

		$collection = $this->_addProductAttributesAndPrices($collection)
			->addStoreFilter()
			->addAttributeToFilter('news_from_date', array('or'=> array(
				0 => array('date' => true, 'to' => $todayDate),
				1 => array('is' => new Zend_Db_Expr('null')))
			), 'left')
			->addAttributeToFilter('news_to_date', array('or'=> array(
				0 => array('date' => true, 'from' => $todayDate),
				1 => array('is' => new Zend_Db_Expr('null')))
			), 'left')
			->addAttributeToFilter(
				array(
					array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
					array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
				)
			)
			->addAttributeToSort('news_from_date', 'desc');

	echo $collection->getSelect()->__toString();
		return $collection;
	}

	/**
	 * Get on sale products collection
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection|Object
	 */
	public function getSaleCollection()
	{
		return false;
		$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

		$collection = Mage::getResourceModel('catalog/product_collection');
		$collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

		$collection = $this->_addProductAttributesAndPrices($collection)
			->addStoreFilter()
			->addAttributeToFilter('special_from_date', array('or'=> array(
				0 => array('date' => true, 'to' => $todayDate),
				1 => array('is' => new Zend_Db_Expr('null')))
			), 'left')
			->addAttributeToFilter('special_to_date', array('or'=> array(
				0 => array('date' => true, 'from' => $todayDate),
				1 => array('is' => new Zend_Db_Expr('null')))
			), 'left')
			->addAttributeToFilter(
				array(
					array('attribute' => 'special_from_date', 'is'=>new Zend_Db_Expr('not null')),
					array('attribute' => 'special_to_date', 'is'=>new Zend_Db_Expr('not null'))
				)
			)
			->addAttributeToSort('special_from_date', 'desc');

		return $collection;
	}

}
