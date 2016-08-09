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
 
class Activo_AdvancedSearch_Block_Autocomplete extends Mage_CatalogSearch_Block_Autocomplete
{
    protected $_suggestData = null;

    protected function _toHtml()
    {
        $html = '';

        if (!$this->_beforeToHtml()) {
            return $html;
        }

        $suggestData = $this->getSuggestData();
        $count = count($suggestData);
        $count--;

        $html = '<ul><li style="display:none"></li>';
        $query = $this->helper('advancedsearch')->getQueryText(false);
        $newWords = Mage::getModel('advancedsearch/dictionary')->correct($query);
        
        for ($i=0; $i<Mage::getStoreConfig('activo_advancedsearch/global/correctmax'); $i++)
        {
            $gotCorrection = false;
            $suggestHtml = "";
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
                            $suggestHtml.= " <strong>".$correction[$i]['word']."</strong>";
                            $suggestText.= " ".$correction[$i]['word'];
                        }
                        else
                        {
                            if ($i==0)
                            {
                                $suggestHtml.= " ".$word;
                                $suggestText.= " ".$word;
                            }
                            else
                            {
                                $suggestHtml.= " <strong>".$correction[0]['word']."</strong>";
                                $suggestText.= " ".$correction[0]['word'];
                            }
                        }
                    }
                    else
                    {
                        $suggestHtml.= " ".$word;
                        $suggestText.= " ".$word;
                    }
                }
            }
            
            if ($gotCorrection)
            {
                $html.= '<li title="'.trim($suggestText).'" class="suggest">Did you mean '.$suggestHtml.'?</li>';
            }
        }
                
        foreach ($suggestData as $index => $item) {
            if ($index == 0) {
                $item['row_class'] .= ' first';
            }

            if ($index == $count) {
                $item['row_class'] .= ' last';
            }

            $html .=  '<li title="'.$this->htmlEscape($item['title']).'" class="'.$item['row_class'].'">'
                . '<span class="amount">'.$item['num_of_results'].'</span>'.$this->htmlEscape($item['title']).'</li>';
        }

        $html.= '</ul>';

        return $html;
    }
}
