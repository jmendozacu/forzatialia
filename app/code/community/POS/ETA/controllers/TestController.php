<?php

/**
 */

/**
 */
class POS_ETA_TestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //    	echo 'test Action';
//    	$model = Mage::getModel('retailexpress/outlet');
//    	$collection = $model->getCollection();
//    	foreach ($collection as $item) {
//        	print_r($item->getData());
//    	}

//    	$model = Mage::getModel('eta/soap');
//    	$result = $model->OutletsGetByChannel();


//    	$result = Mage::getSingleton('retailexpress/retail')->getProductStockPriceById(140339, 1);
//    	$result = Mage::getSingleton('retailexpress/retail')->getOrdersBulkDetail('1900-10-10');
//    	$result = Mage::getSingleton('retailexpress/retail')->getProductsBulkDetail('1900-10-10');
        $result = Mage::getSingleton('retailexpress/retail')->productGetEtaDate();

        mage::log($result);
//    	print_r($result);
    }
}
