<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

	CREATE TABLE `{$this->getTable('aitpagecache_target_page')}` (
	    `page_id` int(10) unsigned NOT NULL auto_increment,
	    `page_path` varchar(255) NOT NULL DEFAULT '',
	    PRIMARY KEY  (`page_id`))
	ENGINE=InnoDB CHARSET=utf8;
	
	CREATE TABLE `{$this->getTable('aitpagecache_target_page_product')}` (
	    `product_id` int(10) unsigned NOT NULL,
	    `page_id` int(10) unsigned NOT NULL)
	ENGINE=InnoDB CHARSET=utf8;
	
	ALTER TABLE `{$this->getTable('aitpagecache_target_page_product')}`
  	    ADD CONSTRAINT `fk_page_id` FOREIGN KEY (`page_id`) REFERENCES `".$installer->getTable('aitpagecache_target_page')."` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE;
  	    
");

$installer->endSetup();