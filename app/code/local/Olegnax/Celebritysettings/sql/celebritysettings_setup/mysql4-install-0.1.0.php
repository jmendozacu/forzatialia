<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

$installer = $this;
$installer->startSetup();
/*
 * do not work on windows - denwer, xampp etc....
 * cant find the reason for now
 *
 * $installer->run("
INSERT INTO `{$installer->getTable('cms_page')}` ( `title`, `root_template`, `meta_keywords`, `meta_description`, `identifier`, `content_heading`, `content`, `creation_time`, `update_time`, `is_active`, `sort_order`, `layout_update_xml`, `custom_theme`, `custom_root_template`, `custom_layout_update_xml`, `custom_theme_from`, `custom_theme_to`) VALUES ('Celebrity Home page', 'one_column', NULL, NULL, 'celebrity_home', NULL,'<div class=\"white-container\">{{block type=\"slideshowtimeline/slideshowtimeline\" name=\"slideshowtimeline\" template=\"olegnax/slideshowtimeline/slideshowtimeline.phtml\" }}<div class=\"banners\"><a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/banner1.jpg\"}}\" alt=\"\" /> </a> <a class=\"text-right\" href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/banner2.jpg\"}}\" alt=\"\" /><span>SEASONAL<br />DISCOUNTS</span></a> <a class=\"text-center\" href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/banner3.jpg\"}}\" alt=\"\" /><span>ON SALE</span></a></div><div class=\"clear\">&nbsp;</div></div><p>{{block type=\"celebrityslider/slider\" template=\"olegnax/celebrityslider/slider.phtml\"}}</p>', '2007-08-23 10:03:25', '2012-03-10 09:45:42', 1, 0, '', NULL, NULL, NULL, NULL, NULL);
SET @CMS_PAGE_HOME := LAST_INSERT_ID();
INSERT INTO `{$installer->getTable('cms_page_store')}` (`page_id`,`store_id`) VALUES (@CMS_PAGE_HOME, 0);

INSERT INTO `cms_block` (`title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) VALUES ('Celebrity Newsletter block text', 'celebrity_newsletter_text', '<p>There are many variations of passages of Lorem Ipsum  available, but the majority have suffered alteration in some  form, by injected humour, or randomised words which</p>', '2012-02-16 15:44:36', '2012-02-23 16:30:37', 1);
SET @CMS_BLOCK := LAST_INSERT_ID();
INSERT INTO `{$installer->getTable('cms_block_store')}` (`block_id`,`store_id`) VALUES (@CMS_BLOCK, 0);

INSERT INTO `cms_block` (`title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) VALUES ('Celebrity Additional links', 'celebrity_additional_links', '<p><a href=\"#\">Customer Assistance</a> <a href=\"#\">Email Us</a> <a href=\"#\">Payment Methods</a> Tel 888-475-7674</p>', '2012-02-20 08:49:35', '2012-02-23 16:30:10', 1);
SET @CMS_BLOCK := LAST_INSERT_ID();
INSERT INTO `{$installer->getTable('cms_block_store')}` (`block_id`,`store_id`) VALUES (@CMS_BLOCK, 0);

INSERT INTO `cms_block` (`title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) VALUES ('Celebrity Footer brands', 'celebrity_footer_brands', '<p><a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand1.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand2.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand3.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand4.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand5.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand6.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand7.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand8.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand9.gif\"}}\" alt=\"\" /></a></p>', '2012-02-23 16:28:03', '2012-02-29 16:27:03', 1);
SET @CMS_BLOCK := LAST_INSERT_ID();
INSERT INTO `{$installer->getTable('cms_block_store')}` (`block_id`,`store_id`) VALUES (@CMS_BLOCK, 0);

INSERT INTO `cms_block` (`title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) VALUES ('Celebrity Footer links', 'celebrity_footer_links', '<ul class=\"footer-links\">\r\n		    <li>\r\n			    <span>Company</span>\r\n			    <ul>\r\n				    <li><a href=\"#\">About</a></li>\r\n				    <li><a href=\"#\">Careers</a></li>\r\n				    <li><a href=\"#\">Technologie</a></li>\r\n				    <li><a href=\"#\">FAQ</a></li>\r\n				    <li><a href=\"#\">E-Gift Cards</a></li>\r\n			    </ul>\r\n		    </li>\r\n		    <li>\r\n			    <span>Policies</span>\r\n			    <ul>\r\n                    <li><a href=\"#\">Terms Of Service</a></li>\r\n                    <li><a href=\"#\">Privacy</a></li>\r\n                    <li><a href=\"#\">Security</a></li>\r\n                    <li><a href=\"#\">Terms of Use</a></li>\r\n                </ul>\r\n		    </li>\r\n		    <li>\r\n			    <span>Customer Service</span>\r\n			    <ul>\r\n                    <li><a href=\"#\">Contact Us</a></li>\r\n                    <li><a href=\"#\">Feedback</a></li>\r\n                    <li><a href=\"#\">Return Policy</a></li>\r\n                    <li><a href=\"#\">Shipping & Tax</a></li>\r\n                    <li><a href=\"#\">International</a></li>\r\n                </ul>\r\n		    </li>\r\n		    <li>\r\n			    <span>Connect with Us</span>\r\n			    <ul class=\"footer-social\">\r\n                    <li><a class=\"twitter\" href=\"#\">Twitter</a></li>\r\n                    <li><a class=\"facebook\" href=\"#\">Facebook</a></li>\r\n                    <li><a class=\"email\" href=\"#\">Email</a></li>\r\n                    <li><a class=\"google\" href=\"#\">Google</a></li>\r\n                </ul>\r\n		    </li>\r\n	    </ul>', '2012-02-29 16:25:01', '2012-02-29 16:25:01', 1);
SET @CMS_BLOCK := LAST_INSERT_ID();
INSERT INTO `{$installer->getTable('cms_block_store')}` (`block_id`,`store_id`) VALUES (@CMS_BLOCK, 0);

");*/
$installer->endSetup();

try {
//create pages and blocks programmatically
//home page
$cmsPage = array(
    'title' => 'Celebrity Home Page',
    'identifier' => 'celebrity_home',
    'content' => "<div class=\"white-container clearfix\">{{block type=\"slideshowtimeline/slideshowtimeline\" name=\"slideshowtimeline\" template=\"olegnax/slideshowtimeline/slideshowtimeline.phtml\" }}<div class=\"banners\"><a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/banner1.jpg\"}}\" alt=\"\" /> </a> <a class=\"text-right\" href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/banner2.jpg\"}}\" alt=\"\" /><span>SEASONAL<br />DISCOUNTS</span></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/banner3.jpg\"}}\" alt=\"\" /></a></div><div class=\"clear\">&nbsp;</div></div>{{block type=\"celebrityslider/slider\" template=\"olegnax/celebrityslider/slider.phtml\"}}",
    'is_active' => 1,
    'sort_order' => 0,
    'stores' => array(0),
    'root_template' => 'one_column'
);
Mage::getModel('cms/page')->setData($cmsPage)->save();
//404 page
$cmsPage = array(
    'title' => 'Celebrity 404 No Route',
    'identifier' => 'celebrity_no_route',
    'content' => '<div class="col-main-left">
    <div class="page-head-alt">
    <h3>Whoops, our bad...</h3>
    </div>
    <dl> <dt>The page you requested was not found, and we have a fine guess why.</dt> <dd>
    <ul class="disc">
    <li>If you typed the URL directly, please make sure the spelling is correct.</li>
    <li>If you clicked on a link to get here, the link is outdated.</li>
    </ul>
    </dd> </dl>
    <p>&nbsp;</p>
    <dl> <dt>What can you do?</dt> <dd>Have no fear, help is near! There are many ways you can get back on track with Magento Demo Store.</dd> <dd>
    <ul class="buttons">
    <li><button class="button" onclick="history.go(-1);"><span><span>Go back</span></span></button></li>
    <li><button class="button" onclick="location.href=\'{{store url=\'\'}}\'"><span><span>Store Home</span></span></button></li>
    <li><button class="button" onclick="location.href=\'{{store url=\'customer/account\'}}\'"><span><span>My Account</span></span></button></li>
    </ul>
    </dd></dl></div>
    <div class="col-right">
    <div class="banner"><a href="#"><img src="{{media url="olegnax/celebrity/right_banner1.jpg" }}" alt="" /></a></div>
    <div class="banner"><a class="text-right" href="#"><img src="{{media url="olegnax/celebrity/right_banner2.jpg" }}" alt="" /><span>SEASON<br />DISCOUNTS</span></a></div>
    </div>',
    'is_active' => 1,
    'sort_order' => 0,
    'stores' => array(0),
    'root_template' => 'one_column'
);
Mage::getModel('cms/page')->setData($cmsPage)->save();

//newsletter text
$staticBlock = array(
    'title' => 'Celebrity Newsletter block text',
    'identifier' => 'celebrity_newsletter_text',
    'content' => 'There are many variations of passages of Lorem Ipsum  available, but the majority have suffered alteration in some',
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();
//additional links
$staticBlock = array(
    'title' => 'Celebrity Additional links',
    'identifier' => 'celebrity_additional_links',
    'content' => "<p><a href=\"#\">Customer Assistance</a> <a href=\"#\">Email Us</a> <a href=\"#\">Payment Methods</a> Tel 888-475-7674</p>",
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();
//footer brands
$staticBlock = array(
    'title' => 'Celebrity Footer brands',
    'identifier' => 'celebrity_footer_brands',
    'content' => "<p><a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand1.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand2.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand3.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand4.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand5.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand6.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand7.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand8.gif\"}}\" alt=\"\" /></a> <a href=\"#\"><img src=\"{{media url=\"olegnax/celebrity/brand9.gif\"}}\" alt=\"\" /></a></p>",
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();
//footer brands
$staticBlock = array(
    'title' => 'Celebrity Footer links',
    'identifier' => 'celebrity_footer_links',
    'content' => '<ul class="footer-links">
                <li><span>Company</span>
	                <ul>
		                <li><a href="{{store url="terms"}}">About us</a></li>
		                <li><a href="{{store url="contacts"}}">Careers</a></li>
		                <li><a href="{{store url="technologie"}}">Technologie</a></li>
		                <li><a href="{{store url="terms"}}">FAQ</a></li>
		                <li><a href="{{store url="giftcards"}}">E-Gift Cards</a></li>
	                </ul>
                </li>
                <li><span>Information</span>
	                <ul>
		                <li><a href="{{store url="shipping"}}">Shipping costs</a></li>
		                <li><a href="{{store url="terms"}}">Terms and conditions</a></li>
		                <li><a href="{{store url="imprint"}}">Imprint</a></li>
		                <li><a href="{{store url="security"}}">Security</a></li>
	                </ul>
                </li>
                <li><span>Customer Service</span>
	                <ul>
		                <li><a href="{{store url="contacts"}}">Contact us</a></li>
		                <li><a href="{{store url="contacts"}}">Feedback</a></li>
		                <li><a href="{{store url="terms"}}">Return Policy</a></li>
		                <li><a href="{{store url="shipping"}}">Shipping & Tax</a></li>
		                <li><a href="{{store url="contacts"}}">International</a></li>
	                </ul>
                </li>
                <li><span>Connect with Us</span>
	                <ul class="footer-social">
	                    <li><a class="twitter" target="social" href="http://twitter.com/olegnax">Twitter</a></li>
	                    <li><a class="facebook" target="social" href="http://facebook.com/olegnax">Facebook</a></li>
	                    <li><a class="email" href="mailto:mail@olegnax.com">Email</a></li>
	                    <li><a class="google" target="social" href="http://plus.google.com/u/0/111109476451145065018">Google</a></li>
	                </ul>
                </li>
                </ul>',
    'is_active' => 1,
    'stores' => array(0)
);
Mage::getModel('cms/block')->setData($staticBlock)->save();
}
catch (Exception $e) {
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occurred while installing celebrity theme pages and cms blocks.'));
}