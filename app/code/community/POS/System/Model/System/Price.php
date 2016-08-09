<?php

class POS_System_Model_System_Price extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        $input = [
            'CustomerDiscountedPrice' => 'Calculated Pricing (customer based and time based discounts)',
            'DiscountedPrice' => 'Calculated Pricing (time based discounts only)',
            'DefaultPrice' => 'Calculated Pricing (discounts disabled)',
            'WebSellPrice' => 'Web Sell Price',
            'WebPrice' => 'Web Price',
            'RRP' => 'RRP',
        ];

        $array = [];
        foreach ($input as $key => $value) {
            $array[] = [
                'value' => $key,
                'label' => $value,
            ];
        }

        return $array;
    }
}
