<?php

$collection = Mage::getModel('retailexpress/diagnostic')->getCollection();
foreach ($collection->getItems() as $item) {
    if ($item->getData('type') == 'yes_no') {
        $item->setData('type', 'yes_check');
        $item->save();
    }
}

$item = Mage::getModel('retailexpress/diagnostic')->load(30);
$item->setData('type', 'no_check');
$item->save();

$item = $collection = null;
