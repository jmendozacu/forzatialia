<?php

$this->startSetup();

$this->run("

DROP TABLE IF EXISTS {$this->getTable('magecon_product_video')};
CREATE TABLE {$this->getTable('magecon_product_video')} (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `entity_id` int(15) NOT NULL,
  `youtube_key` varchar(15) NOT NULL,
  `sort_order` int(5) NOT NULL,
  `store_id` smallint(5) NOT NULL,
  `excluded` boolean NOT NULL,
  `removed` boolean NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$this->endSetup();
?>
