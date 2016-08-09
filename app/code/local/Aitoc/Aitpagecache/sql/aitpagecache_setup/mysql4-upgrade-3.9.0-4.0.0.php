<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('aitpagecache_emails')}` (
`email_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`customer_id` INT NOT NULL ,
`customer_email` VARCHAR( 255 ) NOT NULL ,
`sent_at` DATETIME NULL DEFAULT NULL ,
`rule_id` INT NOT NULL ,
INDEX ( `customer_id` , `rule_id` )
) ENGINE = InnoDB ;

    ");
//Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) didn't worked for some reason
$url = Mage::getStoreConfig('web/unsecure/base_url');
if(!$url)
{
    $url = '/';
}
else
{
    $url .= (($url[strlen($url)-1]=='/')?'':'/');
}
$url .= 'media/aitoc/aitpagecache/stc.swf';    
$block = Mage::getModel('cms/block');
$block->setData(array(
    'title'         => 'Tetris Game',
    'identifier'    => 'tetris',
    'content'       => '<div style="text-align: center;"><object width="480" height="272" type="application/x-shockwave-flash" data="'.$url.'"></object></div>',
    'is_active'     => 1,
))->setStores(array(0))->save();

Mage::getModel('core/config')->saveConfig('aitpagecache/aitpagecache_config_aitloadmon/block_block', (int)$block->getId() ); 

$installer->endSetup();