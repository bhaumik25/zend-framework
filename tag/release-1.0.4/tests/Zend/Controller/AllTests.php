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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Controller_AllTests::main');
}

require_once 'ActionTest.php';
require_once 'Action/HelperBrokerTest.php';
require_once 'Action/Helper/FlashMessengerTest.php';
require_once 'Action/Helper/RedirectorTest.php';
require_once 'Action/Helper/UrlTest.php';
require_once 'Action/Helper/ViewRendererTest.php';
require_once 'Dispatcher/StandardTest.php';
require_once 'FrontTest.php';
require_once 'Plugin/BrokerTest.php';
require_once 'Plugin/ErrorHandlerTest.php';
require_once 'Request/Apache404Test.php';
require_once 'Request/HttpTest.php';
require_once 'Response/HttpTest.php';
require_once 'Router/RouteTest.php';
require_once 'Router/Route/ModuleTest.php';
require_once 'Router/Route/RegexTest.php';
require_once 'Router/Route/StaticTest.php';
require_once 'Router/RewriteTest.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Controller');

        $suite->addTestSuite('Zend_Controller_ActionTest');
        $suite->addTestSuite('Zend_Controller_Action_HelperBrokerTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_FlashMessengerTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_RedirectorTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_UrlTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_ViewRendererTest');
        $suite->addTestSuite('Zend_Controller_Dispatcher_StandardTest');
        $suite->addTestSuite('Zend_Controller_FrontTest');
        $suite->addTestSuite('Zend_Controller_Plugin_BrokerTest');
        $suite->addTestSuite('Zend_Controller_Plugin_ErrorHandlerTest');
        $suite->addTestSuite('Zend_Controller_Request_Apache404Test');
        $suite->addTestSuite('Zend_Controller_Request_HttpTest');
        $suite->addTestSuite('Zend_Controller_Response_HttpTest');
        $suite->addTestSuite('Zend_Controller_Router_RouteTest');
        $suite->addTestSuite('Zend_Controller_Router_Route_ModuleTest');
        $suite->addTestSuite('Zend_Controller_Router_Route_RegexTest');
        $suite->addTestSuite('Zend_Controller_Router_Route_StaticTest');
        $suite->addTestSuite('Zend_Controller_Router_RewriteTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Controller_AllTests::main') {
    Zend_Controller_AllTests::main();
}
