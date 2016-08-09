<?php

class POS_System_Block_Adminhtml_Payment extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected function _construct()
    {
        parent::_construct();
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        foreach ($element->getSortedElements() as $field) {
            break;
        }

        foreach ($this->getActivePaymentMethods() as $p) {
            $field_clone = clone $field;
            $field_clone->setId(str_replace('payf', $p['value'], $field_clone->getId()));
            $field_clone->setName(str_replace('payf', $p['value'], $field_clone->getName()));
            $field_clone->setLabel(sprintf($field_clone->getLabel(), $p['label']));
            $field_clone->setValue(Mage::getStoreConfig('retailexpress/payments/'.$p['value']));
            $html .= $field_clone->toHtml();
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    public function getActivePaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = [
                'label' => $paymentTitle,
                'value' => $paymentCode,
            ];
        }

        return $methods;
    }
}
