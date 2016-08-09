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
 
class Activo_AdvancedSearch_Model_Resource_Dictionary extends Mage_Core_Model_Mysql4_Abstract
{
    protected $attrCodes = array();
    protected $delimiters = array(',', '.', '|', );
    
    protected function _construct()
    {
        $this->_init('advancedsearch/dictionary', 'id');
        
        $eavConfig = Mage::getSingleton('eav/config');
        $this->attrCodes[] = $eavConfig->getAttribute('catalog_product', 'name')->getId();
    }
    
    public function build($mainDictionary)
    {
        $_nameAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name')->getAttributeId();
        $_visibilityAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'visibility')->getAttributeId();
        
        $this->emptyIndexes();
        
        $_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $tableProduct        = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity');
        $tableProductInt     = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_int');
        $tableProductVarchar = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');

        $sql = 'SELECT '.
                'main.entity_id, main.sku, '.
                'p_var.value as name '.
                'FROM '.$tableProduct.' AS main '.
                'LEFT JOIN '.$tableProductInt.' AS p_int '.
                    'ON main.entity_id=p_int.entity_id AND p_int.store_id=' . Mage::app()->getStore()->getId() . ' AND p_int.attribute_id = '.$_visibilityAttributeId.' '.
                'LEFT JOIN '.$tableProductVarchar.' AS p_var '.
                    'ON main.entity_id=p_var.entity_id AND p_var.store_id=' . Mage::app()->getStore()->getId() . ' AND p_var.attribute_id = '.$_nameAttributeId.' '.
                'WHERE p_int.value IN ('.Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH.','.Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH.')';
        
        $_productData = $_read->fetchAll($sql);        

        $numProducts = count($_productData);
        $numWords = 0;
                
        foreach ($_productData as $p) 
        {
            $numWords += $this->parseStringsIntoDictionary($p['name'], $p['entity_id'], $_nameAttributeId);
        }
        
        $mainDictionary->setNumProducts($numProducts);
        $mainDictionary->setNumWords($numWords);
        $mainDictionary->save();
    }
    
    public function parseStringsIntoDictionary($string, $productId, $attributeId)
    {
        //TODO: adopt a way to handle various language delimitors
        $numWords = 0;
        
        $words = explode(" ", $string);
        $stopwords = explode(",", Mage::getStoreConfig('activo_advancedsearch/global/stopwords'));
        
        foreach ($words as $word) 
        {
            //Clean words from special characters
            $word = trim(preg_replace('/[^a-zA-Z0-9]/'," ", $word));
            
            //Learn only words longer then 3 letters
            
            if (strlen($word)>=3 && !in_array($word, $stopwords))
            {
                $this->learnNewWord($word, $productId, $attributeId);
                $numWords++;
            }
        }
        
        return $numWords;
    }
    
    public function learnNewWord($word, $productId, $attributeId)
    {
        $read  = Mage::getSingleton('core/resource')->getConnection('core_read');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableWord = Mage::getSingleton('core/resource')->getTableName('activo_advancedsearch_word');
        $tableWord2Product = Mage::getSingleton('core/resource')->getTableName('activo_advancedsearch_word_product');
        
        $soundex = soundex($word);
        
        try {
            $lookupSql = "SELECT id FROM ".$tableWord." WHERE word='".$word."'";
            $wordId = $read->fetchRow($lookupSql);
            
            if (!$wordId)
            {
                $sql = "INSERT INTO ".$tableWord." (soundex,word) ";
                $sql.= "VALUES ('$soundex','$word')";
            
                $write->query($sql);
                
                $wordId = array('id' => $write->lastInsertId($tableWord));
            }
            
            $sql = "INSERT IGNORE INTO ".$tableWord2Product." (word_id,product_id,attribute_id) ";
            $sql.= "VALUES ({$wordId['id']},$productId,$attributeId)";
            $write->query($sql);
        } 
        catch (Exception $e) 
        {
            Mage::log('Problem inserting word ('.$word.') into DB.');
        }
    }
    
    public function getCorrectedPhrase($phrase)
    {
        $newWords = $this->correct($phrase);
        
        for ($i=0; $i<1; $i++)
        {
            $gotCorrection = false;
            $suggestText = "";
            
            foreach ($newWords as $token)
            {
                foreach ($token as $word => $correction)
                {                    
                    if (is_array($correction))
                    {
                        if (isset($correction[$i])) $gotCorrection = true;
                        
                        if (isset($correction[$i]['word']))
                        {
                            $suggestText.= " ".$correction[$i]['word'];
                        }
                        else
                        {
                            if ($i==0)
                            {
                                $suggestText.= " ".$word;
                            }
                            else
                            {
                                $suggestText.= " ".$correction[0]['word'];
                            }
                        }
                    }
                    else
                    {
                        $suggestText.= " ".$word;
                    }
                }
            }
        }

        if ($gotCorrection)
        {
            return trim($suggestText);
        }
        else
        {
            return $phrase;
        }
    }

    public function correct($phrase)
    {
        $words = explode(" ",$phrase);
        
        $newWords = array();
        foreach ($words as $word)
        {
            $newWords[] = array( $word => $this->correctWord($word) );
        }
        
        return $newWords;
    }
    
    protected function correctWord($word)
    {
        $read  = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableWord = Mage::getSingleton('core/resource')->getTableName('activo_advancedsearch_word');
        $tableWord2Product = Mage::getSingleton('core/resource')->getTableName('activo_advancedsearch_word_product');

        if ($this->isWordCorrect($word))
        {
            return false; //nothing to correct
        }
        else
        {
            $soundex = soundex($word);
            $lookupBySoundex = "SELECT word FROM ".$tableWord." WHERE soundex='".$soundex."'";
            
            $words = $read->fetchAll($lookupBySoundex);
            
            if (count($words)==0)
            {
                return false; //no suggestions available
            }
            else
            {
                foreach ($words as $key => &$w)
                {
                    $w['dist'] = levenshtein($word, $w['word']);
                }
                //Sort array of similar words by levenshtein distance
                //uasort($words, function($a, $b){ return strcasecmp($a['dist'], $b['dist']); }); 
                uasort($words, array($this, 'distSort')); 
                
                //Cut array and only keep top NNN results from config options
                $words = array_slice($words, 0, Mage::getStoreConfig('activo_advancedsearch/global/correctmax'));
                
                return $words;
            }
        }
    }

    protected function distSort($a, $b)
    {
    	return strcasecmp($a['dist'], $b['dist']);
    }
        
    protected function isWordCorrect($word)
    {
        $read  = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableWord = Mage::getSingleton('core/resource')->getTableName('activo_advancedsearch_word');
        
        $lookupWord = "SELECT id FROM ".$tableWord." WHERE word='".$word."'";
        $wordId = $read->fetchRow($lookupWord);
        
        return $wordId != false;
    }
    
    public function emptyIndexes()
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableWord = Mage::getSingleton('core/resource')->getTableName('activo_advancedsearch_word');
        $tableWord2Product = Mage::getSingleton('core/resource')->getTableName('activo_advancedsearch_word_product');
        
        $write->query("TRUNCATE $tableWord");
        $write->query("TRUNCATE $tableWord2Product");
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ( !$object->getId() ) {
            $object->setCreatedAt(now());
        }
        $object->setModifiedAt(now());
        return $this;
    }
}