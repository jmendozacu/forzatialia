<?php

class REX_RetailSoapZipException extends Exception
{
    protected $xml;

    public function getXml()
    {
        return $this->xml;
    }

    public function __construct($xml = null)
    {
        parent::__construct('', 0);
        $this->xml = $xml;
    }
}
