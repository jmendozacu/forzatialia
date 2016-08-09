<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('aitpagecache_target_page_product')} DROP FOREIGN KEY `fk_page_id`;");

$installer->endSetup();