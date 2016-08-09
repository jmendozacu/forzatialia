<?php

class POS_System_Model_Product_Attr extends Mage_Core_Model_Abstract
{
    protected $_allowedAttributes = [
        'name' => 'Name',
        'description' => 'Description',
        'meta_description' => 'Meta Description',
        'meta_keyword' => 'Meta Keywords',
        'meta_title' => 'Meta Title',
        'short_description' => 'Short Description',
        'thumbnail' => 'Thumb Image',
        'small_image' => 'Small Image',
        'image' => 'Base Image',
        'news_from_date' => 'Product New From Date',
        'news_to_date' => 'Product New To Date',
        'visibility' => 'Visibility',
        'gift_message_available' => 'Allow Gift Message',
        'category_ids' => 'Category (ID)',
    ];

    public function toOptionArray()
    {
        $return = [
            0 => [
                'value' => '',
                'label' => '',
            ],
        ];

        foreach ($this->_allowedAttributes as $value => $label) {
            $return[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $return;
    }
}
