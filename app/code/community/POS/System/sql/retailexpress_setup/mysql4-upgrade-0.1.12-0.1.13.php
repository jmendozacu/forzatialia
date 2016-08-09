<?php

$table_history = $this->getTable('sync_history');

$this->startSetup();

$this->run("
alter table {$table_history} ADD created_at timestamp NULL default NULL;
UPDATE {$table_history} SET created_at = created_date;
");
$this->endSetup();
