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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Validate_NotEmpty
 */
require_once 'Zend/Validate/NotEmpty.php';


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_NotEmptyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate_NotEmpty object
     *
     * @var Zend_Validate_NotEmpty
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_NotEmpty object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_NotEmpty();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('word', true),
            array('', false),
            array(1, true),
            array(0, false),
            array(true, true),
            array(false, false),
            array(null, false),
        );
        foreach ($valuesExpected as $i => $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]),
                "Failed test #$i");
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }
}
