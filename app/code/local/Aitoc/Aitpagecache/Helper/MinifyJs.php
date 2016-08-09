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

/* 
* implementation of JsMin library
*/
class Aitoc_Aitpagecache_Helper_MinifyJs extends Mage_Core_Helper_Abstract
{
    const ORD_LF    = 10;
    const ORD_SPACE = 32;

    protected $a           = '';
    protected $b           = '';
    protected $input       = '';
    protected $inputIndex  = 0;
    protected $inputLength = 0;
    protected $lookAhead   = null;
    protected $output      = '';

    /**
     * Minify Javascript
     *
     * @param string $file path to JS file to be minified
     * @param string $contents Javascript to be minified
     * @return string
     */
    public static function minify($file, $contents)
    {
        $jsmin = new Aitoc_Aitpagecache_Helper_MinifyJs($contents);
        return $jsmin->min();
    }

    protected function __construct($input)
    {
        $this->input       = str_replace("\r\n", "\n", $input);
        $this->inputLength = strlen($this->input);
    }

    protected function action($d)
    {
        switch ($d) {
            case 1:
                $this->output .= $this->a;
                // fallthrough
            case 2:
                $this->a = $this->b;
                if ($this->a === "'" || $this->a === '"') {
                    for (;;) {
                        $this->output .= $this->a;
                        $this->a       = $this->get();
                        if ($this->a === $this->b) {
                            break;
                        }
                        if (ord($this->a) <= self::ORD_LF) {
                            Mage::ThrowException('Unterminated string literal.');
                        }
                        if ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a       = $this->get();
                        }
                    }
                }
                // fallthrough
            case 3:
                $this->b = $this->next();
                if ($this->b === '/') {
                    switch ($this->a) {
                        case "\n": 
                        case ' ':
                            if (! $this->spaceBeforeRegExp($this->output)) {
                                break;
                            }
                        case '{':
                        case ';':
                        case '(':
                        case ',':
                        case '=':
                        case ':':
                        case '[':
                        case '!':
                        case '&':
                        case '|':
                        case '?':
                            $this->output .= $this->a.$this->b;
                            for (;;) {
                                $this->a = $this->get();
                                if ($this->a === '/') {
                                    break; // for (;;)
                                } elseif ($this->a === '\\') {
                                    $this->output .= $this->a;
                                    $this->a       = $this->get();
                                } elseif (ord($this->a) <= self::ORD_LF) {
                                    throw new JSMinException('Unterminated regular expression literal.');
                                }
                                $this->output .= $this->a;
                            }
                            $this->b = $this->next();
                            break; // switch ($this->a)
                        // end case ?
                    }
                }
                break; // switch ($d)
            // end case 3
        }
    }

    protected function get()
    {
        $c = $this->lookAhead;
        $this->lookAhead = null;
        if ($c === null) {
            if ($this->inputIndex < $this->inputLength) {
                $c = $this->input[$this->inputIndex];
                $this->inputIndex += 1;
            } else {
                $c = null;
            }
        }
        return ($c === "\r") 
            ? "\n"
            : ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE
                ? $c
                : ' ');
    }

    protected function isAlphaNum($c)
    {
        return (ord($c) > 126 
                || $c === '\\' 
                || preg_match('/^[\w\$]$/', $c) === 1);
    }

    protected function min()
    {
        $this->a = "\n";
        $this->action(3);

        while ($this->a !== null) {
            switch ($this->a) {
                case ' ':
                    if ($this->isAlphaNum($this->b)) {
                        $this->action(1);
                    } else {
                        $this->action(2);
                    }
                    break;
                case "\n":
                    switch ($this->b) {
                        case '{':
                        case '[':
                        case '(':
                        case '+':
                        case '-':
                            $this->action(1);
                            break;
                        case ' ':
                            $this->action(3);
                            break;
                        default:
                            if ($this->isAlphaNum($this->b)) {
                                $this->action(1);
                            } else {
                                $this->action(2);
                            }
                    }
                    break;
                default:
                    switch ($this->b) {
                        case ' ':
                            if ($this->isAlphaNum($this->a)) {
                                $this->action(1);
                                break; // switch ($this->b)
                            }
                            $this->action(3);
                            break; // switch ($this->b)
                        case "\n":
                            switch ($this->a) {
                                case '}':
                                case ']':
                                case ')':
                                case '+':
                                case '-':
                                case '"':
                                case "'":
                                    $this->action(1);
                                    break; // switch ($this->a)
                                default:
                                    if ($this->isAlphaNum($this->a)) {
                                        $this->action(1);
                                    } else {
                                        $this->action(3);
                                    }
                            }
                            break; // switch ($this->b)
                        default:
                            $this->action(1);
                            break; // switch ($this->b)
                    }
                // end default
            }
        }
        return $this->output;
    }

    protected function next()
    {
        $get = $this->get();
        if ($get === '/') {
            $commentContents = '';
            switch ($this->peek()) {
                case '/':
                    // "//" comment
                    for (;;) {
                        $get = $this->get();
                        $commentContents .= $get;
                        if (ord($get) <= self::ORD_LF) {
                            return preg_match('/^\\/@(?:cc_on|if|elif|else|end)\\b/', $commentContents)
                                ? "/{$commentContents}"
                                : $get;
                        }
                    }
                case '*':
                    // "/* */" comment
                    $this->get();
                    for (;;) {
                        $get = $this->get();
                        switch ($get) {
                            case '*':
                                if ($this->peek() === '/') {
                                    $this->get();
                                    if (0 === strpos($commentContents, '!')) {
                                        // YUI Compressor style
                                        return "\n/*" . substr($commentContents, 1) . "*/\n";
                                    }
                                    return preg_match('/^@(?:cc_on|if|elif|else|end)\\b/', $commentContents)
                                        ? "/*{$commentContents}*/" // IE conditional compilation
                                        : ' ';
                                }
                                break;
                            case null:
                                throw new JSMinException('Unterminated comment.');
                        }
                        $commentContents .= $get;
                    }
                default:
                    return $get;
            }
        }
        return $get;
    }

    protected function peek()
    {
        $this->lookAhead = $this->get();
        return $this->lookAhead;
    }

    protected function spaceBeforeRegExp($output)
    {
        $length = strlen($output);
        $isSpace = false;
        $tmp = "";
        foreach (array("case", "else", "in", "return", "typeof") as $word) {
            if ($length === strlen($word)) {
                $isSpace = ($word === $output);
            } elseif ($length > strlen($word)) {
                $tmp = substr($output, $length - strlen($word) - 1);
                $isSpace = (substr($tmp, 1) === $word) && ! $this->isAlphaNum($tmp[0]);
            }
            if ($isSpace) {
                break;
            }
        }
        return ($length < 2) 
            ? true 
            : $isSpace;
    }


}