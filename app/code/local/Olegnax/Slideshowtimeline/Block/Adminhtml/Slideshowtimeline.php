<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Slideshowtimeline_Block_Adminhtml_Slideshowtimeline extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_slideshowtimeline';
		$this->_blockGroup = 'slideshowtimeline';
		$this->_headerText = Mage::helper('slideshowtimeline')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('slideshowtimeline')->__('Add Item');
		parent::__construct();
	}
}