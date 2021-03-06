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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: SkipTests.php 4841 2007-05-17 19:15:26Z bkarwin $
 */

require_once 'PHPUnit/Framework/TestCase.php';

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Skip_CommonTest extends PHPUnit_Framework_TestCase
{
    public $message = null;

    abstract public function getDriver();

    public function setUp()
    {
        $driver = $this->getDriver();
        $message = 'Skipping ' . $this->getDriver();
        if ($this->message) {
            $message .= ': ' . $this->message;
        }
        $this->markTestSkipped($message);
    }

    public function testDb()
    {
        // this is here only so we have at least one test
    }
}

class Zend_Db_Skip_Pdo_IbmTest extends Zend_Db_Skip_CommonTest
{
    function getDriver()
    {
        return 'Pdo_Ibm';
    }
}
