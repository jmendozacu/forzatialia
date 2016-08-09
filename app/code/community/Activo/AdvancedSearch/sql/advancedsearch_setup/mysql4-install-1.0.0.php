<?php
/**
 * Activo Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Activo Commercial License
 * that is available through the world-wide-web at this URL:
 * http://extensions.activo.com/license_professional
 *
 * @copyright   Copyright (c) 2012 Activo Extensions (http://extensions.activo.com)
 * @license     Commercial
 */
 
$installer = $this;
$installer->startSetup();
 
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('activo_advancedsearch_word')};
CREATE TABLE {$this->getTable('activo_advancedsearch_word')} (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `soundex` VARCHAR(4) NOT NULL,
  `word` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `soundex_idx` (`soundex`),
  UNIQUE KEY `word_idx` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('activo_advancedsearch_word_product')};
CREATE TABLE {$this->getTable('activo_advancedsearch_word_product')} (
  `word_id` INT(10) UNSIGNED NOT NULL,
  `product_id` INT(10) UNSIGNED NOT NULL,
  `attribute_id` INT(10) UNSIGNED NOT NULL,
  KEY (`word_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('activo_advancedsearch_dictionary')};
CREATE TABLE {$this->getTable('activo_advancedsearch_dictionary')} (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_products` INT(10) UNSIGNED NOT NULL,
  `num_words` INT(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
 
$installer->endSetup();
