<?php

$installer = $this;
$installer->startSetup();
$table_product = $this->getTable('sync_product');

$installer->run("
drop table if exists {$table_product};
create table {$table_product} (
	`entity_id` int(11) unsigned not null auto_increment,
	`product_id` int(10) unsigned not null,
    `last_date` int(10) unsigned not null,
	PRIMARY KEY(entity_id),
	KEY(product_id, last_date)
) engine=InnoDB default charset=utf8;

");

$installer->endSetup();
