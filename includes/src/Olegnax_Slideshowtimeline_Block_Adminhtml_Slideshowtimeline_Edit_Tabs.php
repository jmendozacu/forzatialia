<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Slideshowtimeline_Block_Adminhtml_Slideshowtimeline_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('slideshowtimeline_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('slideshowtimeline')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('slideshowtimeline')->__('Item Information'),
          'title'     => Mage::helper('slideshowtimeline')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('slideshowtimeline/adminhtml_slideshowtimeline_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}