<?php

/**
 * class for importing customer
 */

class POS_System_Model_Import_Customer extends POS_System_Model_Import_Abstract
{

    /**
     * @var string - prefix for config names
     */
    protected $_config_prefix = 'cb_';

    protected $_created = 0;
    protected $_updated = 0;
    protected $_errored = 0;

    /**
     * @var array of products
     */
    protected $_result = null;

    /**
     * initialize import
     *
     * @return null
     */
    protected function _initImport()
    {
        if (!$this->_is_init) {
            $this->_created = $this->_getConfigValue('mcs');
            $this->_updated = $this->_getConfigValue('mus');
            $this->_errored = $this->_getConfigValue('me');
        }

        parent::_initImport();
    }

    /**
     * start import
     *
     * @abstract
     */
    protected function _startImport()
    {
        $model = Mage::getModel('retailexpress/retail');
		if ($model->getError()) {
			throw new Exception($model->getError());
		}

        $last_date = Mage::helper('retailexpress')->getBulkLastTime('customer');
        $current_time = time();
		$this->_import_data = $model->getCustomerBulkDetails($last_date);
        $this->_setConfigValue('bulklast', $current_time);
        $data = $model->parseCustomersXML($this->_import_data);
        $this->_report = array();
        $this->_report["Customers Synchronise"] = '';
        $this->setTotal(count($data['customers']));
    }

    /**
     * Commit import step result
     *
     * @return void
     */
    protected function _commitImport()
    {
        parent::_commitImport();

        $this->_setConfigValue('mcs', $this->_created);
        $this->_setConfigValue('mus', $this->_updated);
        $this->_setConfigValue('me', $this->_errored);
    }

    /**
     * finish import data
     */
    protected function _finishBulk()
    {
        $created = $this->_getConfigValue('mcs');
        $updated = $this->_getConfigValue('mus');
        $errored = $this->_getConfigValue('me');
        Mage::helper('retailexpress')->setBulkLastTime('customer', $this->_getConfigValue('bulklast'));

        if (!POS_System_Model_Config::DEBUG_IN_HISTORY) {
            $this->_report = array('Bulk Synchronisation of Customer Details Completed Successfully' => $updated . ' Updated, ' . $created . ' Created, ' . $errored . ' Errors.' . "\n" . $this->_error);
        }

        return parent::_finishBulk();
    }

    /**
     * do import step
     *
     * @return bool - is finished or not
     */
    protected function _doImport()
    {
        $timer = $this->getStartTime();
        $model = Mage::getModel('retailexpress/retail');
		if ($model->getError()) {
			throw new Exception($model->getError());
		}

        $this->_result = $model->parseCustomersXML($this->_import_data);

        $customers_count = 0;
        $finished = true;
        if ($this->_result['customers']) {
            foreach ($this->_result['customers'] as $c) {
                $customers_count++;
                if ($customers_count <= $this->_pos) {
                    continue;
                }

                try {
                    $_t = Mage::helper('retailexpress')->updateMagentoCustomer($c);
                    if ($_t['new']) {
                        $this->_created++;
                    } else {
                        $this->_updated++;
                    }
                    $this->_report["Customers Synchronise"] .= $_t['str'];
                } catch (Exception $e) {
                    $this->_errored++;
                    $this->_report["Customers Synchronise"] .= $e->getMessage();
                    $this->_error .= $e->getMessage();
                }

                if (($customers_count % 10) == 0) {
                    $this->_setConfigValue('pos', $customers_count);
                    if (time() > ($timer + POS_System_Model_Config::CONFIG_BULK_TIME)) {
                        $finished = false;
                        break;
                    }
                }
            }
        }

        $this->_pos = $customers_count;
        return $finished;
    }

}
 
