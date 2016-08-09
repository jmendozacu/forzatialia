<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
DELETE FROM {$this->getTable('core_config_data')} WHERE path='crontab/jobs/aitpagecache_clear_cache/schedule/cron_expr';
DELETE FROM {$this->getTable('core_config_data')} WHERE path='crontab/jobs/aitpagecache_clear_cache/run/model';
DELETE FROM {$this->getTable('core_config_data')} WHERE path='aitpagecache/aitpagecache_config_cron/aitpagecache_config_cron_frequency';
INSERT INTO {$this->getTable('core_config_data')} (`path`,`value`)  
VALUES('crontab/jobs/aitpagecache_clear_cache/schedule/cron_expr','*/15 */1 * * *'),
('crontab/jobs/aitpagecache_clear_cache/run/model','aitpagecache/observer::clearCache'), 
('aitpagecache/aitpagecache_config_cron/aitpagecache_config_cron_frequency','*/15 */1 * * *');
");

$installer->endSetup();