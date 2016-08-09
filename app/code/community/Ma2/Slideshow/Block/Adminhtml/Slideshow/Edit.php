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
/* $Id: Edit.php 15 2013-11-05 07:30:45Z linhnt $ */

class Ma2_Slideshow_Block_Adminhtml_Slideshow_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
			
        $this->_objectId = 'id';
        $this->_blockGroup = 'slideshow';
        $this->_controller = 'adminhtml_slideshow';
        
        $this->_updateButton('save', 'label', Mage::helper('slideshow')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('slideshow')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('slideshow_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'slideshow_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'slideshow_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('slideshow_data') && Mage::registry('slideshow_data')->getId() ) {
            return Mage::helper('slideshow')->__("Edit Slideshow Item '%s'", $this->htmlEscape(Mage::registry('slideshow_data')->getTitle()));
        } else {
            return Mage::helper('slideshow')->__('Add Slideshow Item');
        }
    }
}