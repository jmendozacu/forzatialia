<?php

/**
 * abstract import file
 */
abstract class POS_System_Model_Import_Abstract extends Mage_Core_Model_Abstract
{

    const CONFIG_SUFFIX_STATUS = 'status';
    const CONFIG_SUFFIX_TOTAL  = 'total';

    /**
     * @var string - prefix for config names
     */
    protected $_config_prefix = null;

    /**
     * @var $_report array - report array
     */
    protected $_report = null;

    /**
     * @var $_import_data string - import data
     */
    protected $_import_data = '';

    /**
     * @var $_error string - error
     */
    protected $_error = '';

    /**
     * @var bool already init or not
     */
    protected $_is_init = false;

    /**
     * @var int position on import
     */
    protected $_pos = 0;

    /**
     * @var array config values
     */
    protected $_config = array();

    /**
     * Schedule bulk sync (change status)
     *
     * @return void
     */
    public function schedule()
    {
        switch ($this->getStatus()) {
            case POS_System_Model_Task_Status::STATUS_WAIT:
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_SCHEDULE);
                break;
            case POS_System_Model_Task_Status::STATUS_CANCEL:
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
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_SCHEDULE);
                break;
            default:
                $this->_setStatus(POS_System_Model_Task_Status::STATUS_SCHEDULE);
                break;
        }
    }

    /**
     * Cancel bulk sync (change status)
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
                if ($this->_getConfigValue('file')) {
                    @unlink($this->_getConfigValue('file'));
                    $this->_setConfigValue('file', '');
                }

                return true;
                break;
        }

        return false;
    }

    /**
     * Schedule bulk sync with waiting
     *
     * @param $counter int - iterations wait
     * @return void
     */
    public function wait($counter)
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
     * make on step for bulk synchronization
     * @return void
     */
    public function import()
    {
        try {
            // making start time
            if (!$this->getStartTime()) {
                $this->setStartTime(time());
            }

            switch ($this->getStatus()) {
                case POS_System_Model_Task_Status::STATUS_WAIT:
                    $this->wait($this->_getConfigValue('wait') - 1);
                    break;
                case POS_System_Model_Task_Status::STATUS_SCHEDULE:
                    $this->_startBulk();
                    break;
                case POS_System_Model_Task_Status::STATUS_INPROGRESS:
                case POS_System_Model_Task_Status::STATUS_INPROSHE:
                    $this->_processBulk();
                    break;
            }
        } catch (Exception $e) {
            $this->_errorBulk($e->getMessage());
        }
    }

    /**
     * start import data
     *
     * @abstract
     */
    abstract protected function _startImport();

    /**
     * do import step
     *
     * @abstract
     */
    abstract protected function _doImport();

    /**
     * first iteration for product bulk method
     * @return void
     */
    protected function _startBulk()
    {
        $this->_setStatus(POS_System_Model_Task_Status::STATUS_INPROGRESS);
        $this->_setConfigValue('pid', getmypid());
        $this->_startImport();
        $path =  Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . time() . ".xml";
        $fd = fopen($path, "wb+");
        if (!$fd) {
            throw new Exception('Cannot open file ' . $path);
        }

        chmod($path, 0666);
        if (!fwrite($fd, $this->_import_data)) {
            fclose($fd);
            throw new Exception('Cannot write to file ' . $path);
        }

        fclose($fd);
        $this->_setConfigValue('file', $path);
        $this->_setConfigValue('pos', '0');
        $this->_setConfigValue('lasttime', '');

        // make report file
        $path =  Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . "report";
        $fd = fopen($path, "wb+");
        chmod($path, 0666);
        fwrite($fd, serialize($this->_report));
        fclose($fd);
        // make error file
        $path = Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . "error";
        $fd = fopen($path, "wb+");
        chmod($path, 0666);
        fclose($fd);
        $this->_is_init = true;
        $this->_processBulk();
    }

    /**
     * Process error during bulk import
     *
     * @param string $message - Error message
     * @return void
     */
    protected function _errorBulk($message)
    {
        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Cron'
                , 'comment' => 'Error: ' . $message
            )
        );
        $history->save();
        $this->_clearBulk();
    }

    /**
     * finish bulk import
     *
     * @return void
     */
    protected function _finishBulk()
    {
        $comment = '';
        foreach ($this->_report as $k => $v) {
            $comment .= $k . ":\n" . $v . "\n\n";
        }

        $history = Mage::getModel('retailexpress/history');
        $history->setData(
            array(
                  'type' => 'Cron'
                , 'comment' => $comment
            )
        );
        $history->save();
        $this->_clearBulk();
    }

    /**
     * clearing all temporary data for bulk import
     *
     * @return void
     */
    protected function _clearBulk()
    {
        if ($this->_getConfigValue('file')) {
            @unlink($this->_getConfigValue('file'));
            $this->_setConfigValue('file', '');
        }

        @unlink(Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . "report");
        @unlink(Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . "error");
        $this->_setConfigValue('pos', '');
        $this->_setConfigValue('pid', '');
        $this->_setConfigValue('lasttime', now());
        $this->_setStatus(POS_System_Model_Task_Status::STATUS_DONE);
    }

    /**
     * initialize import
     *
     * @return void
     */
    protected function _initImport()
    {
        if ($this->_is_init) {
            return;
        }

        $this->_setConfigValue('pid', getmypid());
        $path =  $this->_getConfigValue('file');
        $this->_import_data = file_get_contents($path);
        if (!$this->_import_data) {
            throw new Exception('Cannot open file: ' . $path);
        }

        $path =  Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . "report";
        $this->_report = unserialize(file_get_contents($path));
        $path =  Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . "error";
        $this->_error = file_get_contents($path);
        $this->_pos = $this->_getConfigValue('pos');
        $this->_is_init = true;
    }

    /**
     * Commit import step result
     *
     * @return void
     */
    protected function _commitImport()
    {
        // make report file
        $path = Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . "report";
        $fd = fopen($path, "wb+");
        fwrite($fd, serialize($this->_report));
        fclose($fd);
        // make error file
        $path = Mage::getBaseDir('var') . DS . 'retail' . DS . $this->_config_prefix . "error";
        $fd = fopen($path, "wb+");
        fwrite($fd, $this->_error);
        fclose($fd);

        $this->_setConfigValue('pos', $this->_pos);
    }

    /**
     * process each step of cron iteration import
     * 
     * @return void
     */
    protected function _processBulk()
    {
        if ($this->_getConfigValue('pid') && (getmypid() != $this->_getConfigValue('pid')) && function_exists("posix_getsid") && (posix_getsid($this->_getConfigValue('pid')) !== false)) {
            return;
        }

        $this->_initImport();
        $finished = $this->_doImport();
        $this->_setConfigValue('pid', "");
        $this->_commitImport();
        if ($finished) {
            $this->_finishBulk();
        }
    }

    /**
     * get status of bulk method
     *
     * @return int status ot product bulk sync
     */
    public function getStatus()
    {
        return $this->_getConfigValue(self::CONFIG_SUFFIX_STATUS);
    }

    /**
     * set status of bulk method
     *
     * @param $value int - status of bulk import
     */
    protected function _setStatus($value)
    {
        $this->_setConfigValue(self::CONFIG_SUFFIX_STATUS, $value);
    }

    /**
     * get total items of bulk method
     *
     * @return int status ot product bulk sync
     */
    public function getTotal()
    {
        return $this->_getConfigValue(self::CONFIG_SUFFIX_TOTAL);
    }

    /**
     * get position of bulk method
     *
     * @return int status ot product bulk sync
     */
    public function getPosition()
    {
        return $this->_getConfigValue('pos');
    }

    /**
     * get last time of bulk method
     *
     * @return int status ot product bulk sync
     */
    public function getLastTime()
    {
        return $this->_getConfigValue('lasttime');
    }

    /**
     * set total items of bulk method
     *
     * @param $value int - items of bulk import
     */
    public function setTotal($value)
    {
        $this->_setConfigValue(self::CONFIG_SUFFIX_TOTAL, $value);
    }

    /**
     * Getting internal config value for import
     *
     * @param string $name - name of internal config
     * @return string|null - value of the config
     */
    protected function _getConfigValue($name)
    {
        if (!isset($this->_config[$name])) {
            $return = Mage::getModel('retailexpress/conf')->load($this->_config_prefix . $name)->getValue();
            $this->_config[$name] = $return;
        }

        return $this->_config[$name];
    }

    /**
     * Setting internal config value for import
     *
     * @param $name string - name of config
     * @param $value string - value of config
     * @return void
     */
    protected function _setConfigValue($name, $value)
    {
        Mage::getModel('retailexpress/conf')->load($this->_config_prefix . $name)
            ->setConfId($this->_config_prefix . $name)
            ->setValue($value)
            ->save();
        $this->_config[$name] = $value;
    }

}
