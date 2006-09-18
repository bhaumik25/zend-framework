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
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';

/** Zend_Search_Lucene_Index_SegmentInfo */
require_once 'Zend/Search/Lucene/Index/SegmentInfo.php';

/** Zend_Search_Lucene_Index_SegmentWriter */
require_once 'Zend/Search/Lucene/Index/SegmentWriter.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Index_SegmentWriter_StreamWriter extends Zend_Search_Lucene_Index_SegmentWriter
{
    /**
     * '.fdx'  file - Stored Fields, the field index.
     *
     * @var Zend_Search_Lucene_Storage_File
     */
    protected $_fdxFile;

    /**
     * '.fdt'  file - Stored Fields, the field data.
     *
     * @var Zend_Search_Lucene_Storage_File
     */
    protected $_fdtFile;


    /**
     * Object constructor.
     *
     * @param Zend_Search_Lucene_Storage_Directory $directory
     * @param string $name
     */
    public function __construct(Zend_Search_Lucene_Storage_Directory $directory, $name)
    {
        parent::__construct($directory, $name);
    }


    /**
     * Create stored fields files and open them for write
     */
    public function createStoredFieldsFiles()
    {
        $this->_fdxFile = $this->_directory->createFile($this->_name . '.fdx');
        $this->_fdtFile = $this->_directory->createFile($this->_name . '.fdt');

        $this->_files[] = $this->_name . '.fdx';
        $this->_files[] = $this->_name . '.fdt';
    }


    /**
     * Close segment, write it to disk and return segment info
     *
     * @return Zend_Search_Lucene_Index_SegmentInfo
     */
    public function close()
    {
        if ($this->_docCount == 0) {
            return null;
        }

        $this->_dumpFNM();


        /** @todo remove null return (used for test purposes) */
        return null;

        $this->_generateCFS();

        return new Zend_Search_Lucene_Index_SegmentInfo($this->_name,
                                                        $this->_docCount,
                                                        $this->_directory);
    }
}

