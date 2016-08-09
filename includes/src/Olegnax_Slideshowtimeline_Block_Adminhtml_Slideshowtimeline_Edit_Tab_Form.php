<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Slideshowtimeline_Block_Adminhtml_Slideshowtimeline_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {

	  $model = Mage::registry('slideshowtimeline_slideshowtimeline');

      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('slideshowtimeline_form', array('legend'=>Mage::helper('slideshowtimeline')->__('Item information')));

	  if (!Mage::app()->isSingleStoreMode()) {
        $fieldset->addField('store_id', 'multiselect', array(
              'name'      => 'stores[]',
              'label'     => Mage::helper('slideshowtimeline')->__('Store View'),
              'title'     => Mage::helper('slideshowtimeline')->__('Store View'),
              'required'  => true,
              'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
          ));
      }
      else {
          $fieldset->addField('store_id', 'hidden', array(
              'name'      => 'stores[]',
              'value'     => Mage::app()->getStore(true)->getId()
          ));
          //$model->setStoreId(Mage::app()->getStore(true)->getId());
      }

	  $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('slideshowtimeline')->__('Title'),
          'required'  => false,
          'name'      => 'title',
      ));
	  
	  $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('slideshowtimeline')->__('Link'),
          'required'  => false,
          'name'      => 'link',
      ));

	  $fieldset->addField('details', 'select', array(
	        'label'     => Mage::helper('slideshowtimeline')->__('Show Details Button'),
	        'name'      => 'details',
	        'values'    => array(
	            array(
	                'value'     => 1,
	                'label'     => Mage::helper('slideshowtimeline')->__('Yes'),
	            ),
	            array(
	                'value'     => 0,
	                'label'     => Mage::helper('slideshowtimeline')->__('No'),
	            ),
	        ),
	    ));

	  $data = array();
	  $out = '';
	  if ( Mage::getSingleton('adminhtml/session')->getSlideshowtimelineData() )
		{
			$data = Mage::getSingleton('adminhtml/session')->getSlideshowtimelineData();
		} elseif ( Mage::registry('slideshowtimeline_data') ) {
			$data = Mage::registry('slideshowtimeline_data')->getData();
		}

	  if ( !empty($data['image']) ) {
		  $url = Mage::getBaseUrl('media') . $data['image'];
		  $out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
		  $out .= "<img src=" . $url . " width='150px' />";
		  $out .= '</a></center>';
	  }

      $fieldset->addField('image', 'file', array(
          'label'     => Mage::helper('slideshowtimeline')->__('Image'),
          'required'  => false,
          'name'      => 'image',
	      'note' => $out,
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('slideshowtimeline')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('slideshowtimeline')->__('Enabled'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('slideshowtimeline')->__('Disabled'),
              ),
          ),
      ));

      $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('slideshowtimeline')->__('Sort Order'),
            'required'  => false,
            'name'      => 'sort_order',
        ));

      if ( Mage::getSingleton('adminhtml/session')->getSlideshowtimelineData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSlideshowtimelineData());
          Mage::getSingleton('adminhtml/session')->setSlideshowtimelineData(null);
      } elseif ( Mage::registry('slideshowtimeline_data') ) {
          $form->setValues(Mage::registry('slideshowtimeline_data')->getData());
      }
      return parent::_prepareForm();
  }
}