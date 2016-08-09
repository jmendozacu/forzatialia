<?php

class POS_System_Block_Adminhtml_Viewsynclog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_log';
        $this->_blockGroup = 'retailexpress';
        parent::__construct();
        $this->setTemplate('retailexpress/viewsynclogxml.phtml');
        $this->removeButton('add');
    }

    /**
     * get Sync Logs.
     *
     *
     * Gets the request parameters and loads the XML logs from the database.
     *
     * @return String
     */
    public function getSyncLogs()
    {
        //get data from URL parameters
        $xmlType = Mage::app()->getRequest()->getParam('xmltype');

        $syncId = Mage::app()->getRequest()->getParam('id');

        $model = Mage::getModel('retailexpress/log')->load($syncId);

        return $this->formatXmlString($model->$xmlType);
    }

    /**
     * format XML String.
     *
     *
     * Method that formats the XML output string and fixed the xml spaces and new lines.
     *
     * @return String
     */
    public function formatXmlString($xml)
    {
        $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
        $token = strtok($xml, "\n");
        $result = '';
        $pad = 0;
        $matches = [];
        while ($token !== false) :
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
              $indent = 0; elseif (preg_match('/^<\/\w/', $token, $matches)) :
              $pad--;
        $indent = 0; elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
              $indent = 1; else :
              $indent = 0;
        endif;
        $line = str_pad($token, strlen($token) + $pad, ' ', STR_PAD_LEFT);
        $result .= $line."\n";
        $token = strtok("\n");
        $pad    += $indent;
        endwhile;

        return $result;
    }
}
