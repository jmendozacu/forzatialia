<?php

/**
 * Retail Diagnostic controller.
 *
 *
 * Controller for the diagnostic page
 *
 * @author chris@retailexpress.com.au
 */
class POS_System_Adminhtml_RetaildiagnosticController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/diagnostic')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Diagnostic Tool'), Mage::helper('adminhtml')->__('Diagnostic Tool'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function ajaxAction()
    {
        $error = true;
        $result = null;

        if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
            $this->getResponse()->setHeader('content-type', 'application/json');
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    [
                        'redirect' => Mage::helper('adminhtml')->getUrl(),
                        'error' => true,
                    ]
                )
            );
        }

        if (Mage::app()->getRequest()->isAjax()) {
            /* @var POS_System_Model_Mysql4_Diagnostic_Collection $collection */
            $diagnostic = Mage::getModel('retailexpress/diagnostic');

            if (Mage::app()->getRequest()->has('id')) {
                $collection = $diagnostic->getCollection();
                $collection->addFieldToSelect(['list_id', 'section', 'section_name', 'name', 'type', 'path', 'last_status']);
                $collection->getSelect()
                    ->joinLeft(['c' => 'core_config_data'], 'main_table.path = c.path', 'c.*')->order('order', 'ASC');
                $collection->addFieldToFilter('list_id', ['eq' => Mage::app()->getRequest()->get('id')]);

                $item = $collection->getFirstItem();

                try {
                    $status = $diagnostic->runDiagnostic($item->type, $item->path, $item->value);
                    $error = false;
                    $result = [
                        'id' => $item->getId(),
                        'status' => $status,
                    ];
                    $item->setLastStatus($status);
                } catch (Exception $e) {
                    $error = true;
                    $result = [
                        'id' => $item->getId(),
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ];
                    $item->setLastStatus('error');
                }
                $item->save();

                if (Mage::app()->getRequest()->get('debug')) {
                    $result['debug'] = print_r($item->getData(), true);
                }
            } else {
                $result = [
                    'id' => Mage::app()->getRequest()->get('id'),
                    'message' => 'Parameter ID wrong',
                ];
            }
        } else {
            $result = [
                'id' => Mage::app()->getRequest()->get('id'),
            ];
        }

        $this->getResponse()->setHeader('content-type', 'application/json');
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                [
                    'result' => $result,
                    'error' => $error,
                ]
            )
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/history/retaildiagnostic');
    }
}
