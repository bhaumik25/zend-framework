<?php
// Call Zend_LayoutTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Layout_HelperTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Layout/Controller/Action/Helper/Layout.php';
require_once 'Zend/Layout.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * Test class for Zend_Layout_Controller_Action_Helper_Layout
 */
class Zend_Layout_HelperTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Layout_HelperTest");
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
        $layout = new Zend_Layout();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout($layout);
        $this->assertSame($layout, $helper->getLayout());
    }

    public function testGetLayoutCreatesLayoutObjectWhenNoPluginRegistered()
    {
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();
        $layout = $helper->getLayout();
        $this->assertTrue($layout instanceof Zend_Layout);
    }

    public function testGetLayoutPullsLayoutObjectFromRegisteredPlugin()
    {
        $layout = new Zend_Layout();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();
        $this->assertSame($layout, $helper->getLayout());
    }

    public function testSetLayoutReplacesExistingLayoutObject()
    {
        $layout = new Zend_Layout();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();
        $this->assertSame($layout, $helper->getLayout());

        $newLayout = new Zend_Layout(array('mvcEnabled' => false));
        $this->assertNotSame($layout, $newLayout);

        $helper->setLayout($newLayout);
        $this->assertSame($newLayout, $helper->getLayout());
    }

    public function testDirectFetchesLayoutObject()
    {
        $layout = new Zend_Layout();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();

        $received = $helper->direct();
        $this->assertSame($layout, $received);
    }
}

// Call Zend_Layout_HelperTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Layout_HelperTest::main") {
    Zend_Layout_HelperTest::main();
}