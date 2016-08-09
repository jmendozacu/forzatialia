<?php

class REX_CDATAXMLElement extends SimpleXMLElement
{
    public function addCDATA($text)
    {
        $node = dom_import_simplexml($this);
        $ownerDocument = $node->ownerDocument;
        $node->appendChild($ownerDocument->createCDATASection($text));
    }
}
