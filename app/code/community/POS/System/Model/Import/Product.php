<?php

/**
 * class for importing product.
 */
class POS_System_Model_Import_Product extends POS_System_Model_Import_Abstract
{
    /**
     * @var string - prefix for config names
     */
    protected $_config_prefix = 'pb_';

    /**
     * @var array - ids REX => Magento
     */
    protected $_prod2id = [];

    protected $_created = 0;
    protected $_created_conf = 0;
    protected $_updated_conf = 0;
    protected $_updated = 0;
    protected $_errored = 0;

    /**
     * @var array of products
     */
    protected $_result = null;

    /**
     * initialize import.
     */
    protected function _initImport()
    {
        if (!$this->_initialized) {
            $this->_prod2id = $this->_readFile($this->_config_prefix.'prod2id', false);
            $this->_created = $this->_getConfigValue('created');
            $this->_updated = $this->_getConfigValue('updated');
            $this->_created_conf = $this->_getConfigValue('created_conf');
            $this->_updated_conf = $this->_getConfigValue('updated_conf');
            $this->_errored = $this->_getConfigValue('errored');
        }

        parent::_initImport();
    }

    /**
     * start import.
     *
     * @abstract
     */
    protected function _startImport()
    {
        REX_Logger::log('Starting products import.');

        $model = Mage::getModel('retailexpress/retail');
        if ($model->getError()) {
            throw new Exception($model->getError());
        }

        REX_Logger::log('Initialised "retail" model.');

        $last_date = Mage::helper('retailexpress')->getBulkLastTime('product');
        $current_time = time();
        try {
            $this->_import_data = $model->getProductsBulkDetail($last_date);
        } catch (SoapFault $e) {
            $this->_processSoapError($e);
            REX_Logger::log('soap '.$e->getMessage());
            exit;
        }

        REX_Logger::log('Loaded XML response.');

        REX_Logger::log('XML length is '.($this->_import_data ? round(strlen($this->_import_data) / 1024 / 1024, 2).'Mb' : strlen($this->_import_data)), REX_Logger::TYPE_SYNC);

        $this->_setConfigValue('bulklast', $current_time);

        $this->_saveFile($this->_config_prefix.$current_time.'.xml', $this->_import_data);
        $this->_setConfigValue('file', $this->_config_prefix.$current_time.'.xml');

        REX_Logger::log('Parse XML into array', REX_Logger::TYPE_SYNC);
        try {
            $this->_result = $model->parseProductXml($this->_import_data);
        } catch (Exception $e) {
            REX_Logger::log('Error parsing XML to Array: '.$e->getMessage(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
            throw $e;
        }
        $this->_saveFile($this->_config_prefix.'result', serialize($this->_result));

        $this->_report = [];
        $this->_report['Attributes'] = $this->_importAttributes($this->_result['attributes']);
        $this->_report['Payments'] = $this->_importPayments($this->_result['payments']);
        $this->_report['Products'] = '';
        $this->setTotal(sizeof($this->_result['products']));
    }

    /**
     * finish import data.
     */
    protected function _finishBulk()
    {
        Mage::helper('retailexpress')->setBulkLastTime('product', $this->_getConfigValue('bulklast'));

        if (!Mage::getSingleton('retailexpress/config')->getDebugInHistory()) {
            if (Mage::getStoreConfig('retailexpress/main/sync_type') == POS_System_Model_System_Method::IMPORT_TYPE_IMPORT) {
                $this->_report = ['Bulk Synchronisation of Product Details Completed Successfully' => ($this->_updated + $this->_created + $this->_created_conf + $this->_updated_conf).' Proccessed, '.$this->_errored.' Errors.'.(sizeof($this->_error) ? ' <a class="details" href="#" onclick="$(this).next().toggle();return false;">&#x00BB;</a><p style="display: none;">'.implode(PHP_EOL, $this->_error).'</p>' : '')];
            } else {
                $this->_report = ['Bulk Synchronisation of Product Details Completed Successfully' => $this->_updated.' Updated, '.$this->_created.' Simple Product(s) Created, '.$this->_created_conf.' Configurable Product(s) Created, '.$this->_errored.' Errors.'.(sizeof($this->_error) ? ' <a class="details" href="#" onclick="$(this).next().toggle();return false;">&#x00BB;</a><p style="display: none;">'.implode(PHP_EOL, $this->_error).'</p>' : '')];
            }
        }
        $this->_safeDelete($this->_config_prefix.'prod2id');
        $this->_safeDelete($this->_config_prefix.'result');

        parent::_finishBulk();
    }

    /**
     * do import step.
     *
     * @return bool - is finished or not
     */
    protected function _doImport()
    {
        Mage::dispatchEvent('rex_parse_after');
        REX_Logger::log(' pb_pos = '.$this->_pos, REX_Logger::TYPE_SYNC);

        if (Mage::getStoreConfig('retailexpress/main/sync_type') == POS_System_Model_System_Method::IMPORT_TYPE_IMPORT) {
            return $this->_importProductByImport();
        } else {
            return $this->_importProductByModel();
        }
    }

    protected function _parseXml()
    {
        if ($this->_result === null) {
            $model = Mage::getModel('retailexpress/retail');
            if ($model->getError()) {
                throw new Exception($model->getError());
            }
            $this->_import_data = $this->_readFile($this->_getConfigValue('file'));
//            if(empty($this->_import_data))
//                throw new Exception('');
            REX_Logger::log('Parse XML into array', REX_Logger::TYPE_SYNC);

            try {
                $this->_result = $model->parseProductXml($this->_import_data);
                $this->setTotal(sizeof($this->_result['products']));
            } catch (Exception $e) {
                REX_Logger::log('Error parsing XML to Array: '.$e->getMessage(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
                throw $e;
            }

            $this->_saveFile($this->_config_prefix.'result', serialize($this->_result));
            $model = null;
            unset($model);
        }
    }

    /**
     * Import product by import/export module.
     *
     * @return bool - is finished or not
     */
    protected function _importProductByImport()
    {
        /* @var $model_import POS_System_Model_Import_Product_Source */

        if ($this->getStatus() == POS_System_Model_Task_Status::STATUS_CANCEL) {
            REX_Logger::log('Exit cause task was cancelled.', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
            exit();
        }

        $timer = $this->getStartTime();
        $attribute_set = Mage::getModel('retailexpress/conf')->load('attribute_set')->getValue();
//        $attribute_set_attributes = array();
        $model = Mage::getModel('eav/entity_attribute_set')->load($attribute_set);
        if (!$model->getId()) {
            $attribute_set = false;
        }

        $attribute_set_name = $model->getAttributeSetName();

        $rex_attributes = [];
        foreach ($this->_result['attributes'] as $code => $values) {
            $rex_attributes[$code] = [];
            foreach ($values as $v) {
                $rex_attributes[$code][$v['id']] = $v['name'];
            }
        }

        $products_count = 0;
        $bunch_count = 0;
        $bunch_items = [];
        $finished = true;
        $website_name = Mage::app()->getWebsite(Mage::helper('retailexpress')->getWebsiteId())->getCode();
//        $last_errored = 0;
        $last_error = '';
//        $last_proccesed = 0;

        if ((time() > ($timer + Mage::getSingleton('retailexpress/config')->getConfigBulkTime() && $timer > 0) || $this->getStatus() == POS_System_Model_Task_Status::STATUS_CANCEL)) {
            $this->_commitImport();
            REX_Logger::log('Exit cause time is up. Time left '.(time() - $this->getStartTime()).' sec.', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);

            return false;
        }

        REX_Logger::log('Bulk import products start', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);

        $model_import = Mage::getModel('retailexpress/import_product_source');
        if ($attribute_set) {
            foreach ($this->_result['products'] as $product) {
                if (++$products_count <= $this->_pos) {
                    continue;
                }

                try {
                    $product['_store'] = $product['_category'] = /*$product['description'] = $product['short_description'] =*/
                    $product['_super_products_sku'] = $product['_super_attribute_code'] = $product['_super_attribute_option'] = $product['_super_attribute_price_corr'] = null;
                    if (!$product['weight']) {
                        $product['weight'] = 0;
                    }
                    $product['_product_websites'] = $website_name;
                    $product['_type'] = $product['type_id'];
                    $product['_attribute_set'] = $attribute_set_name;
                    $stock_data = $product['stock_data'];
                    $product['options_container'] = Mage::getModel('catalog/entity_product_attribute_design_options_container')->getOptionText('container1');
                    unset($product['type_id'], $product['stock_data']);
                    $product = array_merge($stock_data, $product);
                    foreach ($rex_attributes as $code => $__v) {
                        $attribute_code = Mage::getStoreConfig('retailexpress/attr/'.$code);
                        $v = $product[$code];
                        unset($product[$code]);
                        if ($attribute_code) {
                            if (isset($__v[$v])) {
                                $product[$attribute_code] = $__v[$v];
                            } else {
                                $product[$attribute_code] = '';
                            }
                        }
                    }

                    if ('configurable' == $product['_type']) {
                        $use_attributes = $this->_getSuperAttributes($product);

                        // check associated simple products attribute combinations
                        $result = $this->_isAttributeCombinationValid($this->_result['existing_associated_products'][$product['rex_product_id']], $use_attributes);

                        if (true === $result) {
                            // combinations are ok
                        } else {
                            // failed
                            // we just will create (update) configurable product without assigning and hide it
                            $product['visibility'] = 1;
                            $bunch_items[] = $product;

                            // log error message
                            ++$this->_errored;
                            $mes = 'POS Product ID '.$product['rex_sku'].' - Configurable Product is not set up correctly in Retail Express. Please check that all members of this style code have a size and or colour and do not have more than one of each combination in the group. Once updated, please try your synchronisation again.';
//                            $mes = 'Error assigning products to configurable product (POS ManufacturerSKU: ' .$product['rex_sku']. ') - products sharing the code have matching attribute combinations. Adjust the size/colour combinations in Retail Express to resolve this issue';
                            $this->_error[] = $mes;
                            $this->_report['Products'] .= $mes.PHP_EOL;
                            continue;
                        }

                        $use_skus = [];
                        foreach ($product['associated_products'] as $assoc_product) {
                            $use_skus[] = $assoc_product['sku'];
                        }

                        unset($product['associated_products']);
                        $max = max(sizeof($use_attributes), sizeof($use_skus));
                        for ($__i = 0; $__i < $max; ++$__i) {
                            if ($__i) {
                                foreach ($product as $pk => $pv) {
                                    $product[$pk] = null;
                                }
                            }

                            if (isset($use_skus[$__i])) {
                                $product['_super_products_sku'] = $use_skus[$__i];
                            }

                            if (isset($use_attributes[$__i])) {
                                $product['_super_attribute_code'] = Mage::getStoreConfig('retailexpress/attr/'.$use_attributes[$__i]);
                            }

                            $bunch_items[] = $product;
                        }
                    } else {
                        $bunch_items[] = $product;
                    }
                } catch (Exception $e) {
                    ++$this->_errored;
                    $mes = 'POS Product ID '.$product['rex_product_id'].' error: '.$e->getMessage();
                    $this->_error[] = $mes;
                    $this->_report['Products'] .= $mes.PHP_EOL;
                    REX_Logger::log('Error '.$e->getMessage().PHP_EOL.' at '.$e->getFile().':'.$e->getLine());
                    continue;
                }

                if (++$bunch_count >= Mage::getSingleton('retailexpress/config')->getProductsPerIteration()) {
                    try {
                        $model_import->resetImportedCounters();
                        Mage::getSingleton('retailexpress/import_product_resource')->setBunch($bunch_items);
                        $model_import->importSource();
                        $this->_errored += $model_import->getErrorsCount();
                        $last_error .= implode(PHP_EOL, array_keys($model_import->getErrors()));
                        $this->_created += $model_import->getProcessedEntitiesCount();
                        $this->_commitImport();
                    } catch (Exception $e) {
                        ++$this->_errored;
                        $mes = 'Import error: '.$e->getMessage();
                        $this->_error[] = $mes;
                        REX_Logger::log('Error '.$e->getMessage().PHP_EOL.' at '.$e->getFile().':'.$e->getLine());
                        $this->_report['Products'] .= $mes.PHP_EOL;
                    }

                    $bunch_count = 0;
                    $bunch_items = [];
                    $this->_pos = $products_count;
                    $this->_setConfigValue('pos', $products_count);
                    if (time() > ($timer + Mage::getSingleton('retailexpress/config')->getConfigBulkTime() || $this->getStatus() == POS_System_Model_Task_Status::STATUS_CANCEL)) {
                        $finished = false;
                        REX_Logger::log('Exit cause time has left.', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
                        break;
                    }
                }
            }
        } else {
            ++$this->_errored;
            $em = 'Product Attribute Set does not exists';
            $this->_error[] = $em;
            $this->_report['Products'] .= $em.PHP_EOL;
        }

        if ($bunch_count) {
            try {
                $model_import->resetImportedCounters();
                Mage::getSingleton('retailexpress/import_product_resource')->setBunch($bunch_items);
                $model_import->importSource();
                $this->_errored += $model_import->getErrorsCount();
                $last_error .= implode(PHP_EOL, array_keys($model_import->getErrors()));
                $this->_created += $model_import->getProcessedEntitiesCount();
                $this->_commitImport();
            } catch (Exception $e) {
                REX_Logger::log('Error '.$e->getMessage().PHP_EOL.' at '.$e->getFile().':'.$e->getLine());
                ++$this->_errored;
                $mes = 'Import error: '.$e->getMessage();
                $this->_error[] = $mes;
                $this->_report['Products'] .= $mes.PHP_EOL;
            }
        }

        $this->_error[] = $last_error;
        $this->_report['Products'] .= $last_error;
        // reindex here

        $this->_pos = $products_count;

        if (time() > ($timer + Mage::getSingleton('retailexpress/config')->getConfigBulkTime()) && ((time() - $timer) < 300 || (time() - $timer - 300) < (+Mage::getSingleton('retailexpress/config')->getConfigBulkTime()))) {
            REX_Logger::log('Exit cause time is up. Time left '.(time() - $this->getStartTime()).' sec.', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);

            return false;
        }

        if ($finished) {
            foreach (Mage::getResourceModel('index/process_collection')->getItems() as $p) {
                REX_Logger::log('Check index '.$p->getIndexerCode(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
                try {
                    $p->reindexEverything();
                } catch (Exception $e) {
                    REX_Logger::log('Error during reindex '.$e->getMessage(), REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
                }
                REX_Logger::log('Reindexing of '.$p->getIndexerCode().' was finished success', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
            }

            REX_Logger::log('Clean cache', REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);
            Mage::app()->cleanCache();
        }

        // apply catalog rules
        try {
            if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                // 1.7.0.0 or greater
                Mage::getModel('catalogrule/rule')->applyAll();
                Mage::getModel('catalogrule/flag')->loadSelf()
                    ->setState(0)
                    ->save();
            } else {
                Mage::getModel('catalogrule/rule')->applyAll();
                Mage::app()->removeCache('catalog_rules_dirty');
            }
        } catch (Mage_Core_Exception $e) {
            $this->_error[] = 'Catalog Rule error: '.$e->getMessage();
        }

        return $finished;
    }

    protected function _isAttributeCombinationValid($associatedProducts, $attributes)
    {
        $attributeCombinations = [];
        foreach ($associatedProducts as $product) {
            $key = [];
            foreach ($attributes as $attribute) {
                $key[] = $product[$attribute];
            }
            if (!isset($attributeCombinations[implode('_', $key)])) {
                $attributeCombinations[implode('_', $key)] = [];
            }
            $attributeCombinations[implode('_', $key)][] = $product['rex_product_id'];
        }
        if (count($associatedProducts) == count($attributeCombinations)) {
            return true;
        }
        $matchedProducts = [];
        foreach ($attributeCombinations as $attributeCombination) {
            if (count($attributeCombination) > 1) {
                $matchedProducts[] = $attributeCombination;
            }
        }

        return $matchedProducts;
    }

    /**
     * get super attributes for config product.
     *
     * @param $product
     *
     * @return array - super attributes array
     */
    protected function _getSuperAttributes($product)
    {
        REX_Logger::log('Bulk import products => superattribute '.$product['rex_sku'], REX_Logger::TYPE_SYNC, REX_Logger::CAT_PRODUCTS);

        $attribute_set_attributes = $this->getAttributeSetAttributes();
        if (is_null($attribute_set_attributes)) {
            $attribute_set = Mage::getModel('retailexpress/conf')->load('attribute_set')->getValue();
            $model = Mage::getModel('eav/entity_attribute_set')->load($attribute_set);
            if (!$model->getId()) {
                throw new Exception('Attribute set does not found');
            }

            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($model->getId())
                ->load();

            foreach ($groups as $group) {
                $groupAttributesCollection = Mage::getModel('eav/entity_attribute')
                    ->getResourceCollection()
                    ->setAttributeGroupFilter($group->getId())
                    ->load();

                foreach ($groupAttributesCollection as $attribute) {
                    $attribute_set_attributes[$attribute->getId()] = true;
                }
            }

            $this->setAttributeSetAttributes($attribute_set_attributes);
        }

        $use_attributes = [];
        foreach (['rex_sizes', 'rex_colours'] as $code) {
            // if only one associated simple product
            if (count($product['associated_products']) == 1) {
                if ($product['associated_products'][0][$code]) {
                    $use_attributes[] = $code;
                }

                break;
            }

            $find = null;
            $need_use = false;
            foreach ($product['associated_products'] as $assoc_product) {
                if (!isset($assoc_product[$code]) || !$assoc_product[$code]) {
                    $need_use = false;
                    break;
                }

                if (is_null($find)) {
                    $find = $assoc_product[$code];
                } elseif ($find != $assoc_product[$code]) {
                    $need_use = true;
                    break;
                }
            }

            if ($need_use) {
                $use_attributes[] = $code;
            }
        }

        if (!count($use_attributes)) {
            $this->_error[] = 'POS Product ID '.$product['rex_sku'].' - Configurable Product is not set up correctly in Retail Express. Please check that all members of this style code have a size and or colour and do not have more than one of each combination in the group. Once updated, please try your synchronisation again.';
        }

        foreach ($use_attributes as $code) {
            $attribute_code = Mage::getStoreConfig('retailexpress/attr/'.$code);
            if (!$attribute_code) {
                $this->_error[] = 'POS attribute '.$code.' is not mapped to an attribute in Magento, but has been used in defining Configurable Products. Please update your POS Attribute mapping in System > Configuration > POS System';
                continue;
            }
            $attr_id = Mage::getModel('eav/entity_attribute')->load(Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code)->getId())->getId();
            if (!isset($attribute_set_attributes[$attr_id])) {
                $this->_error[] = 'Attribute '.$attribute_code.' is missing from the attribute set ('.$attribute_set.')';
            }
        }

        return $use_attributes;
    }

    /**
     * Import product by model.
     *
     * @return bool - is finished or not
     */
    protected function _importProductByModel()
    {
        $timer = $this->getStartTime();
        $attribute_set = Mage::getModel('retailexpress/conf')->load('attribute_set')->getValue();
        $model = Mage::getModel('eav/entity_attribute_set')->load($attribute_set);
        if (!$model->getId()) {
            $attribute_set = false;
        }

        $products_count = 0;
        $products_count_error = 0;
        $finished = true;
        if ($attribute_set) {
            //            $report['Products'] = '';
            foreach ($this->_result['products'] as $product) {
                ++$products_count;
                if ($products_count <= $this->_pos) {
                    continue;
                }

                try {
                    $mag_product = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToFilter('rex_product_id', $product['rex_product_id'])
                        ->getFirstItem();
                    /*if ($mag_product->getId()) {
                        $mag_product->setStockItem(Mage::getModel('cataloginventory/stock_item')->loadByProduct($mag_product));
                    }*/

                    $product['attribute_set_id'] = $attribute_set;
                    $product['website_ids'] = [Mage::helper('retailexpress')->getWebsiteId()];
                    $product['options_container'] = 'container1';
                    if (!$product['weight']) {
                        $product['weight'] = 0;
                    }
                    if ($product['type_id'] == 'configurable') {
                        $__id = $mag_product->getId();
                        $media_gallery = [];
                        if ($__id) {
                            foreach (Mage::getModel('catalog/product')->load($mag_product->getId())->getData() as $___k => $___v) {
                                if (!is_object($___v) && !isset($product[$___k]) && !in_array($___k, ['entity_id', 'entity_type_id', 'updated_at', 'has_options', 'required_options', 'is_in_stock'])) {
                                    $product[$___k] = $___v;
                                }
                            }

                            $product['category_ids'] = $mag_product->getCategoryIds();
                            if (isset($product['media_gallery'])) {
                                foreach ($product['media_gallery']['images'] as $_ik => $_iv) {
                                    $distanationDirectory = dirname(Mage::getSingleton('catalog/product_media_config')->getTmpMediaPath($_iv['file']));
                                    if (!is_dir($distanationDirectory)) {
                                        mkdir($distanationDirectory, 0777, true);
                                    }
                                    $path = Mage::getBaseDir('media').'/catalog/product'.$_iv['file'];
                                    @copy($path, Mage::getSingleton('catalog/product_media_config')->getTmpMediaPath($_iv['file']));
                                    unset($product['media_gallery']['images'][$_ik]['value_id']);
                                }
                            }
                            if ('magematrix' == $product['rex_product_id']) {
                            }
                            $mag_product->delete();
                        }

                        $import_product = Mage::getModel('catalog/product')->load(''/*$mag_product->getId()*/);
                        $import_product->setAttributeSetId($product['attribute_set_id']);
                        $import_product->setTypeId($product['type_id']);
                        $use_attributes = $this->_getSuperAttributes($product);

                        // check associated simple products attribute combinations
                        $result = $this->_isAttributeCombinationValid($this->_result['existing_associated_products'][$product['rex_product_id']], $use_attributes);

                        if (true === $result) {
                            // combinations are ok
                            $product['configurable_products_data'] = [];
                            $product['configurable_attributes_data'] = [];
                            foreach ($product['associated_products'] as $assoc_product) {
                                $list = [];
                                foreach ($use_attributes as $code) {
                                    $attribute_code = Mage::getStoreConfig('retailexpress/attr/'.$code);
                                    $list[] = [
                                        'label' => '',
                                        'value_index' => $this->getCacheAttributeByRex($attribute_code, $assoc_product[$code]),
                                        'attribute_id' => Mage::getModel('eav/entity_attribute')->load(Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code)->getId())->getId(),
                                        'is_percent' => 0,
                                        'pricing_value' => '',
                                    ];
                                }
                                $product['configurable_products_data'][$this->_prod2id[$assoc_product['rex_product_id']]] = $list;
                                $a_ids = [];
                                foreach ($use_attributes as $code) {
                                    $attribute_code = Mage::getStoreConfig('retailexpress/attr/'.$code);
                                    $a_ids[] = Mage::getModel('eav/entity_attribute')->load(Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code)->getId())->getId();
                                }
                            }

                            foreach ($use_attributes as $code) {
                                $attribute_code = Mage::getStoreConfig('retailexpress/attr/'.$code);
                                $list = [
                                    'id' => null,
                                    'label' => '',
                                    'use_default' => 1,
                                    'position' => 0,
                                    'values' => [],
                                    'attribute_id' => Mage::getModel('eav/entity_attribute')->load(Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code)->getId())->getId(),
                                    'attribute_code' => $attribute_code,
                                    'frontend_label' => '',
                                    'store_label' => '',
                                    'html_id' => '',];
                                $values = [];
                                $setted_value = [];
                                foreach ($product['associated_products'] as $assoc_product) {
                                    if (isset($setted_value[$assoc_product[$code]])) {
                                        continue;
                                    }

                                    $values[] = [
                                        'attribute_id' => Mage::getModel('eav/entity_attribute')->load(Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code)->getId())->getId(),
                                        'value_index' => $this->getCacheAttributeByRex($attribute_code, $assoc_product[$code]),
                                        'label' => '',
                                        'is_percent' => '',
                                        'pricing_value' => '',
                                    ];
                                    $setted_value[$assoc_product[$code]] = true;
                                }
                                $list['values'] = $values;
                                $product['configurable_attributes_data'][] = $list;
                            }
                        } else {
                            // failed
                            // we just will create (update) configurable product without assigning and hide it
                            $product['visibility'] = 1;

                            ++$this->_errored;
                            $mes = 'Error assigning products to configurable product (POS ManufacturerSKU: '.$product['rex_sku'].') - products sharing the code have matching attribute combinations. Adjust the size/colour combinations in Retail Express to resolve this issue';
                            $this->_error[] = $mes;
                            $this->_report['Products'] .= $mes.PHP_EOL;
                            ++$products_count_error;
                        }

                        if (!$import_product->getId()) {
                            $import_product->getTypeInstance()->setUsedProductAttributeIds($a_ids);
                        }

                        $import_product->setStockItem(Mage::getModel('cataloginventory/stock_item')->loadByProduct($import_product));

                        unset($product['associated_products']);
                        $import_product->addData($product)->save();
                        if ($__id) {
                            ++$this->_updated;
                        } else {
                            ++$this->_created_conf;
                        }
                    } else {
                        foreach ($this->_result['attributes'] as $code => $__v) {
                            $attribute_code = Mage::getStoreConfig('retailexpress/attr/'.$code);
                            $v = $product[$code];
                            unset($product[$code]);
                            if ($attribute_code) {
                                if ($_temp = $this->getCacheAttributeByRex($attribute_code, $v)) {
                                    $product[$attribute_code] = $_temp;
                                } else {
                                    $product[$attribute_code] = 0;
                                }
                            }
                        }

                        $__id = $mag_product->getId();
                        $import_product = Mage::getModel('catalog/product')->load($mag_product->getId());
                        $import_product->setStockItem(Mage::getModel('cataloginventory/stock_item')->loadByProduct($import_product));
                        $import_product->addData($product)->save();
                        if ($__id) {
                            ++$this->_updated;
                        } else {
                            ++$this->_created;
                        }
                    }

                    $this->_prod2id[$product['rex_product_id']] = $import_product->getId();
                    $this->_report['Products'] .= 'POS Product ID '.$product['rex_product_id'].' synchronised: (#'.$import_product->getId().')'.PHP_EOL;
                    REX_Logger::log('prod2id: '.print_r($this->_prod2id, true), REX_Logger::TYPE_SYNC);
                } catch (Exception $e) {
                    ++$this->_errored;
                    $mes = 'POS Product ID '.$product['rex_product_id'].' error: '.$e->getMessage();
                    $this->_error[] = $mes;
                    $this->_report['Products'] .= $mes.PHP_EOL;
                    ++$products_count_error;
                }

                if (($products_count % 10) == 0) {
                    $this->_setConfigValue('pos', $products_count);
                    if (time() > ($timer + Mage::getSingleton('retailexpress/config')->getConfigBulkTime())) {
                        $finished = false;
                        break;
                    }
                }
            }
        } else {
            ++$this->_errored;
            $em = 'Product Attribute Set does not exists';
            $this->_report['Products'] .= $em.PHP_EOL;
            $this->_error[] = $em;
        }

        $this->_setConfigValue('pos', $products_count);
        $this->_pos = $products_count;

        return $finished;
    }

    /**
     * Commit import step result.
     */
    protected function _commitImport()
    {
        $this->_setConfigValue('created', $this->_created);
        $this->_setConfigValue('created_conf', $this->_created_conf);
        $this->_setConfigValue('updated', $this->_updated);
        $this->_setConfigValue('updated_conf', $this->_updated_conf);
        $this->_setConfigValue('errored', $this->_errored);

        // make product_ids file
        parent::_commitImport();

        $this->_saveFile($this->_config_prefix.'prod2id', serialize($this->_prod2id));
        $this->_saveFile($this->_config_prefix.'result', serialize($this->_result));
    }

    /**
     * Import attributes.
     *
     * @param $attributes - attributes array
     *
     * @return string - report string
     */
    protected function _importAttributes($attributes)
    {
        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_write');
            $optionTable = Mage::getSingleton('retailexpress/attr')->getResource()->getTable('eav/attribute_option');
            $optionValueTable = Mage::getSingleton('retailexpress/attr')->getResource()->getTable('eav/attribute_option_value');
            foreach ($attributes as $code => $values) {
                $attribute_code = Mage::getStoreConfig('retailexpress/attr/'.$code);
                if (!$attribute_code) {
                    continue;
                }

                $attribute = Mage::getModel('eav/entity_attribute')->load(Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code)->getId());

                $attribute_id = $attribute->getId();
                if (!$attribute_id) {
                    continue;
                }

//                $conn->delete($optionTable, $conn->quoteInto('attribute_id=?', $attribute_id));
                foreach ($values as $labels) {
                    $magento_id = Mage::getModel('retailexpress/attr')->getMagentoIdByRexCode($attribute_code, $labels['id']);
                    if (('brand' == $attribute_code) && Mage::getConfig()->getModuleConfig('Infinity_Allcooks')->is('active', 'true')) {
                        $Brand_model = Mage::getModel($attribute->getSourceModel())->load($magento_id);
                        $Brand_model->setBrandName($labels['name'])->save();
                        $magento_id = $Brand_model->getId();
                        Mage::getModel('retailexpress/attr')->setData([
                            'code' => $attribute_code, 'rex_id' => $labels['id'], 'magento_id' => $magento_id,
                        ])->save();
                    } else {
                        if ($magento_id) {
                            $_r = $conn->fetchRow('SELECT * FROM '.$optionTable.' WHERE option_id = ?', [(int) $magento_id]);
                            if (!is_array($_r) || !isset($_r['attribute_id']) || ($_r['attribute_id'] != $attribute_id)) {
                                Mage::getModel('retailexpress/attr')->deleteMagentoIdByRexCodeDirectly($attribute_code, $labels['id']);
                                $magento_id = false;
                            }
                        }
                        if (!$magento_id) {
                            $data = [
                                'attribute_id' => $attribute_id,
                                'sort_order' => isset($labels['sort_order']) ? $labels['sort_order'] : 0,
                            ];
                            $conn->insert($optionTable, $data);
                            $magento_id = $conn->lastInsertId();
                            $data = [
                                'option_id' => $magento_id,
                                'store_id' => 0,
                                'value' => $labels['name'],
                            ];
                            $conn->insert($optionValueTable, $data);
                            Mage::getModel('retailexpress/attr')->setData([
                                'code' => $attribute_code, 'rex_id' => $labels['id'], 'magento_id' => $magento_id,
                            ])->save();
                        } else {
                            if (isset($labels['sort_order'])) {
                                $conn->update($optionTable, ['sort_order' => $labels['sort_order']], $conn->quoteInto('option_id = ?', $magento_id));
                            }

                            $conn->update($optionValueTable, ['value' => $labels['name']], $conn->quoteInto('option_id = ? AND store_id = 0', $magento_id));
                        }
                    }
                }
            }

            return 'Synchronised.';
        } catch (Exception $e) {
            return 'Error: '.$e->getMessage();
        }
    }

    /**
     * import payments.
     *
     * @param $payments - arrat of REX payments
     *
     * @return string - report string
     */
    protected function _importPayments($payments)
    {
        try {
            foreach (Mage::getModel('retailexpress/payment')->getCollection()->getItems() as $i) {
                if (!isset($payments[$i->getRexId()])) {
                    $i->delete();
                }

                $i->addData($payments[$i->getRexId()])->save();
                unset($payments[$i->getRexId()]);
            }

            foreach ($payments as $p) {
                Mage::getModel('retailexpress/payment')->addData($p)->save();
            }
        } catch (Exception $e) {
            return 'Error: '.$e->getMessage();
        }

        return 'Synchronised.';
    }

    /**
     * Get magento attribute value id by rex id.
     *
     * @param $code - attribute code
     * @param $rex_id - REX attribute value
     *
     * @return $magento - attribute value
     */
    public function getCacheAttributeByRex($code, $rex_id)
    {
        static $values = [];
        if (!isset($values[$code])) {
            $values[$code] = [];
        }

        if (!isset($values[$code][$rex_id])) {
            $values[$code][$rex_id] = Mage::getModel('retailexpress/attr')->getMagentoIdByRexCodeDirectly($code, $rex_id);
        }

        return $values[$code][$rex_id];
    }
}
