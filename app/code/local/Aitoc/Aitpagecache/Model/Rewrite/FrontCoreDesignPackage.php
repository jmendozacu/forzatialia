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
/**
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/

class Aitoc_Aitpagecache_Model_Rewrite_FrontCoreDesignPackage extends Mage_Core_Model_Design_Package
{
    /* 
    * rewrite method getMergedJsUrl
    * add opportunity to minify js files after merge (add callback method in Aitpagecache Helper class)
    */
    public function getMergedJsUrl($files)
    {
        
        $targetFilename = md5(implode(',', $files)) . '.js';
        $targetDir = $this->_initMergerDir('js');
        if (!$targetDir) {
            return '';
        }

        if(file_exists($targetDir . DS . $targetFilename)) {
            return Mage::getBaseUrl('media') . 'js/' . $targetFilename;
        }

        //if (Mage::helper('core')->mergeFiles($files, $targetDir . DS . $targetFilename, false, null, 'js')) {
        $time = time();
        #Mage::log('JS MIN START');
        if (Mage::helper('core')->mergeFiles($files, $targetDir . DS . $targetFilename, true, array('Aitoc_Aitpagecache_Helper_MinifyJs', 'minify'), 'js')) {
            #Mage::log('JS MIN END: ' . (time() - $time));
            return Mage::getBaseUrl('media') . 'js/' . $targetFilename;
        }

        return '';
    }


    /**
     * Merge specified css files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedCssUrl($files)
    {
        if(version_compare(Mage::getVersion(), '1.5.0.0', '>='))
        {
            // secure or unsecure
            $isSecure = Mage::app()->getRequest()->isSecure();
            $mergerDir = $isSecure ? 'css_secure' : 'css';
            $targetDir = $this->_initMergerDir($mergerDir);
            if (!$targetDir) {
                return '';
            }

            // base hostname & port
            $baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
            $hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
            $port = parse_url($baseMediaUrl, PHP_URL_PORT);
            if (false === $port) {
                $port = $isSecure ? 443 : 80;
            }

            // merge into target file
            $targetFilename = md5(implode(',', $files) . "|{$hostname}|{$port}") . '.css';
            
            if(file_exists($targetDir . DS . $targetFilename)) {
                return $baseMediaUrl . $mergerDir . '/' . $targetFilename;
            }
            
            $time = time();
            #Mage::log('CSS MIN START');
            //if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, array($this, 'beforeMergeCss'), 'css')) {
            if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, true, array($this, 'beforeMergeCss'), 'css')) {
                #Mage::log('CSS MIN END: ' . (time() - $time));
                return $baseMediaUrl . $mergerDir . '/' . $targetFilename;
            }
            return '';
        }
        else
        {
            $targetFilename = md5(implode(',', $files)) . '.css';
            $targetDir = $this->_initMergerDir('css');
            if (!$targetDir) {
                return '';
            }

            // check if cached file exists
            // 
            if(file_exists($targetDir . DS . $targetFilename)) {
                return Mage::getBaseUrl('media') . 'css/' . $targetFilename;
            }
            
            $time = time();
            #Mage::log('CSS MIN START');
            //if (Mage::helper('core')->mergeFiles($files, $targetDir . DS . $targetFilename, false, array($this, 'beforeMergeCss'), 'css')) {
            if (Mage::helper('core')->mergeFiles($files, $targetDir . DS . $targetFilename, true, array($this, 'beforeMergeCss'), 'css')) {
                 #Mage::log('CSS MIN END: ' . (time() - $time));
                return Mage::getBaseUrl('media') . 'css/' . $targetFilename;
            }
            return '';
        }
    }


    public function beforeMergeCss($file, $contents)
    {
       $this->_setCallbackFileDir($file);

       $cssImport = '/@import\\s+([\'"])(.*?)[\'"]/';
       $contents = preg_replace_callback($cssImport, array($this, '_cssMergerImportCallback'), $contents);

       $cssUrl = '/url\\(\\s*([^\\)\\s]+)\\s*\\)?/';
       $contents = preg_replace_callback($cssUrl, array($this, '_cssMergerUrlCallback'), $contents);

       // Aitoc Magento Booster modification: add minify
       $contents = call_user_func(array(Mage::helper('aitpagecache/minifyCss'), 'minify'), $contents);

       return $contents;
    }

}

?>