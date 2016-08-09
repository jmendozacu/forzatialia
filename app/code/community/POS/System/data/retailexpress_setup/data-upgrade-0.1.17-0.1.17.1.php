<?php

$item = Mage::getModel('retailexpress/diagnostic')->load(16);
$item->name = 'Supported Magento version (1.7)';
$item->save();

unset($item);
