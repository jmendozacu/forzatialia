<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */
class Olegnax_Celebritysettings_Block_Adminhtml_Activate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $isElementDisabled = false;
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('celebritysettings')->__('Activate Parameters')));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled
            ));
        }
        else {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => array(array('value' => 0, 'label' => 'Default Store View')),
                'disabled'  => $isElementDisabled
            ));
        }

        $fieldset->addField('update_currency', 'checkbox', array(
            'label' => Mage::helper('celebritysettings')->__('Update Currency'),
            'required' => false,
            'name' => 'update_currency',
            'value' => 1,
            'note' => Mage::helper('celebritysettings')->__('Select if you wish to update allowed currencies to USD, EUR, POUND'),
        ))->setIsChecked(1);

        $fieldset->addField('setup_cms', 'checkbox', array(
            'label' => Mage::helper('celebritysettings')->__('Create Cms Pages & Blocks'),
            'required' => false,
            'name' => 'setup_cms',
            'value' => 1,
        ))->setIsChecked(1);

        $form->setAction($this->getUrl('*/*/activate'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
