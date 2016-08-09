<?php

class POS_System_Model_Configvalidation extends Mage_Core_Model_Config_Data
{
    /**
     * save.
     *
     * Override the core function save and validate URL
     */
    public function save()
    {
        $url = $this->getValue();
        //remove white spaces
        $url = trim($url);
        $this->setValue($url);

        return parent::save();
    }
}
