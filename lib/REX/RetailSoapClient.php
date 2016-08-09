<?php

class REX_RetailSoapClient extends SoapClient
{
    /**
     * @var bool is REX should return zip archive, no XML
     */
    protected $is_zip_response = true;

    /**
     * @var int ID of job
     */
    protected $job_id = null;

    protected $last_request = null;
    protected $last_response = null;

    public function getJobId()
    {
        return $this->job_id;
    }

    public function setIsZip($is_zip = true)
    {
        $this->is_zip_response = $is_zip;

        return $this;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        try {
            $this->last_request = $request;
            $return = parent::__doRequest($request, $location, $action, $version, $one_way);
            $this->last_response = $request;

            if (!strpos($return, '<faultstring>') && $this->is_zip_response) {
                $return = Mage::helper('retailexpress')->unzip($return, $this->job_id);
                throw new REX_RetailSoapZipException($return);
            }

            return $return;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
