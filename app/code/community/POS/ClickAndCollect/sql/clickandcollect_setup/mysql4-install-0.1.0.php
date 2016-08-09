<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'store_pickup_rule', array(
    'group' => 'General',
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Store Pickup Rule',
    'input' => 'select',
    'class' => '',
    'source' => 'clickandcollect/product_attribute_source_pickuprule',
    'default' => 'no_selection',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => 'no_selection',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
    'is_configurable' => false,
);

//attribute is still visible in General Tab by some reason, so will hide it with direct sql query
$id = $installer->getAttributeId('catalog_product', 'store_pickup_rule');
$installer->run("
    UPDATE `{$installer->getTable('catalog_eav_attribute')}` SET `is_visible` = '0' WHERE `attribute_id` ='{$id}';
");

$installer->endSetup();

$installer = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
$installer->startSetup();

$installer->addAttribute('order', 'clickandcollect_order_comment', ['type' => 'text']);
$installer->addAttribute('order', 'clickandcollect_order_fulfilmentouletid', ['type' => 'varchar']);

$installer->endSetup();
