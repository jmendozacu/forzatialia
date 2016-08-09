<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$pageTable = $installer->getTable('slideshowtimeline/slideshowtimeline');
$installer->getConnection()->addColumn($pageTable, 'sort_order',
    "SMALLINT(6) NOT NULL DEFAULT '0' AFTER `status`");

$installer->endSetup();