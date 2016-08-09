<?php

$table_job = $this->getTable('sync_job');

$this->startSetup()->run("

ALTER TABLE {$table_job} DROP `job_type`;
ALTER TABLE {$table_job} ADD `action` VARCHAR(50);
ALTER TABLE {$table_job} ADD INDEX (`action`);
ALTER TABLE {$table_job} ADD `history_id` int(10) unsigned not null default 0 AFTER `job_id`;
ALTER TABLE {$table_job} ADD INDEX (`history_id`);

")->endSetup();
