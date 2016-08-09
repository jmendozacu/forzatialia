<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Celebritysettings_Adminhtml_RestoreController extends Mage_Adminhtml_Controller_Action
{

    protected $_stores;
    protected $_clear;

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('olegnax/celebrity/restore');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('olegnax/celebrity/restore')
            ->_addBreadcrumb(Mage::helper('celebritysettings')->__('Restore Defaults'), Mage::helper('celebritysettings')->__('Restore Defaults'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_title($this->__('Olegnax'))
            ->_title($this->__('Celebrity'))
            ->_title($this->__('Restore Defaults'));

        $this->_addContent($this->getLayout()->createBlock('celebritysettings/adminhtml_restore_edit'));
        $block = $this->getLayout()->createBlock('core/text', 'restore-desc')
                ->setText('<b>Theme default settings :</b>
                        <br/><br/>
                        <b>Appearance</b>
                        <ul>
                            <li>ATTENTION: All colors will be restored to default scheme. Do not restore if you do not want to loose your changes</li>
                        </ul>
                        <b>Navigation</b>
                        <ul>
                            <li>Show Top Category description in dropdown: Yes</li>
                            <li>Show "Learn more" button under description:: Yes</li>
                        </ul>');
        $this->_addLeft($block);

        $this->renderLayout();
    }

    public function restoreAction()
    {
        $this->_stores = $this->getRequest()->getParam('stores', array(0));
        $this->_clear = $this->getRequest()->getParam('clear_scope', false);
	    $setup_cms = $this->getRequest()->getParam('setup_cms', 0);

        if ($this->_clear) {
            if ( !in_array(0, $this->_stores) )
                $stores[] = 0;
        }

	    try {
		    $defaults = new Varien_Simplexml_Config();
            $defaults->loadFile(Mage::getBaseDir().'/app/code/local/Olegnax/Celebritysettings/etc/config.xml');
            $this->_restoreSettings($defaults->getNode('default/celebritysettings')->children(), 'celebritysettings');

		    if ($setup_cms) {
                Mage::getModel('celebritysettings/settings')->setupCms();
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('celebritysettings')->__('Celebrity Theme Settings has been restored. Please clear cache (System > Cache management) if you do not see changes in storefront'));
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('celebritysettings')->__('An error occurred while restoring theme settings.'));
        }

        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    private function _restoreSettings($items, $path)
    {
        $websites = Mage::app()->getWebsites();
        $stores = Mage::app()->getStores();
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->_restoreSettings($item->children(), $path.'/'.$item->getName());
            } else {
                if ($this->_clear) {
                    Mage::getConfig()->deleteConfig($path.'/'.$item->getName());
                    foreach ($websites as $website) {
                        Mage::getConfig()->deleteConfig($path.'/'.$item->getName(), 'websites', $website->getId());
                    }
                    foreach ($stores as $store) {
                        Mage::getConfig()->deleteConfig($path.'/'.$item->getName(), 'stores', $store->getId());
                    }
                }
                foreach ($this->_stores as $store) {
                    $scope = ($store ? 'stores' : 'default');
                    Mage::getConfig()->saveConfig($path.'/'.$item->getName(), (string)$item, $scope, $store);
                }
            }
        }
    }

}