<?php

$table_conf = $this->getTable('sync_conf');

$this->startSetup();
$this->run("

drop table if exists {$table_conf};
create table {$table_conf} (
	`conf_id` VARCHAR(32) not null DEFAULT '',
	`value` VARCHAR(255) not null DEFAULT '',
	PRIMARY KEY(conf_id),
	KEY(`value`)
) engine=InnoDB default charset=utf8;

");
$entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
$collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
    ->setEntityTypeFilter($entityTypeId);

$id = 0;

foreach ($collection->getItems() as $v) {
    $id = $v->getAttributeSetId();
    if (strtolower($v->getAttributeSetName()) == 'default') {
        break;
    }
}

$model = Mage::getModel('eav/entity_attribute_set')
            ->setEntityTypeId($entityTypeId);
$model->setAttributeSetName('POS System');
$model->save();
$model->initFromSkeleton($id);
$model->save();

$this->run("
INSERT INTO {$table_conf} (conf_id, value) VALUES ('attribute_set', '".$model->getId()."');
");

$this->endSetup();
