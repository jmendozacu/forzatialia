<?php

$table_payment = $this->getTable('sync_payment');

$this->startSetup();

$attributes = [
    'pos_prices' => 'POS Prices',
];
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

/* @var $model Mage_Catalog_Model_Entity_Attribute */
$model = Mage::getModel('catalog/resource_eav_attribute');
/* @var $helper Mage_Catalog_Helper_Product */
$helper = Mage::helper('catalog/product');

foreach ($attributes as $code => $name) {
    $data = [
        'attribute_code' => $code,
        'is_global' => '1',
        'is_unique' => '0',
        'is_required' => '0',
        'is_configurable' => '0',
        'user_defined' => '0',
    ];

    $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
    $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);

    $setup->addAttribute('catalog_product', $code, [
        'label' => $name,
        'required' => false,
        'input' => 'price',
        'default' => '',
        'position' => 10,
        'sort_order' => 30,
        'group' => 'Prices',
        'user_defined' => false,
    ]);
    $id = $setup->getAttributeId('catalog_product', $code);
    $model->load($id)->addData($data)->save();
}

$this->endSetup();
