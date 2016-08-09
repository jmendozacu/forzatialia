<?php
/**
 * Magento Booster 1.4+
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpagecache
 * @version      4.0.5
 * @license:     AACcewAJ3nZYMUsItZcwugZ3g4HsbQPMHWb0Pv6oyc
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpagecache_Model_Observer_Emails extends Mage_Core_Model_Abstract
{
    
    public function prepare()
    {
        $filename = Mage::getBaseDir().DS.Aitoc_Aitpagecache_Monitor::TMP_PATH.DS.Aitoc_Aitpagecache_Monitor::TMP_EMAIL_FILE;
        if(file_exists($filename))
        {
            $emails = explode('|',file_get_contents($filename));
            $emails = array_unique($emails);
            foreach($emails as $email)
            {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
                {
                    $model = Mage::getModel('aitpagecache/emails');
                    $model->setCustomerEmail($email);
                    $model->save();
                }
            }
            unlink($filename);
        }
    }
    
    public function send()
    {
        if(Mage::helper('aitpagecache')->getMonitor() && !(Mage::helper('aitpagecache')->getMonitor()->getLoadLevel()>1))
        {
            $collection = Mage::getModel('aitpagecache/emails')->getCollection();
            $collection->getSelect()->where('sent_at IS NULL');
            $date = date('Y-m-d H:i:s');
            $vars = array(
                'url'       => Mage::getBaseUrl(),
                'storename' => Mage::getStoreConfig('general/store_information/name'),
            );
            $emails = array();
            $template = Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_allow_discount')?Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_email_template_discount'):Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_email_template');
            foreach($collection as $item)
            {
                $ruleId = 0;
                $vars['coupon'] = '';
                if(!in_array($item->getCustomerEmail(), $emails))
                {
                    $emails[] = $item->getCustomerEmail();
                    if(Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_allow_discount'))
                    {
                        $coupon = $this->_createCoupon($item->getEmailId());
                        if(isset($coupon['coupon_code']) && $coupon['coupon_code'])
                        {
                            $ruleId = $coupon['coupon_id'];
                            $vars['coupon'] = $coupon['coupon_code'];
                        }
                    }

                    $this->_sendEmail($item->getCustomerEmail(),$template, $vars);
                    $item
                        ->setSentAt($date)
                        ->setRuleId($ruleId)
                        ->save();
                }
                else
                {
                    $item->delete();
                }
            }
        }
        return $this;
    }

    private function _sendEmail($to,$template,$vars)
    {
        $to = trim($to);
        if(!$to)
        {
            return;
        }
        $emailModel = Mage::getModel('core/email_template');
        $emailModel->sendTransactional(
            $template,
            Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_identity'),
            $to,
            null,
            $vars
        );
    }


    protected function _createCoupon($id)
    {
        $couponData = array();
        $couponData['name']      = 'Block #' . $id;
        $couponData['is_active'] = 1;
        $websites = Mage::getModel('core/website')->getCollection();
        $websiteIds = array();
        foreach($websites as $website)
        {
            $websiteIds[] = $website->getWebsiteId();
        }
        $couponData['website_ids'] = $websiteIds;
        $couponData['coupon_code'] = strtoupper($id . uniqid()); // todo check for uniq in DB
        $couponData['uses_per_coupon'] = 1;
        $couponData['uses_per_customer'] = 1;
        $couponData['from_date'] = ''; //current date

        $days = Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_discount_days');
        $date = date('Y-m-d', Mage::getModel('core/date')->timestamp(time() + $days*24*3600));
        $couponData['to_date'] = $date;

        $couponData['uses_per_customer'] = 1;
        $couponData['simple_action']   = Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_discount_type');
        $couponData['discount_amount'] = Mage::getStoreConfig('aitpagecache/aitpagecache_config_aitloadmon/block_discount_amount');
        $couponData['conditions'] = array(
            1 => array(
                'type'       => 'salesrule/rule_condition_combine',
                'aggregator' => 'all',
                'value'      => 1,
                'new_child'  =>'',
            )
        );

        $couponData['actions'] = array(
            1 => array(
                'type'       => 'salesrule/rule_condition_product_combine',
                'aggregator' => 'all',
                'value'      => 1,
                'new_child'  =>'',
            )
        );

        //create for all customer groups
        $couponData['customer_group_ids'] = array();

        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load();

        $found = false;
        foreach ($customerGroups as $group) {
            if (0 == $group->getId()) {
                $found = true;
            }
            $couponData['customer_group_ids'][] = $group->getId();
        }
        if (!$found) {
            $couponData['customer_group_ids'][] = 0;
        }

        if(!version_compare(Mage::getVersion(), '1.4.1.0', '<'))
        {
            $couponData['coupon_type'] = 2; // Need to use coupon code - fix for 1.4.1.0
        }
        $salesRuleModel = Mage::getModel('salesrule/rule');
        try {
            $salesRuleModel
                ->loadPost($couponData)
                ->save();
            $couponData['coupon_id'] = $salesRuleModel->getId();
        }
        catch (Exception $e){
            //print_r($e); exit;
            $couponData['coupon_code'] = '';
            $couponData['coupon_id'] = '';
        }

        return $couponData;

    }
}