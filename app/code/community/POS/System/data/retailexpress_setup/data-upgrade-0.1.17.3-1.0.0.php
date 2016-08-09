<?php

$configPath = 'retailexpress/main/cron';
$cronExpression = Mage::getStoreConfig($configPath);

try {
    Mage::getModel('cron/schedule')->setCronExpr($cronExpression);
} catch (Exception $e) {
    $offendingRecord = Mage::getModel('core/config_data')->load($configPath, 'path');
    $offendingRecord->delete();
}
