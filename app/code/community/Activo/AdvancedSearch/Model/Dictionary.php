<?php
/**
 * Activo Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Activo Commercial License
 * that is available through the world-wide-web at this URL:
 * http://extensions.activo.com/license_professional
 *
 * @copyright   Copyright (c) 2012 Activo Extensions (http://extensions.activo.com)
 * @license     Commercial
 */
 
class Activo_AdvancedSearch_Model_Dictionary extends Mage_Core_Model_Abstract
{
    
    protected function _construct()
    {
        $this->_init('advancedsearch/dictionary');
    }
    
    public function build()
    {
        $this->getResource()->build($this);
    }
    
    public function correct($word)
    {
        return $this->getResource()->correct($word);
    }
}
