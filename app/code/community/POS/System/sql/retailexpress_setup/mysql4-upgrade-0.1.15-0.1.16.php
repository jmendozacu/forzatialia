<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('catalog_product', 'rex_sku', array(
    'label' => 'POS SKU',
    'required' => false,
    'input' => 'text',
    'default' => '',
    'position' => 1,
    'sort_order' => 3,
));
