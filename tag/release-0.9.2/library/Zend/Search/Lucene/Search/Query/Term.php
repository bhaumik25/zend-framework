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
     *
     * @var array
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
     * Re-write query into primitive queries in the context of specified index
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
     * Optimize query in the context of specified index
     *
     * @param Zend_Search_Lucene $index
     * @return Zend_Search_Lucene_Search_Query
     */
    public function optimize(Zend_Search_Lucene $index)
    {
        // Check, that index contains specified term
        if (!$index->hasTerm($this->_term)) {
            return new Zend_Search_Lucene_Search_Query_Empty();
        }

        return $this;
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
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * @param Zend_Search_Lucene $reader
     */
    public function execute($reader)
    {
        $this->_docVector = array_flip($reader->termDocs($this->_term));
        $this->_termPositions = $reader->termPositions($this->_term);

        // Initialize weight if it's not done yet
        $this->_initWeight($reader);
    }

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @return array
     */
    public function matchedDocs()
    {
        return $this->_docVector;
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
        if (isset($this->_docVector[$docId])) {
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
     * Return query term
     *
     * @return Zend_Search_Lucene_Index_Term
     */
    public function getTerm()
    {
        return $this->_term;
    }

    /**
     * Returns query term
     *
     * @return array
     */
    public function getTerms()
    {
        return $this->_terms;
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

