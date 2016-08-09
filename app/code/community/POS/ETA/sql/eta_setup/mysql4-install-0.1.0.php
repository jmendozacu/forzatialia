<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('cataloginventory_stock_item')}` ADD `use_config_allow_check_availability_status` tinyint(1) unsigned NOT NULL default '1';
");

$installer->run("
    ALTER TABLE `{$installer->getTable('cataloginventory_stock_item')}` ADD `allow_check_availability_status` tinyint(1) unsigned NOT NULL default '1';
");

$installer->run("
    ALTER TABLE `{$installer->getTable('cataloginventory_stock_item')}` ADD `allow_check_availability` tinyint(1) unsigned NOT NULL default '0';
");

$installer->run("
    ALTER TABLE `{$installer->getTable('cataloginventory_stock_item')}` ADD `qty_on_order` INT NOT NULL;
");

$installer->endSetup();

$installer = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
$installer->startSetup();

$installer->addAttribute('order', 'order_eta_combined', array('type' => 'varchar'));

$installer->endSetup();
