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
 * @category     Zend
 * @package        Zend_Gdata
 * @copyright    Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd         New BSD License
 * @version        $Id: Entry.php 3941 2007-03-14 21:36:13Z darby $
 */


/**
 * @see Zend_Gdata_EntryAtom
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Gdata_Spreadsheets_Extension_Cell
 */
require_once 'Zend/Gdata/Spreadsheets/Extension/Cell.php';

/**
 * Concrete class for working with Cell entries.
 *
 * @category     Zend
 * @package        Zend_Gdata_Spreadsheets
 * @copyright    Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd         New BSD License
 */
class Zend_Gdata_Spreadsheets_CellEntry extends Zend_Gdata_Entry
{

    protected $_entryClassName = 'Zend_Gdata_Spreadsheets_CellEntry';

    protected $_cell;
    
    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_cell != null) {
            $element->appendChild($this->_cell->getDOM($element->ownerDocument));
        }
        return $element;
    }
    
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case Zend_Gdata_Data::lookupNamespace('gs') . ':' . 'cell';
            $cell = new Zend_Gdata_Spreadsheets_Extension_Cell();
            $cell->transferFromDOM($child);
            $this->_cell = $cell;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }
    
    public function getCell() {
        return $this->_cell;
    }
    
    public function setCell($cell) {
        $this->_cell = $cell;
        return $this;
    }

}