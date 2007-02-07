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

/** Zend_Search_Lucene_Search_Weight_Boolean */
require_once 'Zend/Search/Lucene/Search/Weight/Boolean.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Search_Query_Boolean extends Zend_Search_Lucene_Search_Query
{

    /**
     * Subqueries
     * Array of Zend_Search_Lucene_Query
     *
     * @var array
     */
    private $_subqueries = array();

    /**
     * Subqueries signs.
     * If true then subquery is required.
     * If false then subquery is prohibited.
     * If null then subquery is neither prohibited, nor required
     *
     * If array is null then all subqueries are required
     *
     * @var array
     */
    private $_signs = array();

    /**
     * A score factor based on the fraction of all query subqueries
     * that a document contains.
     * float for conjunction queries
     * array of float for non conjunction queries
     *
     * @var mixed
     */
    private $_coord = null;


    /**
     * Class constructor.  Create a new Boolean query object.
     *
     * if $signs array is omitted then all subqueries are required
     * it differs from addSubquery() behavior, but should never be used
     *
     * @param array $subqueries    Array of Zend_Search_Search_Query objects
     * @param array $signs    Array of signs.  Sign is boolean|null.
     * @return void
     */
    public function __construct($subqueries = null, $signs = null)
    {
        if (is_array($subqueries)) {
            $this->_subqueries = $subqueries;

            $this->_signs = null;
            // Check if all subqueries are required
            if (is_array($signs)) {
                foreach ($signs as $sign ) {
                    if ($sign !== true) {
                        $this->_signs = $signs;
                        break;
                    }
                }
            }
        }
    }


    /**
     * Add a $subquery (Zend_Search_Lucene_Query) to this query.
     *
     * The sign is specified as:
     *     TRUE  - subquery is required
     *     FALSE - subquery is prohibited
     *     NULL  - subquery is neither prohibited, nor required
     *
     * @param  Zend_Search_Lucene_Search_Query $subquery
     * @param  boolean|null $sign
     * @return void
     */
    public function addSubquery(Zend_Search_Lucene_Search_Query $subquery, $sign=null) {
        if ($sign !== true || $this->_signs !== null) {       // Skip, if all subqueries are required
            if ($this->_signs === null) {                     // Check, If all previous subqueries are required
                foreach ($this->_subqueries as $prevSubquery) {
                    $this->_signs[] = true;
                }
            }
            $this->_signs[] = $sign;
        }

        $this->_subqueries[] = $subquery;
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
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        $query->setBoost($this->getBoost());

        foreach ($this->_subqueries as $subqueryId => $subquery) {
            $query->addSubquery($subquery->rewrite($index),
                                ($this->_signs === null)?  true : $this->_signs[$subqueryId]);
        }

        return $query;
    }

    /**
     * Returns subqueries
     *
     * @return array
     */
    public function getSubqueries()
    {
        return $this->_subqueries;
    }


    /**
     * Return subqueries signs
     *
     * @return array
     */
    public function getSigns()
    {
        return $this->_signs;
    }


    /**
     * Constructs an appropriate Weight implementation for this query.
     *
     * @param Zend_Search_Lucene $reader
     * @return Zend_Search_Lucene_Search_Weight
     */
    public function createWeight($reader)
    {
        $this->_weight = new Zend_Search_Lucene_Search_Weight_Boolean($this, $reader);
        return $this->_weight;
    }


    /**
     * Score calculator for conjunction queries (all subqueries are required)
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function _conjunctionScore($docId, $reader)
    {
        if ($this->_coord === null) {
            $this->_coord = $reader->getSimilarity()->coord(count($this->_subqueries),
                                                            count($this->_subqueries) );
        }

        $score = 0;

        foreach ($this->_subqueries as $subquery) {
            $score += $subquery->score($docId, $reader) * $this->_coord;
        }

        return $score * $this->_coord * $this->getBoost();
    }


    /**
     * Score calculator for non conjunction queries (not all subqueries are required)
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function _nonConjunctionScore($docId, $reader)
    {
        if ($this->_coord === null) {
            $this->_coord = array();

            $maxCoord = 0;
            foreach ($this->_signs as $sign) {
                if ($sign !== false /* not prohibited */) {
                    $maxCoord++;
                }
            }

            for ($count = 0; $count <= $maxCoord; $count++) {
                $this->_coord[$count] = $reader->getSimilarity()->coord($count, $maxCoord);
            }
        }

        $score = 0;
        $matchedSubqueries = 0;
        foreach ($this->_subqueries as $subqueryId => $subquery) {
            $subscore = $subquery->score($docId, $reader);

            // Prohibited
            if ($this->_signs[$subqueryId] === false && $subscore != 0) {
                return 0;
            }

            // is required, but doen't match
            if ($this->_signs[$subqueryId] === true &&  $subscore == 0) {
                return 0;
            }

            if ($subscore != 0) {
                $matchedSubqueries++;
                $score += $subscore;
            }
        }

        return $score * $this->_coord[$matchedSubqueries] * $this->getBoost();
    }

    /**
     * Execute query in context of index reader
     * It also initializes necessary internal structures
     *
     * @param Zend_Search_Lucene $reader
     */
    public function execute($reader)
    {
        // Initialize weight if it's not done yet
        $this->_initWeight($reader);

        foreach ($this->_subqueries as $subquery) {
            $subquery->execute($reader);
        }

        if ($this->_signs === null) {
            // All subqueries are required
            foreach ($this->_subqueries as $subquery) {
                // Prepare priority queue
                /** @todo implementation */
            }
        } else {
            // Prepare priority queue
            /** @todo implementation */
        }

        // While queue is not used
        $this->_matchedDocs = array();
        for ($count = 0; $count < $reader->maxDoc(); $count++) {
            $this->_matchedDocs[$count] = $count;
        }

        reset($this->_matchedDocs);
    }



    /**
     * Get next document id matching the query
     * null means the end of result set
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return integer|null
     */
    public function next()
    {
        /** @todo implementation*/
        // While queue is not used
        if (($current = each($this->_matchedDocs)) === false) {
            // end of result set
            return null;
        } else {
            return $current[0];
        }
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param Zend_Search_Lucene $reader
     * @return float
     */
    public function score($docId, $reader)
    {
        if ($this->_signs === null) {
            return $this->_conjunctionScore($docId, $reader);
        } else {
            return $this->_nonConjunctionScore($docId, $reader);
        }
    }

    /**
     * Return query terms
     *
     * @return array
     */
    public function getQueryTerms()
    {
        $terms = array();

        foreach ($this->_subqueries as $id => $subquery) {
            if ($this->_signs === null  ||  $this->_signs[$id] !== false) {
                $terms = array_merge($terms, $subquery->getQueryTerms());
            }
        }

        return $terms;
    }

    /**
     * Highlight query terms
     *
     * @param integer &$colorIndex
     * @param Zend_Search_Lucene_Document_Html $doc
     */
    public function highlightMatchesDOM(Zend_Search_Lucene_Document_Html $doc, &$colorIndex)
    {
        foreach ($this->_subqueries as $id => $subquery) {
            if ($this->_signs === null  ||  $this->_signs[$id] !== false) {
                $subquery->highlightMatchesDOM($doc, $colorIndex);
            }
        }
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        // It's used only for query visualisation, so we don't care about characters escaping

        $query = '';

        foreach ($this->_subqueries as $id => $subquery) {
            if ($id != 0) {
                $query .= ' ';
            }

            if ($this->_signs === null || $this->_signs[$id] === true) {
                $query .= '+';
            } else if ($this->_signs[$id] === false) {
                $query .= '-';
            }

            $query .= '(' . $subquery->__toString() . ')';

            if ($subquery->getBoost() != 1) {
                $query .= '^' . $subquery->getBoost();
            }
        }

        return $query;
    }
}

