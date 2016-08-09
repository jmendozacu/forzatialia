<?php

$installer = $this;
$installer->startSetup();

$table_sync_log = $this->getTable('sync_log');
$table_sync_diagnostic_list = $this->getTable('sync_diagnostic_list');

$installer->run("
CREATE TABLE IF NOT EXISTS {$table_sync_log} (
  `log_id` int(20) NOT NULL AUTO_INCREMENT,
  `method` varchar(200) NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_request` longtext CHARACTER SET utf8 NOT NULL,
  `sync_response` longtext CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

drop table if exists {$table_sync_diagnostic_list};
CREATE TABLE IF NOT EXISTS {$table_sync_diagnostic_list} (
  `list_id` int(20) NOT NULL AUTO_INCREMENT,
  `config_id` int(20) NOT NULL,
  `section` varchar(100) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `name` varchar(200) NOT NULL,
  `path` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `order` int(10) NOT NULL,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO {$table_sync_diagnostic_list} (`config_id`, `section`, `section_name`, `name`, `path`, `type`, `order`) VALUES
(443, 'general', 'General Mapping and Configuration', 'POS System plugin is enabled', 'retailexpress/main/enabled', 'yes_no', 1),
(444, 'general', 'General Mapping and Configuration', 'Valid URL provided for connection to Retail Express', 'retailexpress/main/url', 'custom', 2),
(445, 'general', 'General Mapping and Configuration', 'Client ID provided for connection to POS System', 'retailexpress/main/client_id', 'has_value', 3),
(446, 'general', 'General Mapping and Configuration', 'POS System Username provided for connection to POS System', 'retailexpress/main/username', 'has_value', 4),
(447, 'general', 'General Mapping and Configuration', 'POS System Password provided for connection to POS System', 'retailexpress/main/password', 'has_value', 5),
(449, 'general', 'General Mapping and Configuration', 'Bulk Import Method set to Fast Mode', 'retailexpress/main/sync_type', 'custom', 7),
(450, 'general', 'General Mapping and Configuration', 'Synchronising only updated items', 'retailexpress/main/sync_new', 'custom', 8),
(451, 'general', 'General Mapping and Configuration', 'Magento attribute Size mapped to POS System attribute', 'retailexpress/attr/rex_sizes', 'custom', 9),
(452, 'general', 'General Mapping and Configuration', 'Magento attribute Colour mapped to POS System attribute', 'retailexpress/attr/rex_colours', 'custom', 10),
(453, 'general', 'General Mapping and Configuration', 'Magento attribute Season mapped to POS System attribute', 'retailexpress/attr/rex_seasons', 'custom', 11),
(454, 'general', 'General Mapping and Configuration', 'Magento attribute Product Type mapped to POS System attribute', 'retailexpress/attr/rex_product_types', 'custom', 12),
(455, 'general', 'General Mapping and Configuration', 'Magento attribute Brands Type mapped to POS System attribute', 'retailexpress/attr/rex_brands', 'custom', 13),
(0, 'general', 'General Mapping and Configuration', 'URL, Client ID, Username and Password are valid and connect to POS System', '', 'soap_request', 6),
(0, 'general', 'General Mapping and Configuration', 'Magento attributes are mapped to POS System attributes more than once', '', 'check_attribute_count', 14),
(0, 'general', 'General Mapping and Configuration', 'Each enabled Payment Method is mapped to a POS System payment method', '', 'check_payment_methods', 16),
(0, 'hosting', 'Hosting and Environment Setup', 'Supported Magento version (1.5.1.0)', '', 'get_magento_version', 17),
(0, 'hosting', 'Hosting and Environment Setup', 'Permissions set to read/write (777) on /var/retail and all subfolders', '', 'check_permissions', 18),
(0, 'hosting', 'Hosting and Environment Setup', 'Permissions set to read/write (777) on PHP temp directory', '', 'check_permissions_tmp', 19),
(0, 'hosting', 'Hosting and Environment Setup', 'SOAP is supported on the server', '', 'check_soap', 20),
(0, 'hosting', 'Hosting and Environment Setup', 'gZip supported', '', 'check_gz', 21),
(0, 'hosting', 'Hosting and Environment Setup', 'Valid SSL Certificate installed', '', 'check_ssl', 22),
(0, 'setup', 'Magento Setup and Configuration', 'Store information not entered (System > Sales > Checkout > Checkout Options > Store Information)', '', 'store_information', 23),
(0, 'setup', 'Magento Setup and Configuration', 'Store Email Address General Contact not defined (System > Configuration > General > Store Email Addresses >  General Contact)', '', 'check_store_email_general', 24),
(0, 'setup', 'Magento Setup and Configuration', 'Store Email Address Sales Representative not defined (System > Configuration > General > Store Email Addresses >  Sales Representative)', '', 'check_store_email_sales', 25),
(0, 'setup', 'Magento Setup and Configuration', 'Store Email Address Customer Support not defined (System > Configuration > General > Contacts >  Email Options)', '', 'check_store_email_support', 26),
(494, 'setup', 'Magento Setup and Configuration', 'Guest checkout is disabled (System > Configuration > Sales > Checkout >  Checkout Options)', 'checkout/options/guest_checkout', 'custom', 27),
(0, 'setup', 'Magento Setup and Configuration', 'Shipping origin set (System > Configuration > Sales >  Shipping Settings)', '', 'check_shipping_settings', 28),
(0, 'setup', 'Magento Setup and Configuration', 'At least one Payment Method enabled (System > Configuration > Sales > Checkout >  Payment Methods)', '', 'check_active_payment_method', 29),
(254, 'setup', 'Magento Setup and Configuration', 'Saved CC Payment Method disabled (System > Configuration > Sales > Checkout >  Payment Methods)', 'payment/ccsave/active', 'custom', 30),
(418, 'setup', 'Magento Setup and Configuration', 'Disable all email communication enabled (only concerning when in implementation) (System > Configuration > Advanced > System > Mail Sending Setting)', 'system/smtp/disable', 'yes_no', 31),
(0, 'data', 'Data', 'Less than 15k products', '', 'get_product_count', 32),
(0, 'data', 'Data', 'Large product images', '', 'get_product_image', 34),
(0, 'hosting', 'Hosting and Environment Setup', 'CRON job set to run /cron.php every 5 minutes', '', 'get_cron', 17),
(0, 'hosting', 'Hosting and Environment Setup', 'Domain is pointed with an A Record (only concerning if live)', '', 'check_url_redirect', 21),
(0, 'setup', 'Magento Setup and Configuration', 'Compilation Disabled (System > Tools > Compilation)', '', 'get_compilation_status', 33);
");

$installer->endSetup();
