<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Validator for check input type value
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator extends Zend_Validate_InArray
{

    /**
     * Construct
     */
    public function __construct()
    {
        //set data haystack
        /** @var $model Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype */
        $model = Mage::getSingleton('eav/adminhtml_system_config_source_inputtype');
        foreach ($model->toOptionArray() as $item) {
            $this->_haystack[] = $item['value'];
        }

        $this->_strict = true;

        //reset message template and set custom
        $this->_messageTemplates = null;
        $this->_initMessageTemplates();
    }

    /**
     * Initialize message templates with translating
     *
     * @return Mage_Adminhtml_Model_Core_File_Validator_SavePath_Available
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = array(
                self::NOT_IN_ARRAY => Mage::helper('core')->__('Input type "%value%" not found in the input types list.'),
            );
        }
        return $this;
    }
}
