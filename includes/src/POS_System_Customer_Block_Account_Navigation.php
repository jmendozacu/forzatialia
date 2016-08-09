<?php

class POS_System_Customer_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{


    public function getLinks()
    {
        $return = parent::getLinks();
        foreach ($return as $k => $v) {
            $sorted[$k] = $v;
            if ($k == 'orders') {
                if (isset($return['retailexpress'])) {
                    $sorted['retailexpress'] = $return['retailexpress'];
                }
            }

            if ($k == 'retailexpress') {
                continue;
            }
        }

        return $sorted;
    }


}