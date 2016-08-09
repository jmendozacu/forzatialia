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
class Magecon_ProductVideo_Model_Observer {

    static protected $_singletonFlag = false;

    public function saveProductTabData(Varien_Event_Observer $observer) {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;

            $product = $observer->getEvent()->getProduct();

            try {
                $data = $this->_getRequest()->getPost();

                foreach ($data["key"] as $i => $_key) {
                    $_key = trim($_key);
                    if ($_key != "") {

                        $_sort = (int) trim($data["sort"][$i]);
                        if ($_sort == 0) {
                            $_sort = max($data["sort"]) + 1;
                            $data["sort"][$i] = $_sort;
                        }

                        $dataArray = array(
                            'entity_id' => $product->getId(),
                            'youtube_key' => $_key,
                            'sort_order' => $_sort,
                            'excluded' => in_array($_key, $data["excluded"]) == true ? 1 : 0,
                            'removed' => in_array($_key, $data["removed"]) == true ? 1 : 0
                        );

                        if (isset($data["id"][$i])) {
                            $_id = $data["id"][$i];
                            $model = Mage::getModel('productvideo/video')->load($_id)->addData($dataArray);
                            $model->setId($_id)->save();
                        } else {
                            $model = Mage::getModel('productvideo/video');
                            $model->setData($dataArray)->save();
                        }
                    }
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }

    public function getProduct() {
        return Mage::registry('product');
    }

    protected function _getRequest() {
        return Mage::app()->getRequest();
    }

}