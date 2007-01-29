<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Search_Query */
require_once 'Zend/Search/Lucene/Search/Query.php';

/** Zend_Search_Lucene_Search_Weight_Term */
require_once 'Zend/Search/Lucene/Search/Weight/Term.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Search_Query_Term extends Zend_Search_Lucene_Search_Query
{
    /**
     * Term to find.
     *
     * @var Zend_Search_Lucene_Index_Term
     */
    private $_term;

    /**
     * Documents vector.
     * Bitset or array of document IDs
     * (depending from Bitset extension availability).
     *
     * @var mixed
     */
    private $_docVector = null;

    /**
     * Term positions vector.
     * Array: docId => array( pos1, pos2, ... )
     *
     * @var array
     */
    private $_termPositions;


    /**
     * Zend_Search_Lucene_Search_Query_Term constructor
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @param boolean $sign
     */
    public function __construct($term)
    {
        $this->_term = $term;
    }

    /**
     * Re-write queries into primitive queries
     * Also used for query optimization and binding to the index
     *
     * @param Zend_Search_Lucene $index
     * @return Zend_Search_Lucene_Search_Query
     */
    public function rewrite(Zend_Search_Lucene $index)
    {
        if ($this->_term->field != null) {
            return $this;
        } else {
            $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
            $query->setBoost($this->getBoost());

            foreach ($index->getFieldNames(true) as $fieldName) {
                $term = new Zend_Search_Lucene_Index_Term($this->_term->text, $fieldName);

                $query->addTerm($term);
            }

            return $query->rewrite($index);
        }
    }


    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param Zend_Search_Lucene $reader
     * @return Zend_Search_Lucene_Search_Weight
     */
    public function createWeight($reader)
    {
        $this->_weight = new Zend_Search_Lucene_Search_Weight_Term($this->_term, $this, $reader);
        return $this->_weight;
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function score( $docId, $reader )
    {
        if($this->_docVector===null) {
            if (extension_loaded('bitset')) {
                $this->_docVector = bitset_from_array( $reader->termDocs($this->_term) );
            } else {
                $this->_docVector = array_flip($reader->termDocs($this->_term));
            }

            $this->_termPositions = $reader->termPositions($this->_term);
            $this->_initWeight($reader);
        }

        $match = extension_loaded('bitset') ?  bitset_in($this->_docVector, $docId) :
                                               isset($this->_docVector[$docId]);
        if ($match) {
            return $reader->getSimilarity()->tf(count($this->_termPositions[$docId]) ) *
                   $this->_weight->getValue() *
                   $reader->norm($docId, $this->_term->field) *
                   $this->getBoost();
        } else {
            return 0;
        }
    }

    /**
     * Return query terms
     *
     * @return array
     */
    public function getQueryTerms()
    {
        return array($this->_term);
    }

    /**
     * Highlight query terms
     *
     * @param integer &$colorIndex
     * @param Zend_Search_Lucene_Document_Html $doc
     */
    public function highlightMatchesDOM(Zend_Search_Lucene_Document_Html $doc, &$colorIndex)
    {
        $doc->highlight($this->_term->text, $this->_getHighlightColor($colorIndex));
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        // It's used only for query visualisation, so we don't care about characters escaping
        return (($this->_term->field === null)? '':$this->_term->field . ':') . $this->_term->text;
    }
}

