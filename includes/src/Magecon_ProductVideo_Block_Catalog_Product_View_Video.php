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
class Magecon_ProductVideo_Block_Catalog_Product_View_Video extends Mage_Catalog_Block_Product_View_Abstract {

    public function getVideosSorted($productId, $sortOrder) {
        return Mage::getModel('productvideo/video')->getCollection()->addAttributeToFilter('entity_id', $productId)
                        ->addAttributeToFilter('removed', 0)->addAttributeToFilter('excluded', 0)->setOrder('sort_order', $sortOrder);
    }

}

?>