<?php

$table_job = $this->getTable('sync_job');
$table_history = $this->getTable('sync_history');

$this->startSetup()->run("
drop table if exists {$table_job};
create table {$table_job} (
	`job_id` int(10) unsigned not null auto_increment,
	`job_type` char(1) default '',
	`magento_id` int(10) unsigned not null default 0,
	`rex_id` int(10) unsigned not null default 0,
    `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`comment` TEXT,
	PRIMARY KEY(job_id),
	KEY(job_type),
	KEY(magento_id),
	KEY(rex_id),
	KEY(created_date)
) engine=InnoDB default charset=utf8;

ALTER TABLE {$table_history} ADD INDEX (created_date);

")->endSetup();
