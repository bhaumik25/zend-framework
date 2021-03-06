<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    ZSearch
 * @subpackage search
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/** ZSearchQueryToken */
require_once 'Zend/Search/Lucene/search/ZSearchQueryToken.php';

/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';


/**
 * @package    ZSearch
 * @subpackage search
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZSearchQueryTokenizer implements Iterator
{
    /**
     * inputString tokens.
     *
     * @var array
     */
    protected $_tokens = array();

    /**
     * tokens pointer.
     *
     * @var integer
     */
    protected $_currToken = 0;


    /**
     * QueryTokenize constructor needs query string as a parameter.
     *
     * @param string $inputString
     */
    public function __construct($inputString)
    {
        if (!strlen($inputString)) {
            throw new Zend_Search_Lucene_Exception('Cannot tokenize empty query string.');
        }

        $currentToken = '';
        for ($count = 0; $count < strlen($inputString); $count++) {
            if (ctype_alnum( $inputString{$count} )) {
                $currentToken .= $inputString{$count};
            } else {
                // Previous token is finished
                if (strlen($currentToken)) {
                    $this->_tokens[] = new ZSearchQueryToken(ZSearchQueryToken::TOKTYPE_WORD,
                                                                $currentToken);
                    $currentToken = '';
                }

                if ($inputString{$count} == '+' || $inputString{$count} == '-') {
                    $this->_tokens[] = new ZSearchQueryToken(ZSearchQueryToken::TOKTYPE_SIGN,
                                                                $inputString{$count});
                } elseif ($inputString{$count} == '(' || $inputString{$count} == ')') {
                    $this->_tokens[] = new ZSearchQueryToken(ZSearchQueryToken::TOKTYPE_BRACKET,
                                                                $inputString{$count});
                } elseif ($inputString{$count} == ':' && $this->count()) {
                    if ($this->_tokens[count($this->_tokens)-1]->type == ZSearchQueryToken::TOKTYPE_WORD) {
                        $this->_tokens[count($this->_tokens)-1]->type = ZSearchQueryToken::TOKTYPE_FIELD;
                    }
                }
            }
        }

        if (strlen($currentToken)) {
            $this->_tokens[] = new ZSearchQueryToken(ZSearchQueryToken::TOKTYPE_WORD, $currentToken);
        }
    }


    /**
     * Returns number of tokens
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_tokens);
    }


    /**
     * Returns TRUE if a token exists at the current position.
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_currToken < $this->count();
    }


    /**
     * Resets token stream.
     *
     * @return integer
     */
    public function rewind()
    {
        $this->_currToken = 0;
    }


    /**
     * Returns the token at the current position or FALSE if
     * the position does not contain a valid token.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->valid() ? $this->_tokens[$this->_currToken] : false;
    }


    /**
     * Returns next token
     *
     * @return ZSearchQueryToken
     */
    public function next()
    {
        return ++$this->_currToken;
    }


    /**
     * Return the position of the current token.
     *
     * @return integer
     */
    public function key()
    {
        return $this->_currToken;
    }

}

