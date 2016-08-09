<?php

$installer = $this;
$installer->startSetup();
$table_product = $this->getTable('sync_product');

$installer->run("
ALTER TABLE `sync_diagnostic_list` ADD `last_status` ENUM('warning', 'success', 'fail', 'error','unknown') NOT NULL DEFAULT 'unknown' ;
");

$installer->endSetup();
