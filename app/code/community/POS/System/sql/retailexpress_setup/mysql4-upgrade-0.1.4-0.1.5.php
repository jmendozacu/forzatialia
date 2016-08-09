<?php

$table_job = $this->getTable('sync_job');
$table_job_item = $this->getTable('sync_job_item');

$this->startSetup()->run("
drop table if exists {$table_job_item};
create table {$table_job_item} (
	`job_item_id` int(10) unsigned not null auto_increment,
	`job_id` int(10) unsigned not null default 0,
	`magento_id` int(10) unsigned not null default 0,
	`rex_id` int(10) unsigned not null default 0,
	`comment` TEXT,
	PRIMARY KEY(job_item_id),
	KEY(job_id),
	KEY(magento_id),
	KEY(rex_id)
) engine=InnoDB default charset=utf8;

ALTER TABLE {$table_job} DROP `magento_id`;
ALTER TABLE {$table_job} DROP `rex_id`;
ALTER TABLE {$table_job} DROP `comment`;
ALTER TABLE {$table_job} ADD error_text TEXT;
ALTER TABLE {$table_job} ADD request_xml TEXT;
ALTER TABLE {$table_job} ADD response_xml TEXT;

")->endSetup();
