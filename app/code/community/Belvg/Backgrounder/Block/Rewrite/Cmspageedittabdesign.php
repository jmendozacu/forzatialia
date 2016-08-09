<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Belvg_Backgrounder_Block_Rewrite_Cmspageedittabdesign extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design
{

    protected function _prepareForm()
    {
      
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $model = Mage::registry('cms_page');
        $layoutFieldset = $form->addFieldset('layout_fieldset', array(
            'legend' => Mage::helper('cms')->__('Page Layout'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));

        $layoutFieldset->addField('root_template', 'select', array(
            'name'     => 'root_template',
            'label'    => Mage::helper('cms')->__('Layout'),
            'required' => true,
            'values'   => Mage::getSingleton('page/source_layout')->toOptionArray(),
            'disabled' => $isElementDisabled
        ));
        if (!$model->getId()) {
            $model->setRootTemplate(Mage::getSingleton('page/source_layout')->getDefaultValue());
        }

        $layoutFieldset->addField('layout_update_xml', 'textarea', array(
            'name'      => 'layout_update_xml',
            'label'     => Mage::helper('cms')->__('Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled
        ));

        $designFieldset = $form->addFieldset('design_fieldset', array(
            'legend' => Mage::helper('cms')->__('Custom Design'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $designFieldset->addField('custom_theme_from', 'date', array(
            'name'      => 'custom_theme_from',
            'label'     => Mage::helper('cms')->__('Custom Design From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_theme_to', 'date', array(
            'name'      => 'custom_theme_to',
            'label'     => Mage::helper('cms')->__('Custom Design To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_theme', 'select', array(
            'name'      => 'custom_theme',
            'label'     => Mage::helper('cms')->__('Custom Theme'),
            'values'    => Mage::getModel('core/design_source_design')->getAllOptions(),
            'disabled'  => $isElementDisabled
        ));


        $designFieldset->addField('custom_root_template', 'select', array(
            'name'      => 'custom_root_template',
            'label'     => Mage::helper('cms')->__('Custom Layout'),
            'values'    => Mage::getSingleton('page/source_layout')->toOptionArray(true),
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_layout_update_xml', 'textarea', array(
            'name'      => 'custom_layout_update_xml',
            'label'     => Mage::helper('cms')->__('Custom Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled
        ));

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_design_prepare_form', array('form' => $form));
		
				
		$bgFieldset = $form->addFieldset('bg_fieldset', array(
            'legend' => Mage::helper('cms')->__('Backgrounds'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));
		
		
		$cmsBgCollection = $this->helper('backgrounder')->getBgForCms($model->getId());
		foreach($cmsBgCollection  as $cmsBg){
			$bgFieldset->addField('bg'.$cmsBg->getId(), 'file', array(
				'name'      => 'bg[]',
				'label'     => Mage::helper('cms')->__('Background'),                                    
			))->setAfterElementHtml($this->helper('backgrounder')->getAdminBg($cmsBg->getImage()).' '.$this->helper('backgrounder')->getMultiUploadHtml('page_bg'));
		}
		if (!sizeof($cmsBgCollection)){
			$bgFieldset->addField('bg', 'file', array(
				'name'      => 'bg[]',
				'label'     => Mage::helper('cms')->__('Background'),                                    
			))->setAfterElementHtml($this->helper('backgrounder')->getMultiUploadHtml('page_bg'));				
		}


		
		$bgFieldset->addField('bghidden','hidden', array(
            'label'     => '',                 
        ))->setAfterElementHtml($this->helper('backgrounder')->getMultiButtonHtml());
		

        $form->setValues($model->getData());

        $this->setForm($form);

        return $this;//parent::_prepareForm();
    }

}
