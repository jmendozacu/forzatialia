<?php

$table_attr = $this->getTable('sync_attr');

$this->startSetup()->run("

drop table if exists {$table_attr};
create table {$table_attr} (
	`attr_id` int(10) unsigned not null auto_increment,
	`code` varchar(30) not null DEFAULT '',
	`rex_id` int(10) unsigned not null default 0,
	`magento_id` int(10) unsigned not null default 0,
	PRIMARY KEY(attr_id),
	KEY(rex_id),
	KEY(magento_id),
	KEY(code)
) engine=InnoDB default charset=utf8;

")->endSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('catalog_product', 'rex_product_id', [
    'label' => 'POS Product ID / Style Code',
    'required' => false,
    'input' => 'text',
    'default' => '',
    'position' => 1,
    'sort_order' => 3,
]);
