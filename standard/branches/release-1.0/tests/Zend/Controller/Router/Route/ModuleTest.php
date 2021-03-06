<?php
// Call Zend_Controller_Router_Route_ModuleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Router_Route_ModuleTest::main");

    $basePath = realpath(dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 4));

    set_include_path(
        $basePath . DIRECTORY_SEPARATOR . 'tests'
        . PATH_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . 'library'
        . PATH_SEPARATOR . get_include_path()
    );
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Route_Module */
require_once 'Zend/Controller/Router/Route/Module.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_Router_Route_ModuleTest extends PHPUnit_Framework_TestCase
{
    
    protected $_request; 
    protected $_dispatcher; 
    protected $route; 
    
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Router_Route_ModuleTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setParam('noErrorHandler', true)
              ->setParam('noViewRenderer', true);

        $this->_dispatcher = $front->getDispatcher();
        
        $this->_dispatcher->setControllerDirectory(array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'mod'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin',
        ));
        
        $defaults = array(
            'controller' => 'defctrl', 
            'action'     => 'defact',
            'module'     => 'default'
        );
        
        require_once 'Zend/Controller/Request/Http.php';
        $this->_request = new Zend_Controller_Request_Http();
        $front->setRequest($this->_request);
        
        $this->route = new Zend_Controller_Router_Route_Module($defaults, $this->_dispatcher, $this->_request);
    }

    public function testModuleMatch()
    {
        $values = $this->route->match('mod');
        
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
    }

    public function testModuleAndControllerMatch()
    {
        $values = $this->route->match('mod/con');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
    }

    public function testModuleControllerAndActionMatch()
    {
        $values = $this->route->match('mod/con/act');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
    }

    public function testModuleControllerActionAndParamsMatch()
    {
        $values = $this->route->match('mod/con/act/var/val/foo');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
        $this->assertTrue(isset($values['var']));
        $this->assertEquals('val', $values['var']);
        $this->assertTrue(array_key_exists('foo', $values), var_export($values, 1));
        $this->assertTrue(empty($values['foo']));
    }

    public function testControllerOnlyMatch()
    {
        $values = $this->route->match('con');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
    }

    public function testControllerOnlyAndActionMatch()
    {
        $values = $this->route->match('con/act');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
    }

    public function testControllerOnlyActionAndParamsMatch()
    {
        $values = $this->route->match('con/act/var/val/foo');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
        $this->assertTrue(isset($values['var']));
        $this->assertEquals('val', $values['var']);
        $this->assertTrue(array_key_exists('foo', $values), var_export($values, 1));
        $this->assertTrue(empty($values['foo']));
    }

    public function testModuleMatchWithControlKeysChange()
    {
        $this->_request->setModuleKey('m');
        $this->_request->setControllerKey('c');
        $this->_request->setActionKey('a');
        
        $this->route = new Zend_Controller_Router_Route_Module(array(), $this->_dispatcher, $this->_request);
        
        $values = $this->route->match('mod/ctrl');
        
        $this->assertType('array', $values);
        $this->assertSame('mod', $values['m']);
        $this->assertSame('ctrl', $values['c']);
        $this->assertSame('index', $values['a']);
    }
    
    public function testModuleMatchWithLateControlKeysChange()
    {
        $this->_request->setModuleKey('m');
        $this->_request->setControllerKey('c');
        $this->_request->setActionKey('a');
        
        $values = $this->route->match('mod/ctrl');
        
        $this->assertType('array', $values);
        $this->assertSame('mod', $values['m'], var_export(array_keys($values), 1));
        $this->assertSame('ctrl', $values['c'], var_export(array_keys($values), 1));
        $this->assertSame('index', $values['a'], var_export(array_keys($values), 1));
    }
    
    public function testAssembleNoModuleOrController()
    {
        $params = array(
            'action' => 'act',
            'foo'    => 'bar'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('defctrl/act/foo/bar', $url);
    }

    public function testAssembleControllerOnly()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);
        
        $this->assertEquals('con/act/foo/bar', $url);
    }

    public function testAssembleModuleAndController()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/con/act/foo/bar', $url);
    }

    public function testAssembleNoController()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/defctrl/act/foo/bar', $url);
    }

    public function testAssembleNoAction()
    {
        $params = array(
            'module'     => 'mod',
            'controller' => 'ctrl'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/ctrl', $url);
    }

    public function testAssembleNoActionWithParams()
    {
        $params = array(
            'foo'		 => 'bar',
            'module'     => 'mod',
            'controller' => 'ctrl'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/ctrl/defact/foo/bar', $url);
    }

    public function testAssembleNoModuleOrControllerMatched()
    {
        $this->route->match('');
        
        $params = array(
            'action' => 'act',
            'foo'    => 'bar'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('defctrl/act/foo/bar', $url);
    }
    
    public function testAssembleControllerOnlyMatched()
    {
        $this->route->match('ctrl');
        
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);
        
        $this->assertEquals('con/act/foo/bar', $url);
    }

    public function testAssembleModuleAndControllerMatched()
    {
        $this->route->match('mod/ctrl');
        
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'm'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('m/ctrl/act/foo/bar', $url);
    }

    public function testAssembleNoControllerMatched()
    {
        $this->route->match('mod');
        
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/defctrl/act/foo/bar', $url);
    }

    public function testAssembleNoActionMatched()
    {
        $this->route->match('mod/ctrl');
        
        $params = array(
            'module'     => 'def',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('def/con', $url);
    }
    
    public function testAssembleWithReset()
    {
        $values = $this->route->match('mod/con/act/sort/name');

        $url = $this->route->assemble(array('action' => 'new'), true);

        $this->assertSame('defctrl/new', $url);
    }
    
    public function testAssembleWithReset2()
    {
        $values = $this->route->match('mod/con/act/sort/name');

        $url = $this->route->assemble(array('controller' => 'new'), true);

        $this->assertSame('new', $url);
    }

    public function testAssembleWithReset3()
    {
        $values = $this->route->match('mod/con/act/sort/name');

        $url = $this->route->assemble(array('controller' => 'new', 'action' => 'test'), true);

        $this->assertSame('new/test', $url);
    }

    public function testAssembleResetOneVariable()
    {    
        $values = $this->route->match('mod/con/act');

        $url = $this->route->assemble(array('action' => null), false);

        $this->assertSame('mod/con', $url);
    }

    public function testAssembleResetOneVariable2()
    {    
        $values = $this->route->match('mod/con/act');

        $url = $this->route->assemble(array('controller' => null), false);

        $this->assertSame('mod/defctrl/act', $url);
    }

    public function testAssembleResetOneVariable3()
    {    
        $values = $this->route->match('mod/con/act');

        $url = $this->route->assemble(array('module' => null), false);

        $this->assertSame('con/act', $url);
    }
    
    public function testAssembleDefaultModuleResetZF1415()
    {
        $values = $this->route->match('con/act');
    	
    	$url = $this->route->assemble(array('controller' => 'foo', 'action' => 'bar'), true);

		$this->assertSame('foo/bar', $url);
    }
    
    public function testAssembleDefaultModuleZF1415()
    {
        $values = $this->route->match('con/act');
    	
    	$url = $this->route->assemble(array('controller' => 'foo', 'action' => 'bar'), false);

		$this->assertSame('foo/bar', $url);
    }

    public function testAssembleDefaultModuleZF1415_2()
    {
        $values = $this->route->match('default/defctrl/defact');
        $url = $this->route->assemble();
        $this->assertSame('', $url);
        
        $values = $this->route->match('mod/defctrl/defact');
        $url = $this->route->assemble();
        $this->assertSame('mod', $url);
    }
    
    public function testGetInstance()
    {
        require_once 'Zend/Config.php';

        $routeConf = array(
            'defaults' => array(
                'controller' => 'ctrl'
            )
        );
        
        $config = new Zend_Config($routeConf);
        $route = Zend_Controller_Router_Route_Module::getInstance($config);
        
        $this->assertType('Zend_Controller_Router_Route_Module', $route);
    }

}

// Call Zend_Controller_Router_Route_ModuleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Router_Route_ModuleTest::main") {
    Zend_Controller_Router_Route_ModuleTest::main();
}
