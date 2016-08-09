<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('slideshowtimeline')}`;
CREATE TABLE `{$this->getTable('slideshowtimeline')}` (
  `slide_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` tinyint(1) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `details` tinyint(1) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('slideshowtimeline')}` (`slide_id`, `store_id`, `title`, `link`, `details`, `image`, `status`, `created_time`, `update_time`) VALUES (1, 0, 'Leonardo di Caprio', 'http://olegnax.com', 1, 'olegnax/slideshowtimeline/slide1.jpg', 1, '2012-02-28 14:13:05', '2012-02-28 14:13:05');
INSERT INTO `{$this->getTable('slideshowtimeline')}` (`slide_id`, `store_id`, `title`, `link`, `details`, `image`, `status`, `created_time`, `update_time`) VALUES (2, 0, 'Second', 'http://olegnax.com/products/magento', 1, 'olegnax/slideshowtimeline/slide2.jpg', 1, '2012-02-27 15:59:34', '2012-02-27 15:59:34');
INSERT INTO `{$this->getTable('slideshowtimeline')}` (`slide_id`, `store_id`, `title`, `link`, `details`, `image`, `status`, `created_time`, `update_time`) VALUES (3, 0, '', '', 0, 'olegnax/slideshowtimeline/slide3.jpg', 1, '2012-02-27 15:59:50', '2012-02-27 15:59:50');

");

$installer->endSetup();