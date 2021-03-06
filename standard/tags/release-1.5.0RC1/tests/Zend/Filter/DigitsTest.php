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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_Digits
 */
require_once 'Zend/Filter/Digits.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_DigitsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_Digits object extended for checking whether Unicode PCRE is enabled
     *
     * @var Zend_Filter_DigitsTest_Filter
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_Digits object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_DigitsTest_Filter();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            'abc123'  => '123',
            'abc 123' => '123',
            'abcxyz'  => '',
            'AZ@#4.3' => '43',
            '1.23'    => '123',
            '0x9f'    => '09'
            );
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }

    /**
     * Ensures that the filter follows expected behavior for multibyte characters
     *
     * @return void
     */
    public function testMultiByte()
    {
        if (!$this->_filter->getUnicodeEnabled()) {
            $this->markTestSkipped('Multibyte test not run; Unicode PCRE is not supported on this platform');
        }
        $valuesExpected = array(
            '一'  => '一'
            );
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }
}


class Zend_Filter_DigitsTest_Filter extends Zend_Filter_Digits
{
    public function getUnicodeEnabled()
    {
        return self::$_unicodeEnabled;
    }
}
