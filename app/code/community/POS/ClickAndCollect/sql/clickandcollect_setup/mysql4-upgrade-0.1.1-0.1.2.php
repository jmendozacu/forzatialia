<?php

$installer = $this;

$installer->startSetup();

$id = $installer->getAttributeId('catalog_product', 'store_pickup_rule');
$installer->run("
    UPDATE `{$installer->getTable('eav_attribute')}` SET `default_value` = '3' WHERE `attribute_id` ='{$id}';
");

$installer->endSetup();
