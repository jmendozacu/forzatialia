<?php

$table_payment = $this->getTable('sync_payment');

$this->startSetup();
$this->run("

drop table if exists {$table_payment};
create table {$table_payment} (
	`mag_id` int(10) unsigned not null auto_increment,
	`rex_id` int(10) unsigned not null,
	`name` varchar(50) default '',
	PRIMARY KEY(mag_id),
	KEY(rex_id)
) engine=InnoDB default charset=utf8;

");

$this->endSetup();
