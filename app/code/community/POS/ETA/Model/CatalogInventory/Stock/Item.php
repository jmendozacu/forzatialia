<?php

/**
 */
class POS_ETA_Model_CatalogInventory_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item
{
    const ALLOW_CHECK_AVAILABILITY_NO = 0;
    const ALLOW_CHECK_AVAILABILITY_YES = 1;
    const INVENTORY_STATUS_IN_STOCK = 1;
    const INVENTORY_STATUS_OUT_OF_STOCK = 0;
    const INVENTORY_STATUS_CHECK_AVAILABILITY = 2;

    const XML_PATH_ALLOW_CHECK_AVAILABILITY_STATUS = 'cataloginventory/item_options/allow_check_availability_status';

    protected $_globalAllowCheckAvailabilityStatus;
    protected $_enableEtaCalculation;

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_globalAllowCheckAvailabilityStatus = Mage::helper('eta')->getGlobalAllowCheckAvailabilityStatus();
        $this->_enableEtaCalculation = Mage::helper('eta')->getEtaCalculationEnabled();

        return parent::_construct();
    }

    /**
     *
     */
    public function verifyStock($qty = null)
    {
        if ($qty === null) {
            $qty = $this->getQty();
        }
        if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_NO && $qty <= $this->getMinQty() && !$this->_getAllowCheckAvailability()) {
            return false;
        }

        return true;
    }

    /**
     */
    protected function _getAllowCheckAvailability()
    {
        if (
            $this->getAllowCheckAvailability()
            &&
            (($this->getQty() + $this->getQtyOnOrder()) > $this->getMinQty())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check quantity.
     *
     * @param decimal $qty
     * @exception Mage_Core_Exception
     *
     * @return bool
     */
    public function checkQty($qty)
    {
        if (!$this->getManageStock() || Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if ($this->getQty() - $this->getMinQty() - $qty < 0) {
            switch ($this->getBackorders()) {
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY:
                    return true;
                    break;
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY:
                    return true;
                    break;
                default:
                    break;
            }

            if (($this->getQty() + $this->getQtyOnOrder() - $this->getMinQty() - $qty) >= 0) {
                return $this->getAllowCheckAvailability();
            }
        } else {
            return true;
        }

        return false;
    }

    /**
     * Checking quote item quantity.
     *
     * @param mixed $qty        quantity of this item (item qty x parent item qty)
     * @param mixed $summaryQty quantity of this product in whole shopping cart which should be checked for stock availability
     * @param mixed $origQty    original qty of item (not multiplied on parent item qty)
     *
     * @return Varien_Object
     */
    public function checkQuoteItemQty($qty, $summaryQty, $origQty = 0)
    {
        $result = new Varien_Object();
        $result->setHasError(false);

        if (!is_numeric($qty)) {
            $qty = Mage::app()->getLocale()->getNumber($qty);
        }

        /*
         * Check quantity type
         */
        $result->setItemIsQtyDecimal($this->getIsQtyDecimal());

        if (!$this->getIsQtyDecimal()) {
            $result->setHasQtyOptionUpdate(true);
            $qty = intval($qty);

            /*
              * Adding stock data to quote item
              */
            $result->setItemQty($qty);

            if (!is_numeric($qty)) {
                $qty = Mage::app()->getLocale()->getNumber($qty);
            }
            $origQty = intval($origQty);
            $result->setOrigQty($origQty);
        }

        if ($this->getMinSaleQty() && ($qty) < $this->getMinSaleQty()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('The minimum quantity allowed for purchase is %s.', $this->getMinSaleQty() * 1))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in requested quantity.'))
                ->setQuoteMessageIndex('qty');

            return $result;
        }

        if ($this->getMaxSaleQty() && ($qty) > $this->getMaxSaleQty()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('The maximum quantity allowed for purchase is %s.', $this->getMaxSaleQty() * 1))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products cannot be ordered in requested quantity.'))
                ->setQuoteMessageIndex('qty');

            return $result;
        }

        if (!$this->getManageStock()) {
            return $result;
        }

        if (!$this->getIsInStock()) {
            $result->setHasError(true)
                ->setMessage(Mage::helper('cataloginventory')->__('This product is currently out of stock.'))
                ->setQuoteMessage(Mage::helper('cataloginventory')->__('Some of the products are currently out of stock'))
                ->setQuoteMessageIndex('stock');
            $result->setItemUseOldQty(true);

            return $result;
        }

        $result->addData($this->checkQtyIncrements($qty)->getData());

        if ($result->getHasError()) {
            return $result;
        }

        if (!$this->checkQty($summaryQty)) {
            $message = Mage::helper('eta')->getUnavailableMessage();
            $result->setHasError(true)
                ->setMessage($message)
                ->setQuoteMessage($message)
                ->setQuoteMessageIndex('qty');

            return $result;
        } else {
            if (($this->getQty() - $summaryQty) < 0) {
                if ($this->getProductName()) {
                    if ($this->getIsChildItem()) {
                        $backorderQty = ($this->getQty() > 0) ? ($summaryQty - $this->getQty()) * 1 : $qty * 1;
                        if ($backorderQty > $qty) {
                            $backorderQty = $qty;
                        }

                        $result->setItemBackorders($backorderQty);
                    } else {
                        $orderedItems = $this->getOrderedItems();
                        $itemsLeft = ($this->getQty() > $orderedItems) ? ($this->getQty() - $orderedItems) * 1 : 0;
                        $backorderQty = ($itemsLeft > 0) ? ($qty - $itemsLeft) * 1 : $qty * 1;

                        if ($backorderQty > 0) {
                            $result->setItemBackorders($backorderQty);
                        }
                        $this->setOrderedItems($orderedItems + $qty);
                    }

                    if ($this->getBackorders() == Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY) {
                        if (!$this->getIsChildItem()) {
                            $result->setMessage(Mage::helper('cataloginventory')->__('This product is not available in the requested quantity. %s of the items will be backordered.', ($backorderQty * 1)));
                        } else {
                            $result->setMessage(Mage::helper('cataloginventory')->__('"%s" is not available in the requested quantity. %s of the items will be backordered.', $this->getProductName(), ($backorderQty * 1)));
                        }
                    }
                }
            }
            // no return intentionally
        }

        return $result;
    }

    /**
     * Before save prepare process (overriding this core function for the availability status).
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _beforeSave()
    {
        // see if quantity is defined for this item type
        $typeId = $this->getTypeId();
        if ($productTypeId = $this->getProductTypeId()) {
            $typeId = $productTypeId;
        }

        $isQty = Mage::helper('catalogInventory')->isQty($typeId);

        if ($isQty) {
            // set allow check availability stock value
            if ($this->_getAutoStockStatus() == self::INVENTORY_STATUS_CHECK_AVAILABILITY) {
                $this->setAllowCheckAvailability(1);
            }

            $this->setIsInStock($this->_getAutoStockStatus())->setStockStatusChangedAutomaticallyFlag(true);

            // if qty is below notify qty, update the low stock date to today date otherwise set null
            $this->setLowStockDate(null);
            if ($this->verifyNotification()) {
                $this->setLowStockDate(Mage::app()->getLocale()->date(null, null, null, false)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            }

            $this->setStockStatusChangedAutomatically(0);
            if ($this->hasStockStatusChangedAutomaticallyFlag()) {
                $this->setStockStatusChangedAutomatically((int) $this->getStockStatusChangedAutomaticallyFlag());
            }
        } else {
            $this->setQty(0);
        }

        return $this;
    }

    /**
     * Retrieve allow check availbility status.
     *
     * @return int
     */
    public function getAllowCheckAvailabilityStatus()
    {
        if ($this->getUseConfigAllowCheckAvailabilityStatus()) {
            return (int) Mage::getStoreConfig(self::XML_PATH_ALLOW_CHECK_AVAILABILITY_STATUS);
        }

        return $this->getData('allow_check_availability_status');
    }

    /**
     * Retrieve product allow check availbility combined value.
     *
     * @return bool
     */
    public function getAllowCheckAvailability()
    {
        if (
            $this->_enableEtaCalculation
            &&
            $this->getAllowCheckAvailabilityStatus()
        ) {
            return ((int) $this->getData('allow_check_availability') == 1) ? true : false;
        }

        return false;
    }

    /**
     * @return enum
     */
    public function getAutoStockStatus()
    {
        return $this->_getAutoStockStatus();
    }

    /**
     * Return stock status, that will be automatically set on product save.
     *
     * @return enum
     */
    protected function _getAutoStockStatus()
    {
        // if stock is not managed or backorders enabled or qty is greater than min qty
        if (!$this->getManageStock() || $this->getBackorders() || ($this->getQty() > $this->getMinQty())) {
            return self::INVENTORY_STATUS_IN_STOCK;
        }
        if (
            $this->_enableEtaCalculation
            &&
            $this->getAllowCheckAvailabilityStatus()
            &&
            (($this->getQty() + $this->getQtyOnOrder()) > $this->getMinQty())
        ) {
            return self::INVENTORY_STATUS_CHECK_AVAILABILITY;
        }

        return self::INVENTORY_STATUS_OUT_OF_STOCK;
    }
}
