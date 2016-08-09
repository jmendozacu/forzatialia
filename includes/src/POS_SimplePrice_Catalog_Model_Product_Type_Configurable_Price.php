<?php
class POS_SimplePrice_Catalog_Model_Product_Type_Configurable_Price
    extends Mage_Catalog_Model_Product_Type_Configurable_Price
{

    protected $_cheapest = array();
    protected $_price = array();
    protected $_minimalPrice = array();
    protected $_finalPrice = array();
    protected $_childPriceDifferent = array();
    protected $_firstSalable = array();

    /**
     * get regular price of configurable product children that have a minimal final price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return double
     */
    public function getPrice($product)
    {

        $productId = $product->getId();

        if (isset($this->_price[$productId])) {

        	return $this->_price[$productId];

        }

        if (isset($this->_cheapest[$productId])) {

            $product = Mage::getModel('catalog/product')->load($this->_cheapest[$productId]);
            $this->_price[$productId] = $product->getPrice($product);
            if ($this->_price[$productId]) {

            	return $this->_price[$productId];

            } else {

                $this->_price[$productId] = parent::getPrice($product);
                return $this->_price[$productId];

            }

        }

        if (isset($this->_childPriceDifferent[$productId]) and $this->_childPriceDifferent[$productId] === false) {
            if ($childProduct = $this->getFirstSalable($product)) {
            	$this->_price[$productId] = $childProduct->getPrice($product);
                return $this->_price[$productId];
            }
        }

    	$configurable_products = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
	    $collection = $configurable_products->getUsedProductCollection()->addAttributeToSelect('*');

	    $minFinalPrice = false;

        foreach($collection as $childProduct) {
            if(!$childProduct->isSalable()) {
                continue;
            }

            $finalPrice = $childProduct->getFinalPrice();

            if (!$minFinalPrice or $minFinalPrice > $finalPrice) {
                $minFinalPrice = $finalPrice;
                $this->_cheapest[$productId] = $childProduct->getId();
            }

        }

        if ($minFinalPrice) {
            $product = Mage::getModel('catalog/product')->load($this->_cheapest[$productId]);
            $this->_price[$productId] = $product->getPrice($product);
            if ($this->_price[$productId]) {

            	return $this->_price[$productId];

            }
        }

        $this->_price[$productId] = parent::getPrice($product);
        return $this->_price[$productId];

    }

    /**
     * get regular price of configurable product children that have a minimal final price (returns the same as getPrice())
     *
     * @param Mage_Catalog_Model_Product $product
     * @return double
     */
    public function getMinimalPrice($product)
    {

        $productId = $product->getId();

        if (isset($this->_minimalPrice[$productId])) {

        	return $this->_minimalPrice[$productId];

        }

        if (isset($this->_cheapest[$productId])) {

            $product = Mage::getModel('catalog/product')->load($this->_cheapest[$productId]);
            $this->_minimalPrice[$productId] = $product->getPrice($product);
            if ($this->_minimalPrice[$productId]) {

            	return $this->_minimalPrice[$productId];

            } else {

                $this->_minimalPrice[$productId] = $this->getFinalPrice(null, $product);
                return $this->_minimalPrice[$productId];

            }

        }

        if (isset($this->_childPriceDifferent[$productId]) and $this->_childPriceDifferent[$productId] === false) {
            if ($childProduct = $this->getFirstSalable($product)) {
            	$this->_minimalPrice[$productId] = $childProduct->getPrice($product);
                return $this->_minimalPrice[$productId];
            }
        }

    	$configurable_products = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
	    $collection = $configurable_products->getUsedProductCollection()->addAttributeToSelect('*');

	    $minFinalPrice = false;

        foreach($collection as $childProduct) {

            $childProductId = $childProduct->getId();

            if(!$childProduct->getIsSalable()) {
                continue;
            }

        	$finalPrice = $childProduct->getFinalPrice();

            if (!$minFinalPrice or $minFinalPrice > $finalPrice) {
        		$this->_cheapest[$productId] = $childProductId;
                $minFinalPrice = $finalPrice;
            }

        }

        if ($minFinalPrice) {

            $product = Mage::getModel('catalog/product')->load($this->_cheapest[$productId]);
            $this->_minimalPrice[$productId] = $product->getPrice($product);

            if ($this->_minimalPrice[$productId]) {
            	return $this->_minimalPrice[$productId];
            }

        }

        $this->_minimalPrice[$productId] = $this->getFinalPrice(null, $product);
        return $this->_minimalPrice[$productId];

    }

    /**
     * get lowest price of configurable product children
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {

        $productId = $product->getId();

        if ($childProduct = $this->getSelectedProduct($product)) {
            return $childProduct->getFinalPrice($qty, $childProduct);

        } else {

            if (isset($this->_finalPrice[$productId])) {

            	return $this->_finalPrice[$productId];

            }

            if (isset($this->_cheapest[$productId])) {
                $product = Mage::getModel('catalog/product')->load($this->_cheapest[$productId]);

                $this->_finalPrice[$productId] = $product->getFinalPrice($qty, $product);

                if ($this->_finalPrice[$productId]) {
                	return $this->_finalPrice[$productId];
                }
            }

            if (isset($this->_childPriceDifferent[$productId]) and $this->_childPriceDifferent[$productId] === false) {
                if ($childProduct = $this->getFirstSalable($product)) {
                	$this->_finalPrice[$productId] = $childProduct->getFinalPrice($product);
                    return $this->_finalPrice[$productId];
                }
            }

        	$configurable_products = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
    	    $collection = $configurable_products->getUsedProductCollection()->addAttributeToSelect('*');

    	    $minFinalPrice = false;

            foreach($collection as $childProduct) {
                if(!$childProduct->isSalable()) {
                    continue;
                }

                $finalPrice = $childProduct->getFinalPrice();

                if (!$minFinalPrice or $minFinalPrice > $finalPrice) {
                    $minFinalPrice = $finalPrice;
                    $this->_cheapest[$productId] = $childProduct->getId();
                }

            }

            if ($minFinalPrice) {
            	$this->_finalPrice[$productId] = $minFinalPrice;
            	return $minFinalPrice;
            }

            $this->_finalPrice[$productId] = parent::getFinalPrice($qty, $product);
            return $this->_finalPrice[$productId];

        }
    }

    /*
     * Get promostional price
     * @param   Mage_Catalog_Model_Product $product
     */
    public function getSpecialPrice($product)
    {

        if ($childProduct = $this->getSelectedProduct($product)) {
            return $childProduct->getSpecialPrice();
        } else if ($childProduct = $this->getFirstSalable($product)) {
            return $childProduct->getSpecialPrice();
        }

        return false;
    }

    /*
     * check if all child products have same final prices
     *
     * @param Mage_Catalog_Model_Product
     * @return bool
     */
    public function getIsChildPricesDifferent($product)
    {

        $productId = $product->getId();

        if (isset($this->_childPriceDifferent[$productId])) {
        	return $this->_childPriceDifferent[$productId];
        }

       	$configurable_products = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
	    $collection = $configurable_products->getUsedProductCollection()->addAttributeToSelect('*');

	    $price = false;

        $this->_childPriceDifferent[$productId] = false;

        foreach($collection as $childProduct) {
            if(!$childProduct->isSalable()) {
                continue;
            }

            if (!$price) {

                $price = $childProduct->getFinalPrice();
                $this->_finalPrice[$productId] = $price;
                $this->_minimalPrice[$productId] = $childProduct->getPrice();
            } else {
            	$_price = $childProduct->getFinalPrice();
            	if ($price != $_price) {
            	    $this->_childPriceDifferent[$productId] = true;
                }
                if ($_price < $this->_finalPrice[$productId]) {
                	$this->_finalPrice[$productId] = $_price;
                	$this->_minimalPrice[$productId] = $childProduct->getPrice();
                }
            }

        }

        return $this->_childPriceDifferent[$productId];

    }

    public function getChoosedData($product, $key)
    {
        $rewrite_keys = $product->getRewriteParams();
        if (!in_array($key, $rewrite_keys)) {
            return false;
        }

        if ($childProduct = $this->getSelectedProduct($product)) {
            return $childProduct->getData($key);
        }

        return false;
    }


    private function getSelectedProduct($product)
    {

        $selectedAttributes = array();
        if ($product->getCustomOption('attributes')) {
            $selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
        }

        if (count($selectedAttributes)) {
        	$configurable_products = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
    	    $collection = $configurable_products->getUsedProductCollection()->addAttributeToSelect('*');
            foreach($collection as $childProduct) {
                $checkRes = true;
                foreach ($selectedAttributes as $attributeId => $attributeValue) {
                    $code = $product->getTypeInstance()->getAttributeById($attributeId)->getAttributeCode();
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
    }


    private function getFirstSalable($product)
    {
      	$configurable_products = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
   	    $collection = $configurable_products->getUsedProductCollection()->addAttributeToSelect('*');
        foreach($collection as $childProduct) {
            if (!$childProduct->isSalable()) {
                continue;
            }

            return $childProduct;
        }

        return false;
    }


    //Force tier pricing to be empty for configurable products:
    public function getTierPriceasd($qty=null, $product)
    {
        if ($childProduct = $this->getSelectedProduct($product)) {
            return $childProduct->getTierPrice($qty, $childProduct);
        } else if ($childProduct = $this->getFirstSalable($product)) {
            return $childProduct->getTierPrice($qty, $childProduct);
        }

        return parent::getTierPrice($qty, $product);
    }
}
