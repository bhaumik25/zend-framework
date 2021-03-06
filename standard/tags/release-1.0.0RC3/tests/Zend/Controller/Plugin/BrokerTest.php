<?php
// Call Zend_Controller_Plugin_BrokerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Plugin_BrokerTest::main");

    $basePath = realpath(dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 3));

    set_include_path(
        $basePath . DIRECTORY_SEPARATOR . 'tests'
        . PATH_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . 'library'
        . PATH_SEPARATOR . get_include_path()
    );
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';

class Zend_Controller_Plugin_BrokerTest extends PHPUnit_Framework_TestCase
{
    public $controller;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Plugin_BrokerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->controller = Zend_Controller_Front::getInstance();
        $this->controller->resetInstance();
        $this->controller->setParam('noViewRenderer', true)
                         ->setParam('noErrorHandler', true);
    }

    public function testDuplicatePlugin()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);
        try {
            $broker->registerPlugin($plugin);
            $this->fail('Duplicate registry of plugin object should be disallowed');
        } catch (Exception $expected) {
            $this->assertContains('already', $expected->getMessage());
        }
    }


    public function testUsingFrontController()
    {
        $this->controller->setControllerDirectory(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files');
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/empty');
        $this->controller->setResponse(new Zend_Controller_Response_Cli());
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $this->controller->registerPlugin($plugin);
        $this->controller->returnResponse(true);
        $response = $this->controller->dispatch($request);
        $this->assertEquals('123456', $response->getBody());
        $this->assertEquals('123456', $plugin->getResponse()->getBody());
    }

    public function testUnregisterPluginWithObject()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);
        $plugins = $broker->getPlugins();
        $this->assertEquals(1, count($plugins));
        $broker->unregisterPlugin($plugin);
        $plugins = $broker->getPlugins();
        $this->assertEquals(0, count($plugins));
    }

    public function testUnregisterPluginByClassName()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);
        $plugins = $broker->getPlugins();
        $this->assertEquals(1, count($plugins));
        $broker->unregisterPlugin('Zend_Controller_Plugin_BrokerTest_TestPlugin');
        $plugins = $broker->getPlugins();
        $this->assertEquals(0, count($plugins));
    }

    public function testGetPlugins()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);
        $plugins = $broker->getPlugins();
        $this->assertEquals(1, count($plugins));
        $this->assertSame($plugin, $plugins[0]);
    }

    public function testGetPluginByName()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);
        $retrieved = $broker->getPlugin('Zend_Controller_Plugin_BrokerTest_TestPlugin');
        $this->assertTrue($retrieved instanceof Zend_Controller_Plugin_BrokerTest_TestPlugin);
        $this->assertSame($plugin, $retrieved);
    }

    public function testGetPluginByNameReturnsFalseWithBadClassName()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);
        $retrieved = $broker->getPlugin('TestPlugin');
        $this->assertFalse($retrieved);
    }

    public function testGetPluginByNameReturnsArray()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);

        $plugin2 = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin2);

        $retrieved = $broker->getPlugin('Zend_Controller_Plugin_BrokerTest_TestPlugin');
        $this->assertTrue(is_array($retrieved));
        $this->assertEquals(2, count($retrieved));
        $this->assertSame($plugin, $retrieved[0]);
        $this->assertSame($plugin2, $retrieved[1]);
    }

    public function testHasPlugin()
    {
        $broker = new Zend_Controller_Plugin_Broker();
        $this->assertFalse($broker->hasPlugin('Zend_Controller_Plugin_BrokerTest_TestPlugin'));

        $plugin = new Zend_Controller_Plugin_BrokerTest_TestPlugin();
        $broker->registerPlugin($plugin);
        $this->assertTrue($broker->hasPlugin('Zend_Controller_Plugin_BrokerTest_TestPlugin'));
    }
}

class Zend_Controller_Plugin_BrokerTest_TestPlugin extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->appendBody('1');
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->appendBody('2');
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->appendBody('3');
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->appendBody('4');
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()->appendBody('5');
    }

    public function dispatchLoopShutdown()
    {
        $this->getResponse()->appendBody('6');
    }
}

// Call Zend_Controller_Plugin_BrokerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Plugin_BrokerTest::main") {
    Zend_Controller_Plugin_BrokerTest::main();
}
