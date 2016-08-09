<?php
/**
 * MagenMarket.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Edit or modify this file with yourown risk.
 *
 * @category    Extensions
 * @package     Ma2_Slideshow free
 * @copyright   Copyright (c) 2013 MagenMarket. (http://www.magenmarket.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
**/
/* $Id: Tabs.php 15 2013-11-05 07:30:45Z linhnt $ */

class Ma2_Slideshow_Block_Adminhtml_Slideshow_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('slideshow_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('slideshow')->__('Item Information'));
  }

  protected function _prepareLayout()
  {

      $this->addTab('main_section', array(
          'label'     => Mage::helper('slideshow')->__('Item Information'),
          'title'     => Mage::helper('slideshow')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('slideshow/adminhtml_slideshow_edit_tab_form')->toHtml(),
		  'active'	  => true,
      ));

      return parent::_prepareLayout();
  }
}