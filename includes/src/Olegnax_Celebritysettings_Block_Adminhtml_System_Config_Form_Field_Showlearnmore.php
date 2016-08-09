<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Celebritysettings_Block_Adminhtml_System_Config_Form_Field_Showlearnmore extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Override method to output our custom image
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        // Get the default HTML for this option
        $html = parent::_getElementHtml($element);

        $html .= '<div class="helper-image"><br/><img src="'.$this->getSkinUrl('images/olegnax/celebrity/navigation_learn_more.jpg').'" alt="" /></div>';
        return $html;
    }
}