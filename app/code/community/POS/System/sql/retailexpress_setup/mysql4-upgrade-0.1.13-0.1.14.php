<?php

$installer = $this;

$installer->startSetup();

$table_outlet = $installer->getTable('sync_outlet');

$installer->run("
drop table if exists {$table_outlet};
create table {$table_outlet} (
	`outlet_id` int(11) unsigned not null auto_increment,
	`fulfilment_outlet_id` int(11) unsigned,
	`outlet_name` varchar(255) default '',
	`address_1` varchar(255) default '',
	`address_2` varchar(255) default '',
	`address_3` varchar(255) default '',
	`suburb` varchar(50) default '',
	`state` varchar(50) default '',
	`postcode` varchar(10) default '',
	`country` varchar(50) default '',
    `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`comment` TEXT,
	PRIMARY KEY(outlet_id)
) engine=InnoDB default charset=utf8;
");

$installer->endSetup();
