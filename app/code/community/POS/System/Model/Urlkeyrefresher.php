<?php

class POS_System_Model_Urlkeyrefresher
{
    /**
     * Due to a bug in Magento 1.7, URL rewrites can become excessively large.
     * This method will find all products that aren't visible and appends
     * their ID to the end of their URI key, which reduces the incrase
     * of URL rewrites. Weird, right?
     *
     * @link   http://magento.stackexchange.com/q/17553
     */
    public function run()
    {
        $products = $this->loadProducts();

        foreach ($products as $product) {

            // If the product is not visible, then append it's URL key with it's ID.
            // This ensures visible products still look okay however.
            $suffix = ($product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) ? '-'.$product->getId() : '';

            // Generate a unique new URL key
            $newUrlkey = Mage::getSingleton('catalog/product_url')->formatUrlKey($product->getName().$suffix);

            // If the new URL key doesn't match the existing one, update it
            if ($newUrlkey !== $product->getUrlKey()) {
                $product->setUrlKey($newUrlkey)->getResource()->saveAttribute($product, 'url_key');
            }
        }
    }

    protected function loadProducts()
    {
        return Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect(['url_key', 'name', 'visibility'])
            ->load();
    }
}
