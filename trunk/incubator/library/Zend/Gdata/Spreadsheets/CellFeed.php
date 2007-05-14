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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Atom.php 4300 2007-04-02 13:35:41Z slaanesh $
 */

/**
 * @see Zend_Gdata_Feed
 */
require_once 'Zend/Gdata/Feed.php';

/**
 * @see Zend_Gdata_Spreadsheets_Extension_RowCount
 */
require_once 'Zend/Gdata/Spreadsheets/Extension/RowCount.php';

/**
 * @see Zend_Gdata_Spreadsheets_Extension_ColCount
 */
require_once 'Zend/Gdata/Spreadsheets/Extension/ColCount.php';

/**
 *
 * @category   Zend
 * @package    Zend_Gdata_Spreadsheets
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Spreadsheets_CellFeed extends Zend_Gdata_Feed
{

    /**
    * The classname for individual feed elements.
    *
    * @var string
    */
    protected $_entryClassName = 'Zend_Gdata_Spreadsheets_CellEntry';
  
    /**
    * The classname for the feed.
    *
    * @var string
    */
    protected $_feedClassName = 'Zend_Gdata_Spreadsheets_CellFeed';

    protected $_rowCount = null;
    protected $_colCount = null;

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->rowCount != null) {
            $element->appendChild($this->_rowCount->getDOM($element->ownerDocument));
        }
        if ($this->colCount != null) {
            $element->appendChild($this->_colCount->getDOM($element->ownerDocument));
        }
        return $element;
    }
    
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case Zend_Gdata_App_Data::lookupNamespace('gs') . ':' . 'rowCount';
                $rowCount = new Zend_Gdata_Spreadsheets_Extension_RowCount();
                $rowCount->transferFromDOM($child);
                $this->_rowCount = $rowCount;
                break;
            case Zend_Gdata_App_Data::lookupNamespace('gs') . ':' . 'colCount';
                $colCount = new Zend_Gdata_Spreadsheets_Extension_ColCount();
                $colCount->transferFromDOM($child);
                $this->_colCount = $colCount;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }
    
    public function getRowCount()
    {
        return $this->_rowCount;
    }
    
    public function getColumnCount()
    {
        return $this->_colCount;
    }
    
    public function setRowCount($rowCount)
    {
        $this->_rowCount = $rowCount;
        return $this;
    }
    
    public function setColumnCount($colCount)
    {
        $this->_colCount = $colCount;
        return $this;
    }
    
    public function issetRowCount()
    {
        return isset($this->_rowCount);
    }
    
    public function issetColumnCount()
    {
        return isset($this->_colCount);
    }
    
    public function unsetRowCount()
    {
        $this->_rowCount = null;
        return $this;
    }
    
    public function unsetColumnCount()
    {
        $this->_colCount = null;
        return $this;
    }
    
}