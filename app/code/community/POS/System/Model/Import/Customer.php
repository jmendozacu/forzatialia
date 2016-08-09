<?php

/**
 * class for importing customer.
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
     * initialize import.
     */
    protected function _initImport()
    {
        if (!$this->_initialized) {
            $this->_created = $this->_getConfigValue('created');
            $this->_updated = $this->_getConfigValue('updated');
            $this->_errored = $this->_getConfigValue('errored');
        }

        parent::_initImport();
    }

    /**
     * start import.
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
        try {
            $this->_import_data = $model->getCustomerBulkDetails($last_date);
        } catch (SoapFault $e) {
            $this->_processSoapError($e);
            REX_Logger::log('soap '.$e->getMessage());
            exit;
        }
        $this->_setConfigValue('bulklast', $current_time);

        $this->_saveFile($this->_config_prefix.$current_time.'.xml', $this->_import_data);
        $this->_setConfigValue('file', $this->_config_prefix.$current_time.'.xml');

        if ($this->_result === null) {
            $this->_result = $model->parseCustomersXml($this->_import_data);
        }

        REX_Logger::log(' cb_pos = '.$this->_pos, REX_Logger::TYPE_SYNC);

        $this->_report = [];
        $this->_report['Customers Synchronise'] = '';
        $this->setTotal(sizeof($this->_result['customers']));
    }

    /**
     * Commit import step result.
     */
    protected function _commitImport()
    {
        $this->_setConfigValue('created', $this->_created);
        $this->_setConfigValue('updated', $this->_updated);
        $this->_setConfigValue('errored', $this->_errored);
        parent::_commitImport();
    }

    /**
     * finish import data.
     */
    protected function _finishBulk()
    {
        Mage::helper('retailexpress')->setBulkLastTime('customer', $this->_getConfigValue('bulklast'));

        if (!Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            $this->_report = ['Bulk Synchronisation of Customer Details Completed Successfully' => $this->_updated.' Updated, '.$this->_created.' Created, '.$this->_errored.' Errors.'.($this->_error ? ' <a class="details" href="#" onclick="$(this).next().toggle();return false;">&#x00BB;</a><p style="display: none;">'.$this->_error ? implode(PHP_EOL, $this->_error) : ''.'</p>' : '')];
        }

        parent::_finishBulk();
    }

    /**
     * do import step.
     *
     * @return bool - is finished or not
     */
    protected function _doImport()
    {
        if ($this->getStatus() == POS_System_Model_Task_Status::STATUS_CANCEL) {
            exit();
        }

        $timer = $this->getStartTime();
        $model = Mage::getModel('retailexpress/retail');
        if ($model->getError()) {
            throw new Exception($model->getError());
        }

        $customers_count = 0;
        $finished = true;
        if ($this->_result['customers']) {
            foreach ($this->_result['customers'] as $c) {
                if (++$customers_count <= $this->_pos) {
                    continue;
                }

                try {
                    $_t = Mage::helper('retailexpress')->updateMagentoCustomer($c);
                    if ($_t['new']) {
                        ++$this->_created;
                    } else {
                        ++$this->_updated;
                    }
                    $this->_report['Customers Synchronise'] .= $_t['str'];
                } catch (Exception $e) {
                    ++$this->_errored;
                    $this->_report['Customers Synchronise'] .= $e->getMessage().PHP_EOL;
                    $this->_error[] = $e->getMessage();
                }

                if (($customers_count % 10) == 0) {
                    $this->_pos = $customers_count;
                    $this->_setConfigValue('pos', $customers_count);
                    if (time() > ($timer + Mage::getSingleton('retailexpress/config')->getConfigBulkTime() || $this->getStatus() == POS_System_Model_Task_Status::STATUS_CANCEL)) {
                        $finished = false;
                        break;
                    }
                }
            }
        }

        $this->_pos = $customers_count;

        return $finished;
    }

    protected function _parseXml()
    {
        if ($this->_result === null) {
            $model = Mage::getModel('retailexpress/retail');
            if ($model->getError()) {
                throw new Exception($model->getError());
            }
            $this->_import_data = $this->_readFile($this->_getConfigValue('file'));
            $this->_result = $model->parseCustomersXml($this->_import_data);
            $model = null;
            unset($model);
        }
    }
}
