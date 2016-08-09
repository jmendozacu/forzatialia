<?php
/**
 * Magento Booster 1.4+
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpagecache
 * @version      4.0.5
 * @license:     AACcewAJ3nZYMUsItZcwugZ3g4HsbQPMHWb0Pv6oyc
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/
class Aitoc_Aitpagecache_Model_Config_Cron extends Mage_Core_Model_Config_Data
{
	protected function _afterSave(){

        $cronStringPath = 'crontab/jobs/aitpagecache_clear_cache/schedule/cron_expr';
        $cronModelPath = 'crontab/jobs/aitpagecache_clear_cache/run/model';

        $value = $this->getData('groups/aitpagecache_config_cron/fields/aitpagecache_config_cron_frequency/value');
        
        if(!Mage::helper('aitpagecache')->isValidCronExpr($value))
            Mage::throwException('The format of cron expression is invalid');
        
        try{
        Mage::getModel('core/config_data')
             ->load($cronStringPath, 'path')
            ->setValue($value)
            ->setPath($cronStringPath)
            ->save();
        Mage::getModel('core/config_data')
            ->load($cronModelPath, 'path')
            ->setValue((string) Mage::getConfig()->getNode($cronModelPath))
            ->setPath($cronModelPath)
            ->save();
            }
        catch (Exception $e) {
            Mage::throwException('Unable to save the cron expression.');
        }
        }
}