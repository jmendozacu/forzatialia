<?php

/**
 * abstract import file.
 */
abstract class POS_System_Model_Import_Abstract extends Mage_Core_Model_Abstract
{
    const CONFIG_SUFFIX_STATUS = 'status';
    const CONFIG_SUFFIX_TOTAL = 'total';
    const CONFIG_SUFFIX_WAIT = 'wait';

    /**
     * @var string - prefix for config names
     */
    protected $_config_prefix = null;

    /**
     * @var array - report array
     */
    protected $_report = null;

    /**
     * @var string - import data
     */
    protected $_import_data = '';

    /**
     * @var string - error
     */
    protected $_error = '';

    /**
     * @var bool already init or not
     */
    protected $_initialized = false;

    /**
     * @var int position on import
     */
    protected $_pos = 0;

    /**
     * @var array config values
     */
    protected $_config = [];

    /** @var POS_System_Helper_Data */
    protected $_helper;

    protected function _construct()
    {
        $this->setData('start_time', time());
        $this->_helper = Mage::helper('retailexpress');
        $this->_configModel = Mage::getModel('retailexpress/conf');
    }

    /**
     * Schedule bulk sync (change status).
     */
    public function schedule()
    {
        switch ($this->getStatus()) {
            case POS_System_Model_Task_Status::STATUS_WAIT:
                REX_Logger::log('Scheduled task '.Mage::app()->getLocale()->date(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_SCHEDULE);
                break;
            case POS_System_Model_Task_Status::STATUS_CANCEL:
                REX_Logger::log('Scheduled task '.Mage::app()->getLocale()->date(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_SCHEDULE);
                break;
            case POS_System_Model_Task_Status::STATUS_SCHEDULE:
                break;
            case POS_System_Model_Task_Status::STATUS_INPROGRESS:
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_INPROSHE);
                break;
            case POS_System_Model_Task_Status::STATUS_INPROSHE:
                break;
            case POS_System_Model_Task_Status::STATUS_DONE:
                REX_Logger::log('Scheduled task '.Mage::app()->getLocale()->date(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_SCHEDULE);
                break;
            default:
                REX_Logger::log('Scheduled task '.Mage::app()->getLocale()->date(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_SCHEDULE);
                break;
        }
    }

    /**
     * Cancel bulk sync (change status).
     *
     * @return bool - canceled or not
     */
    public function cancel()
    {
        switch ($this->getStatus()) {
            case POS_System_Model_Task_Status::STATUS_WAIT:
            case POS_System_Model_Task_Status::STATUS_SCHEDULE:
            case POS_System_Model_Task_Status::STATUS_INPROGRESS:
            case POS_System_Model_Task_Status::STATUS_INPROSHE:
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_CANCEL);
                $this->_clearBulk(true);
                $this->_setConfigValue('wait', 0);

                return true;
                break;
        }

        return false;
    }

    /**
     * Schedule bulk sync with waiting.
     *
     * @param $counter int - iterations wait
     */
    public function wait($counter = 0)
    {
        switch ($this->getStatus()) {
            case POS_System_Model_Task_Status::STATUS_WAIT:
                break;
            case POS_System_Model_Task_Status::STATUS_SCHEDULE:
            case POS_System_Model_Task_Status::STATUS_INPROGRESS:
            case POS_System_Model_Task_Status::STATUS_INPROSHE:
                return;
                break;
            case POS_System_Model_Task_Status::STATUS_DONE:
            case POS_System_Model_Task_Status::STATUS_CANCEL:
            default:
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_WAIT);
                break;
        }

        if ($counter <= 0) {
            $this->schedule();
        }

        $this->_setConfigValue('wait', $counter);
    }

    /**
     * make on step for bulk synchronization.
     */
    public function import()
    {
        set_time_limit(Mage::getSingleton('retailexpress/config')->getMaxExecutionTime());
        ini_set('memory_limit', Mage::getSingleton('retailexpress/config')->getMaxMemoryConsume().'M');

        $logType = $this->_config_prefix == 'pb_' ? REX_Logger::CAT_PRODUCTS : REX_Logger::CAT_CUSTOMERS;

        if (REX_Singlethreader::isLocked()) {
            REX_Logger::log('Cannot run a new import while another process is locking a '.$logType.' import.');

            return;
        }

        REX_Singlethreader::locked(function () use ($logType) {
            REX_Logger::log('Start '.$logType.' task '.Mage::app()->getLocale()->date(), REX_Logger::TYPE_SYNC, $logType);

            switch ($this->getStatus()) {
                case POS_System_Model_Task_Status::STATUS_WAIT:
                    $this->wait($this->_getConfigValue('wait') - 1);
                    REX_Logger::log('wait '.$this->_getConfigValue('wait'), REX_Logger::TYPE_SYNC, $logType);
                    break;
                case POS_System_Model_Task_Status::STATUS_SCHEDULE:
                    try {
                        $this->_startBulk();
                    } catch (Exception $e) {
                        $this->_errorBulk($e->getMessage());
                        REX_Logger::log('Error preparing: '.$e->getMessage().PHP_EOL.$e->getFile().':'.$e->getLine().PHP_EOL.$e->getTraceAsString().PHP_EOL.'Time left '.(time() - $this->getStartTime()).' sec.', REX_Logger::TYPE_ERROR, $logType);
                    }
                    break;
                case POS_System_Model_Task_Status::STATUS_INPROGRESS:
                case POS_System_Model_Task_Status::STATUS_INPROSHE:
                    Varien_Profiler::start('REX::process_bulk');
                    try {
                        $this->_processBulk();
                    } catch (Exception $e) {
                        $this->_errorBulk($e->getMessage());
                        REX_Logger::log('Error processing: '.$e->getMessage().PHP_EOL.$e->getFile().':'.$e->getLine().PHP_EOL.$e->getTraceAsString().PHP_EOL.'Time left '.(time() - $this->getStartTime()).' sec.', REX_Logger::TYPE_ERROR, $logType);
                    }
                    Varien_Profiler::stop('REX::process_bulk');
                    break;
                default:
                    REX_Logger::log('No '.$logType.' to synchronise.', REX_Logger::TYPE_SYNC, $logType);
                    break;
            }
            REX_Logger::log('End. Time left '.(time() - $this->getStartTime()).' sec.', REX_Logger::TYPE_SYNC, $logType);
            REX_Profiler::logAll();
        });
    }

    /**
     * start import data.
     *
     * @abstract
     */
    abstract protected function _startImport();

    /**
     * do import step.
     *
     * @abstract
     */
    abstract protected function _doImport();

    abstract protected function _parseXml();

    protected function _safeDelete($file, $directory = null)
    {
        if ($directory === null) {
            $directory = Mage::getBaseDir('var').DS.'retail'.DS;
        }

        if (file_exists($directory.$file)) {
            @unlink($directory.$file);
        }
    }

    /**
     * first iteration for product bulk method.
     */
    protected function _startBulk()
    {
        $this->_setStatus(POS_System_Model_Task_Status::STATUS_INPROGRESS);
        $this->_setConfigValue('pid', getmypid());
        $this->_setConfigValue('pos', '0');
        $this->_setConfigValue('lasttime', '');
        $this->_setConfigValue('created', '0');
        $this->_setConfigValue('created_conf', '0');
        $this->_setConfigValue('updated', '0');
        $this->_setConfigValue('updated_conf', '0');
        $this->_setConfigValue('errored', '0');

        Varien_Profiler::start('REX::download_xml');
        $this->_startImport();
        Varien_Profiler::stop('REX::download_xml');

        Varien_Profiler::start('REX::save_work_files');
        $this->_saveFile($this->_config_prefix.'report', serialize($this->_report));
        $this->_saveFile($this->_config_prefix.'error', serialize($this->_error));
        Varien_Profiler::stop('REX::save_work_files');

        $this->_initialized = true;
        Varien_Profiler::start('REX::process_bulk');
        $this->_processBulk();
        Varien_Profiler::start('REX::process_bulk');
    }

    /**
     * Process error during bulk import.
     *
     * @param string $message - Error message
     */
    protected function _errorBulk($message)
    {
        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            [
                'type' => 'Cron',
                'comment' => 'Error: '.$message,
            ]
        );
        $history->save();
        $this->_clearBulk();
    }

    /**
     * finish bulk import.
     */
    protected function _finishBulk()
    {
        $comment = [];
        foreach ($this->_report as $k => $v) {
            $comment[] = $k.': '.PHP_EOL.$v;
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            [
                'type' => 'Cron',
                'comment' => implode(PHP_EOL, $comment),
            ]
        );
        $history->save();
        $this->_setStatus(POS_System_Model_Task_Status::STATUS_DONE);
        REX_Logger::log('Import finished '.Mage::app()->getLocale()->date(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);

        $this->_clearBulk(true);
    }
    /**
     * cancel bulk import.
     */
    protected function _cancelBulk($counter = null)
    {
        if ($this->_report !== null) {
            $comment = [];
            foreach ($this->_report as $k => $v) {
                $comment[] = $k.': '.$v;
            }

            $history = Mage::getModel('retailexpress/history');
            $history->setData(
                [
                    'type' => 'Cron',
                    'comment' => implode(PHP_EOL, $comment),
                ]
            );
            $history->save();
        }
        $this->_setStatus(POS_System_Model_Task_Status::STATUS_CANCEL);
        REX_Logger::log('Cancel task '.Mage::app()->getLocale()->date(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);

        if ($counter !== null) {
            $this->wait($counter);
        }

        $this->_clearBulk(true);
    }

    /**
     * clearing all temporary data for bulk import.
     */
    protected function _clearBulk($cleanFile = false)
    {
        if ($cleanFile === true) {
            $this->_setConfigValue('pos', '');
            $this->_cleanFile();
            $this->_safeDelete($this->_config_prefix.'report');
            $this->_safeDelete($this->_config_prefix.'error');
        }

        $this->_setConfigValue('created', '0');
        $this->_setConfigValue('created_conf', '0');
        $this->_setConfigValue('updated', '0');
        $this->_setConfigValue('updated_conf', '0');
        $this->_setConfigValue('errored', '0');

        $this->_setConfigValue('pid', '');
        $this->_setConfigValue('lasttime', now());
    }

    protected function _cleanFile()
    {
        $fileName = $this->_getConfigValue('file');
        if (empty($fileName) || !file_exists(Mage::getBaseDir('var').DS.'retail'.DS.$fileName)) {
            return;
        }
        $this->_safeDelete($fileName);
        $this->_setConfigValue('file', '');
    }

    /**
     * initialize import.
     */
    protected function _initImport()
    {
        if ($this->_initialized) {
            return;
        }

        $this->_setConfigValue('pid', getmypid());

        $this->_pos = $this->_getConfigValue('pos');
        $this->_result = $this->_readFile($this->_config_prefix.'result', false);
        $this->_report = $this->_readFile($this->_config_prefix.'report', false);
        $this->_error = $this->_readFile($this->_config_prefix.'error', false);
        $this->_parseXml();
        if (!$this->_getConfigValue('total')) {
            $this->setTotal(sizeof($this->_result['products']));
        }

        $this->_initialized = true;
    }

    /**
     * Commit import step result.
     */
    protected function _commitImport()
    {
        $this->_setConfigValue('pos', $this->_pos);
        $this->_saveFile($this->_config_prefix.'report', serialize($this->_report));
        $this->_saveFile($this->_config_prefix.'error', serialize($this->_error));
    }

    protected function _saveFile($fileName, $content = null)
    {
        if ($content === null) {
            return false;
        }
        $path = Mage::getBaseDir('var').DS.'retail'.DS.$fileName;
        $fd = fopen($path, 'wb+');
        if (!$fd) {
            throw new Exception('Cannot open file '.$path);
        }

        flock($fd, LOCK_EX);

        if (fwrite($fd, $content) === false) {
            flock($fd, LOCK_UN);
            fclose($fd);
            throw new Exception('Cannot write to file '.$path);
        } else {
            flock($fd, LOCK_UN);
            fclose($fd);
            chmod($path, 0666);
        }

        return true;
    }

    /**
     * Read file and autounserialize the content.
     *
     * @param string $fileName
     * @param bool   $absolutePath
     * @param bool   $throwException
     *
     * @return null|mixed
     *
     * @throws Exception
     */
    protected function _readFile($fileName, $throwException = true)
    {
        $path = Mage::getBaseDir('var').DS.'retail'.DS.$fileName;
        if (!file_exists($path)) {
            if ($throwException) {
                throw new Exception('There is no file '.$path);
            }
        } else {
            $content = file_get_contents(trim($path));

            if ($this->_helper->isSerialized($content)) {
                return unserialize($content);
            } else {
                return $content;
            }
        }

        return;
    }

    /**
     * process each step of cron iteration import.
     */
    protected function _processBulk()
    {
        if ($this->_getConfigValue('file') == '') {
            $this->_error[] = 'Operation of download XML file was failed.';
            $this->_setStatus(POS_System_Model_Task_Status::STATUS_CANCEL);
            $this->_clearBulk(true);
            REX_Logger::log('File did not downloaded.', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);

            return;
        }

        Varien_Profiler::start('REX::init_import');
        $this->_initImport();
        Varien_Profiler::stop('REX::init_import');

        if (getmypid() !== $this->_getConfigValue('pid')) {
            REX_Logger::log('Exit. PID not equal to initialized.', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);

            return;
        }

        Varien_Profiler::start('REX::do_import');
        $finished = $this->_doImport();
        Varien_Profiler::stop('REX::do_import');

        Varien_Profiler::start('REX::commit_import');
        $this->_commitImport();
        Varien_Profiler::stop('REX::commit_import');
        if ($finished) {
            Varien_Profiler::start('REX::finish_bulk');
            $this->_finishBulk();
            Varien_Profiler::stop('REX::finish_bulk');
        }
    }

    /**
     * @param $e
     * @param $matched
     */
    protected function _processSoapError($e)
    {
        $email = Mage::getStoreConfig('retailexpress/main/email_log');
        if ($email) {
            if (preg_match('/called[\s]every[\s]([0-9]+)[\s]minutes?\.[\s]+please[\s]wait[\s]([0-9]+)[\s]minutes?/ui', $e->getMessage(), $matched)) {
                $delay = 12;
                if ($matched[2] > 0) {
                    $delay = $matched[2] % 5 == 0 ? $matched[2] / 5 : (ceil($matched[2] / 10) * 10 + 5) / 5;
                }

                $this->_report = ['Synchronisation was postponed' => 'Server rejected the request cause it requires large server resources and can only be called every '.$matched[1].' minutes.'];
                $this->_cancelBulk($delay);
            } else {
                $mail = new Zend_Mail();
                $body =
                    'Store: '.Mage::getStoreConfig('general/store_information/name').PHP_EOL.
                    'URL: '.Mage::getStoreConfig('web/unsecure/base_url').PHP_EOL.
                    'Error body: '.PHP_EOL.
                    $e->getMessage();
                $mail->setBodyText($body);
                $mail->setFrom(Mage::getStoreConfig('trans_email/ident_general/name'), Mage::getStoreConfig('trans_email/ident_general/email'));
                $mail->addTo($email, '');
                $mail->setSubject('REX AutoSupport. SOAP error was thrown.');
                $mail->send();
                $this->_report = ['SoapFault' => $e->getMessage()];
                $this->_cancelBulk();
            }
        }
    }

    public function getWait()
    {
        return $this->_getConfigValue(self::CONFIG_SUFFIX_WAIT);
    }

    /**
     * get status of bulk method.
     *
     * @return int status ot product bulk sync
     */
    public function getStatus()
    {
        return $this->_getConfigValue(self::CONFIG_SUFFIX_STATUS);
    }

    /**
     * set status of bulk method.
     *
     * @param $value int - status of bulk import
     */
    protected function _setStatus($value)
    {
        $this->_setConfigValue(self::CONFIG_SUFFIX_STATUS, $value);
    }

    /**
     * get total items of bulk method.
     *
     * @return int status ot product bulk sync
     */
    public function getTotal()
    {
        return $this->_getConfigValue(self::CONFIG_SUFFIX_TOTAL);
    }

    /**
     * get position of bulk method.
     *
     * @return int status ot product bulk sync
     */
    public function getPosition()
    {
        return $this->_getConfigValue('pos');
    }

    /**
     * get last time of bulk method.
     *
     * @return int status ot product bulk sync
     */
    public function getLastTime()
    {
        return $this->_getConfigValue('lasttime');
    }

    /**
     * set total items of bulk method.
     *
     * @param $value int - items of bulk import
     */
    public function setTotal($value)
    {
        $this->_setConfigValue(self::CONFIG_SUFFIX_TOTAL, $value);
    }

    /**
     * Getting internal config value for import.
     *
     * @param string $name - name of internal config
     *
     * @return string|null - value of the config
     */
    protected function _getConfigValue($name)
    {
        if (!isset($this->_config[$name])) {
            $return = $this->_configModel->load($this->_config_prefix.$name)->getValue();
            $this->_config[$name] = trim($return);
        }

        return $this->_config[$name];
    }

    /**
     * Setting internal config value for import.
     *
     * @param $name string - name of config
     * @param $value string - value of config
     */
    protected function _setConfigValue($name, $value)
    {
        $this->_configModel->load($this->_config_prefix.$name)
            ->setConfId($this->_config_prefix.$name)
            ->setValue($value)
            ->save();
        $this->_config[$name] = $value;
    }
}
