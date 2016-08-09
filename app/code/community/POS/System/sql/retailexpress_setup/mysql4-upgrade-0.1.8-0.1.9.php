<?php

$table_product = $this->getTable('sync_product');

$this->startSetup();
$this->run("

alter table $table_product ADD customer_id int (10) not null default 0;
alter table $table_product ADD price decimal(12,4) not null default 0;
alter table $table_product ADD special_price decimal(12,4) not null default 0;
alter table $table_product ADD INDEX (product_id, customer_id, last_date);

");

$this->endSetup();
