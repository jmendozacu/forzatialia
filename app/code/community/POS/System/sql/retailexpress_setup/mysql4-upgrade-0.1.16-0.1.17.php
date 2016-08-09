<?php

$installer = $this;
$installer->startSetup();
$table_product = $this->getTable('sync_product');

$installer->run("
DROP TABLE IF EXISTS {$table_product};
CREATE TABLE IF NOT EXISTS {$table_product} (
    `entity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `product_id` int(10) unsigned NOT NULL,
    `last_date` timestamp NULL DEFAULT NULL,
    `customer_id` int(10) NOT NULL,
    `price` decimal(12,4) NOT NULL,
    `special_price` decimal(12,4) NOT NULL,
    PRIMARY KEY (`entity_id`),
    KEY `product_id` (`product_id`,`last_date`),
    KEY `product_id_2` (`product_id`,`customer_id`,`last_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
