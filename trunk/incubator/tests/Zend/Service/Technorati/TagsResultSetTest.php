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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: BlogInfoResultTest.php 7239 2007-12-23 17:05:46Z weppos $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TechnoratiTestHelper.php';

/**
 * @see Zend_Service_Technorati_TagsResultSet
 */
require_once 'Zend/Service/Technorati/TagsResultSet.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_TagsResultSetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->dom = Zend_Service_Technorati_TechnoratiTestHelper::getTestFileContentAsDom('TestTagsResultSet.xml');
        $this->object = new Zend_Service_Technorati_TagsResultSet($this->dom);
    }

    public function testConstruct()
    {
        try {
            $object = new Zend_Service_Technorati_TagsResultSet($this->dom);
            $this->assertType('Zend_Service_Technorati_TagsResultSet', $object);
        } catch (Exception $e) {
            $this->fail("Exception" . $e->getMessage() . " thrown");
        }
    }

    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        try {
            $object = new Zend_Service_Technorati_TagsResultSet('foo');
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Exception $e) {
            $this->assertContains("DOMDocument", $e->getMessage());
        }
    }

    public function testTagsResultSet()
    {
        // check valid object
        $this->assertNotNull($this->object);
        $object = $this->object;

        // check counts
        $this->assertType('integer', $object->totalResultsReturned);
        $this->assertEquals(3, $object->totalResultsReturned);
        $this->assertType('integer', $object->totalResultsAvailable);
        $this->assertEquals(3, $object->totalResultsAvailable);
    }
}
