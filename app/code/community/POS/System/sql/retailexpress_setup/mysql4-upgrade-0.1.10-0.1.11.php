<?php

$table_payment = $this->getTable('sync_payment');

$this->startSetup();

$attributes = [
    'pos_sizes' => 'Size',
    'pos_colours' => 'Colour',
    'pos_seasons' => 'Season',
    'pos_product_types' => 'Product type',
    'pos_brands' => 'Brand',
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
        'is_configurable' => '1',
        'is_filterable' => '1',
        'is_filterable_in_search' => '1',
        'used_for_sort_by' => '1',
        'user_defined' => '1',
        'is_visible_in_advanced_search' => 1,
        'is_comparable' => 1,
    ];

    if ($code == 'rex_product_type') {
        $data['is_filterable'] = 0;
        $data['is_filterable_in_search'] = 0;
        $data['used_for_sort_by'] = 0;
    }

    $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
    $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);

    $setup->addAttribute('catalog_product', $code, [
        'label' => $name,
        'required' => false,
        'input' => 'select',
        'default' => '',
        'position' => 1,
        'sort_order' => 3,
        'group' => 'POS attributes',
        'user_defined' => true,
    ]);
    $id = $setup->getAttributeId('catalog_product', $code);
    $model->load($id)->addData($data)->save();
    Mage::getModel('core/config')->saveConfig('temando/attr/'.str_replace('pos_', 'rex_', $code), $code, 'default', 0);
}

$this->endSetup();
