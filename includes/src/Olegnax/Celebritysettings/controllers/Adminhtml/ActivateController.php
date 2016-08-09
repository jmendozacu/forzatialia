<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Celebritysettings_Adminhtml_ActivateController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('olegnax/celebrity/activate');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('olegnax/celebrity/activate')
            ->_addBreadcrumb(Mage::helper('celebritysettings')->__('Activate Celebrity Theme'),
                Mage::helper('celebritysettings')->__('Activate Celebrity Theme'));

        return $this;
    }

	public function indexAction()
	    {
	        $this->_initAction();
	        $this->_title($this->__('Olegnax'))
	            ->_title($this->__('Celebrity'))
	            ->_title($this->__('Activate Celebrity Theme'));

	        $this->_addContent($this->getLayout()->createBlock('celebritysettings/adminhtml_activate_edit'));
	        $block = $this->getLayout()->createBlock('core/text', 'activate-desc')
	                ->setText('<big><b>Activate will update following settings:</b></big>
	                        <br/><br/>
	                        <big>System > Config</big><br/><br/>
	                        <b>Web > Default pages</b>
	                        <ul>
	                            <li>CMS Home Page</li>
	                            <li>CMS No Route Page</li>
	                        </ul>
	                        <b>Design > Themes</b>
	                        <ul>
	                            <li>Default</li>
	                        </ul>
	                        <b>Design > Header</b>
	                        <ul>
	                            <li>Logo Img Src</li>
	                        </ul>
	                        <b>Design > Footer</b>
	                        <ul>
	                            <li>Copyright</li>
	                        </ul>
	                        <b>Currency Setup > Currency Options</b>
	                        <ul>
	                            <li>Allowed currencies</li>
	                        </ul>
	                        ');
	        $this->_addLeft($block);

	        $this->renderLayout();
	    }

	public function activateAction()
    {
        $stores = $this->getRequest()->getParam('stores', array(0));
        $update_currency = $this->getRequest()->getParam('update_currency', 0);
        $setup_cms = $this->getRequest()->getParam('setup_cms', 0);
        
        try {
	        foreach ($stores as $store) {
                $scope = ($store ? 'stores' : 'default');
		        //web > default pages
                Mage::getConfig()->saveConfig('web/default/cms_home_page', 'celebrity_home', $scope, $store);
                Mage::getConfig()->saveConfig('web/default/cms_no_route', 'celebrity_no_route', $scope, $store);
		        //design > themes
                Mage::getConfig()->saveConfig('design/theme/default', 'celebrity', $scope, $store);
                //design > header
                Mage::getConfig()->saveConfig('design/header/logo_src', 'images/logo.png', $scope, $store);
                //design > footer
                Mage::getConfig()->saveConfig('design/footer/copyright', 'Celebrity Theme &copy; 2012 <a href="//olegnax.com/products/magento/" >Premium Magento Themes</a> by Olegnax', $scope, $store);
                //Currency Setup > Currency Options
                if ($update_currency) {
                    Mage::getConfig()->saveConfig('currency/options/allow', 'GBP,EUR,USD', $scope, $store);
                }
            }

	        if ($setup_cms) {
                Mage::getModel('celebritysettings/settings')->setupCms();
	        }

		    Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('celebritysettings')->__('Celebrity Theme has been activated. Please clear cache (System > Cache management) if you do not see changes in storefront'));
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('celebritysettings')->__('An error occurred while activating theme. '.$e->getMessage()));
        }

        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

}