<?php

/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_ProductVideo
 * @version    1.0.0
 * @copyright  Copyright (c) 2012 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
class Magecon_ProductVideo_Block_Adminhtml_Catalog_Product_Video extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct() {

        $this->setTemplate('productvideo/catalog/product/video.phtml');
        parent::__construct();
    }

    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->addJs('magecon/productvideo/productvideo.js');
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Add New Video'),
                            'class' => 'add',
                            'on_click' => 'addRow()'
                        ))
        );

        return parent::_prepareLayout();
    }

    public function getAddButtonHtml() {
        return $this->getChildHtml('add_button');
    }

    public function getVideos($productId) {
        return Mage::getModel('productvideo/video')->getCollection()->addAttributeToFilter('entity_id', $productId)
                        ->addAttributeToFilter('removed', 0);
    }

    public function getVideosSorted($productId, $sortOrder) {
        return Mage::getModel('productvideo/video')->getCollection()->addAttributeToFilter('entity_id', $productId)
                        ->addAttributeToFilter('removed', 0)->setOrder('sort_order', $sortOrder);
    }

    protected function getProduct() {
        return Mage::registry('current_product');
    }

    public function getTabLabel() {
        return Mage::helper('productvideo')->__('Videos');
    }

    public function getTabTitle() {
        return Mage::helper('productvideo')->__('Videos');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

}