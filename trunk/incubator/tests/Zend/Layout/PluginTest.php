<?php
// Call Zend_LayoutTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Layout_PluginTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Layout/Controller/Plugin/Layout.php';
require_once 'Zend/Layout.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Request/Simple.php';
require_once 'Zend/Controller/Response/Cli.php';

/**
 * Test class for Zend_Layout_Controller_Plugin_Layout
 */
class Zend_Layout_PluginTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Layout_PluginTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
            Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        }
        if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testConstructorWithLayoutObject()
    {
        $layout = new Zend_Layout(array('mvcEnabled' => false));
        $plugin = new Zend_Layout_Controller_Plugin_Layout($layout);
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testGetLayoutReturnsNullWithNoLayoutPresent()
    {
        $plugin = new Zend_Layout_Controller_Plugin_Layout();
        $this->assertNull($plugin->getLayout());
    }

    public function testLayoutAccessorsWork()
    {
        $plugin = new Zend_Layout_Controller_Plugin_Layout();
        $this->assertNull($plugin->getLayout());

        $layout = new Zend_Layout(array('mvcEnabled' => false));
        $plugin->setlayout($layout);
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testGetLayoutReturnsLayoutObjectWhenPulledFromPluginBroker()
    {
        $layout = new Zend_Layout();
        $front  = Zend_Controller_Front::getInstance();
        $this->assertTrue($front->hasPlugin('Zend_Layout_Controller_Plugin_Layout'));
        $plugin = $front->getPlugin('Zend_Layout_Controller_Plugin_Layout');
        $this->assertSame($layout, $plugin->getLayout());
    }

    public function testPostDispatchRendersLayout()
    {
        $front    = Zend_Controller_Front::getInstance();
        $request  = new Zend_Controller_Request_Simple();
        $response = new Zend_Controller_Response_Cli();

        $response->setBody('Application content');
        $front->setRequest($request)
              ->setResponse($response);

        $layout = new Zend_Layout();
        $layout->setLayoutPath(dirname(__FILE__) . '/_files/layouts')
               ->setLayout('plugin.phtml')
               ->disableInflector();

        $plugin = $front->getPlugin('Zend_Layout_Controller_Plugin_Layout');
        $plugin->setResponse($response);
        $plugin->postDispatch($request);

        $body = $response->getBody();
        $this->assertContains('Application content', $body);
        $this->assertContains('Site Layout', $body);
    }
}

// Call Zend_Layout_PluginTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Layout_PluginTest::main") {
    Zend_Layout_PluginTest::main();
}
