<?php

class POS_System_Model_Cron extends Mage_Core_Model_Abstract
{

    /**
     * Daily update
     *
     * @return void
     */
    public function update()
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}

        Mage::helper('retailexpress/data')->waitBulk();
    }

    /**
     * process sync for products bulk method
     * @return void
     */
    public function synchronizeBulk()
    {
    	if (!Mage::getStoreConfig('retailexpress/main/enabled')) {
    		return;
    	}

        Mage::helper('retailexpress/data')->synchronizeBulk();
    }


}
