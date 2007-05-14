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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Spreadsheets_ListQueryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->docQuery = new Zend_Gdata_Spreadsheets_ListQuery();
    }

    public function testSpreadsheetQuery()
    {
        $this->docQuery->setSpreadsheetQuery('first=john&last=smith');
        $this->assertTrue($this->docQuery->getSpreadsheetQuery() == 'first=john&last=smith');
        $this->assertTrue($this->docQuery->getQueryString() == '?sq=first%3Djohn%26last%3Dsmith');
    }
    
    public function testOrderBy()
    {
        $this->docQuery->setOrderBy('column:first');
        $this->assertTrue($this->docQuery->getOrderBy() == 'column:first');
        $this->assertTrue($this->docQuery->getQueryString() == '?orderby=column%3Afirst');
    }
    
    public function testReverse()
    {
        $this->docQuery->setReverse('true');
        $this->assertTrue($this->docQuery->getReverse() == 'true');
        $this->assertTrue($this->docQuery->getQueryString() == '?reverse=true');
    }

}
