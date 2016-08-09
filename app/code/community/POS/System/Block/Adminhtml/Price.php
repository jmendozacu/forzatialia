<?php

class POS_System_Block_Adminhtml_Price extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
{
    public function __construct()
    {
        $this->setTemplate('retailexpress/price.phtml');
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout.
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label' => Mage::helper('catalog')->__('Add POS price'),
                'onclick' => 'return posPriceControl.addItem()',
                'class' => 'add',
            ]);
        $button->setName('add_pos_prices_item_button');

        $this->setChild('add_button', $button);

        return $this;
    }

    /**
     * Show tier prices grid website column.
     *
     * @return bool
     */
    public function isShowWebsiteColumn()
    {
        return false;
    }

    /**
     * Show Website column and switcher for tier price table.
     *
     * @return bool
     */
    public function isMultiWebsites()
    {
        return false;
    }

    /**
     * Prepare Tier Price values.
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        $product = Mage::registry('product');
        if ($product && $product->getId()) {
            $pos = Mage::getModel('retailexpress/product')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $product->getId());
            foreach ($pos->getItems() as $i) {
                if (!$i->getCustomerId()) {
                    continue;
                }

                if (!Mage::getModel('retailexpress/conf')->load('group_'.$i->getCustomerId())->getValue()) {
                    continue;
                }

                $values[] = [
                    'cust_group' => $i->getCustomerId(),
                    'price' => $i->getPrice() ? $i->getPrice() : $product->getPrice(),
                    'special_price' => $i->getSpecialPrice() ? $i->getSpecialPrice() : $product->getSpecialPrice(),
                    'readonly' => true,
                ];
            }
        }

        return $values;
    }
}
