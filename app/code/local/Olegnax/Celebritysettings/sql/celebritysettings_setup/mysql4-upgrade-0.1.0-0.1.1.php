<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

$installer = $this;
$installer->startSetup();
$installer->endSetup();

try {
//create pages and blocks programmatically

//Custom Tab
$staticBlock = array(
    'title' => 'Custom Tab',
    'identifier' => 'celebrity_custom_tab',
    'content' => "<p><strong>Lorem Ipsum</strong><span>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</span></p>",
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();

//Custom Block
$staticBlock = array(
    'title' => 'Custom Block',
    'identifier' => 'celebrity_navigation_block',
    'content' => "<p><strong>Lorem Ipsum</strong><span>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</span></p>",
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();

//Empty Category
$staticBlock = array(
    'title' => 'Empty Category',
    'identifier' => 'celebrity_empty_category',
    'content' => "<p>There are no products matching the selection.<br/> This is a static CMS block displayed if category is empty. You can put your own content here.</p>",
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();

//Informational block under product tabs
$staticBlock = array(
    'title' => 'Informational block under product tabs',
    'identifier' => 'celebrity_after_tabs',
    'content' => '<p class="dotted-border"> This is a static CMS block displayed after product tabs. You can put your own content here or disable block to hide it</p>',
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();

//Celebrity Bottom Support block
$staticBlock = array(
    'title' => 'Celebrity Bottom Support block',
    'identifier' => 'celebrity_bottom_support',
    'content' => '<div class="site-block bottom"><a href="http://olegnax.com/products/magento/"><img src="{{media url="olegnax/celebrity/live_support.png"}}" alt="" /></a></div>',
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();

//Celebrity Store Logo
$staticBlock = array(
    'title' => 'Celebrity Store Logo',
    'identifier' => 'celebrity_logo',
    'content' => '<img src="{{media url="olegnax/celebrity/logo.png"}}" alt="Celebrity Store" />',
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();


}
catch (Exception $e) {
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occurred while installing celebrity theme pages and cms blocks.'));
}