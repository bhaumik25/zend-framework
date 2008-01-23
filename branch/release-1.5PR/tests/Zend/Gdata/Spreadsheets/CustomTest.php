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
class Zend_Gdata_Spreadsheets_CustomTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->custom = new Zend_Gdata_Spreadsheets_Extension_Custom();
    }

    public function testToAndFromString()
    {
        $this->custom->setText('value');
        $this->assertTrue($this->custom->getText() == 'value');
        $this->custom->setColumnName('column_name');
        $this->assertTrue($this->custom->getColumnName() == 'column_name');
        $newCustom = new Zend_Gdata_Spreadsheets_Extension_Custom();
        $doc = new DOMDocument();
        $doc->loadXML($this->custom->saveXML());
        $newCustom->transferFromDom($doc->documentElement);
        $this->assertTrue($this->custom->getText() == $newCustom->getText());
        $this->assertTrue($this->custom->getColumnName() == $newCustom->getColumnName());
    }

}
