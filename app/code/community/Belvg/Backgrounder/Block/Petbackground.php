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
 
class Belvg_Backgrounder_Block_Petbackground extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareLayout()
    {        
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('catalog')->__('Backgrounds')));

		$proid = $this->getRequest()->getParam('id');

		$proBgCollection = $this->helper('backgrounder')->getBgForProduct($proid);
		foreach($proBgCollection  as $proBg){
			$fieldset->addField('bg'.$proBg->getId(), 'file', array(
				'name'      => 'bg[]',
				'label'     => Mage::helper('cms')->__('Background'),                                    
			))->setAfterElementHtml($this->helper('backgrounder')->getAdminBg($proBg->getImage()).' '.$this->helper('backgrounder')->getMultiUploadHtml('page_bg'));
		}
		if (!sizeof($proBgCollection)){
			$fieldset->addField('bg', 'file', array(
				'name'      => 'bg[]',
				'label'     => Mage::helper('cms')->__('Background'),                                    
			))->setAfterElementHtml($this->helper('backgrounder')->getMultiUploadHtml('page_bg'));				
		}


		
		$fieldset->addField('bghidden','hidden', array(
            'label'     => '',                 
        ))->setAfterElementHtml($this->helper('backgrounder')->getMultiButtonHtml());
		
     

        $this->setForm($form);
    }
    
}
