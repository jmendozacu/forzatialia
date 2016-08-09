<?php


class POS_SimplePrice_Catalog_Model_Product
    extends Mage_Catalog_Model_Product
{
    /*
     * check if all child products have the same final prices
     *
     * @return bool
     */
    public function getIsChildPricesDifferent()
    {
        if (is_callable(array($this->getPriceModel(), 'getIsChildPricesDifferent'))) {
            return $this->getPriceModel()->getIsChildPricesDifferent($this);
        } else {
            return false;
        }
    }

    /**
     * get regular price of configurable product children that has minimal final price (returns the same as getPrice()).
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return float
     */
    public function getMinimalPrice()
    {
        if (is_callable(array($this->getPriceModel(), 'getMinimalPrice'))) {
            return $this->getPriceModel()->getMinimalPrice($this);
        } else {
            return parent::getMinimalPrice();
        }
    }

    /**
     * Get product final price.
     *
     * @param float $qty
     *
     * @return float
     */
    public function getFinalPrice($qty = null)
    {
        $price = $this->getPriceModel()->getFinalPrice($qty, $this);
        if ($price !== null) {
            return $price;
        }

        return parent::getFinalPrice($qty);
    }

    /**
     * get special price.
     *
     * @see app/code/core/Mage/Catalog/Model/Mage_Catalog_Model_Product#getSpecialPrice()
     */
    public function getSpecialPrice()
    {
        if ($childProduct = $this->getSelectedProduct()) {
            return $childProduct->getSpecialPrice();
        } elseif ($childProduct = $this->getFirstSalable()) {
            return $childProduct->getSpecialPrice();
        }

        if (Mage::getSingleton('customer/session')->isLoggedIn() && ('downloadable' == $this->getTypeId() || 'simple' == $this->getTypeId())) {
            $mag_id = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            if ($mag_id) {
                $t = Mage::getModel('retailexpress/product')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $this->getId())
                    ->addFieldToFilter('customer_id', $mag_id)
                    ->getFirstItem();
                if ($t->getData('special_price') && ($t->getData('special_price') > 0.01)) {
                    return $t->getData('special_price');
                }
            }
        }

        return parent::getSpecialPrice();
    } // function getSpecialPrice()


    public function getData($key = '', $index = null)
    {
        if ($key != 'price' && $key != 'special_price') {
            return parent::getData($key, $index);
        }

        $return = false;
        if (is_callable(array($this->getPriceModel(), 'getChoosedData'))) {
            $return = $this->getPriceModel()->getChoosedData($this, $key);
        }
        if (Mage::getSingleton('customer/session')->isLoggedIn() && ('downloadable' == $this->getTypeId() || 'simple' == $this->getTypeId())) {
            $mag_id = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
            $pricegroup_id = (int) Mage::getModel('retailexpress/conf')->load('group_'.$mag_id)->getValue();
            if ($mag_id && $pricegroup_id) {
                $t = Mage::getModel('retailexpress/product')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $this->getId())
                    ->addFieldToFilter('customer_id', $mag_id)
                    ->getFirstItem();
                if ($t->getData($key) && ($t->getData($key) > 0.01)) {
                    return $t->getData($key);
                }
            }
        }

        if (false === $return) {
            return parent::getData($key, $index);
        }

        return $return;
    } // function getData()


    public function getData2($key = '', $index = null)
    {
        $rewrite_keys = $this->getRewriteParams();
        $rewrite_keys[] = 'price';
        if (!in_array($key, $rewrite_keys)) {
            return parent::getData($key, $index);
        }

        if ($childProduct = $this->getSelectedProduct()) {
            return $childProduct->getData($key, $index);
        }

        if ($childProduct = $this->getFirstSalable()) {
            return $childProduct->getData($key, $index);
        }

        return parent::getData($key, $index);
    } // function getData()


    public function getSelectedProduct()
    {
        /* */
        if (!is_callable(array($this, 'getTypeInstance')) || !is_callable(array($this->getTypeInstance(), 'getUsedProducts'))) {
            return false;
        }

        $selectedAttributes = array();
        if ($this->getCustomOption('attributes')) {
            $selectedAttributes = unserialize($this->getCustomOption('attributes')->getValue());
        }

        if (count($selectedAttributes)) {
            foreach ($this->getTypeInstance()->getUsedProducts() as $childProduct) {
                $checkRes = true;
                foreach ($selectedAttributes as $attributeId => $attributeValue) {
                    $code = $this->getTypeInstance()->getAttributeById($attributeId)->getAttributeCode();
                    if ($childProduct->getData($code) != $attributeValue) {
                        $checkRes = false;
                        break;
                    }
                }

                if ($checkRes) {
                    return $childProduct;
                }
            }
        }

        return false;
    } // function getSelectedProduct()


    public function getFirstSalable()
    {
        if (!is_callable(array($this, 'getTypeInstance')) || !is_callable(array($this->getTypeInstance(), 'getUsedProducts'))) {
            return false;
        }

        foreach ($this->getTypeInstance()->getUsedProducts() as $childProduct) {
            if (false && !$childProduct->isSalable()) {
                continue;
            }

            return $childProduct;
        }

        return false;
    } // function getSelectedProduct()


    public function getRewriteParams()
    {
        return array('special_price', 'special_from_date', 'special_to_date'/*, 'tier_price'*/);
    }

    public function save()
    {
        if (is_callable(array($this, 'getTypeInstance')) && is_callable(array($this->getTypeInstance(), 'getUsedProducts'))) {
            $data = $this->getRewriteParams();
            foreach ($data as $d) {
                $this->setData($d, $this->getData2($d));
            }
        }

        parent::save();
    }
}
