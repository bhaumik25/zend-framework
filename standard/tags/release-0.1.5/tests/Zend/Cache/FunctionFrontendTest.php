<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
 
 /**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Frontend/Function.php';
require_once 'Zend/Cache/Backend/Test.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';

function foobar($param1, $param2) {
    echo "foobar_output($param1, $param2)";
    return "foobar_return($param1, $param2)";
}

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class Zend_Cache_FunctionFrontendTest extends PHPUnit2_Framework_TestCase {
    
    private $_instance;
    
    public function setUp()
    {
        if (!$this->_instance) {
            $this->_instance = new Zend_Cache_Frontend_Function(array());
            $this->_backend = new Zend_Cache_Backend_Test();
            $this->_instance->setBackend($this->_backend);
        }
    }
    
    public function tearDown()
    {
        unset($this->_instance);
    }
    
    public function testConstructorCorrectCall()
    {
        $options = array(
            'cacheByDefault' => false,
            'cachedFunctions' => array('foo', 'bar')
        );
        $test = new Zend_Cache_Frontend_Function($options);      
    }
    
    public function testConstructorBadCall()
    {
        $options = array(
            'cacheByDefault' => false,
            'foo' => array('foo', 'bar')
        );
        try {
            $test = new Zend_Cache_Frontend_Function($options);      
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown'); 
    }
    
    public function testCallCorrectCall1()
    {
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('bar', $return);
        $this->assertEquals('foo', $data);
    }
    
    public function testCallCorrectCall2()
    {
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('foobar', array('param3', 'param4'));
        $data = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('foobar_return(param3, param4)', $return);
        $this->assertEquals('foobar_output(param3, param4)', $data);
    }
    
    public function testCallCorrectCall3()
    {
        // cacheByDefault = false
        $this->_instance->setOption('cacheByDefault', false);
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('foobar_return(param1, param2)', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }
    
    public function testCallCorrectCall4()
    {
        // cacheByDefault = false
        // cachedFunctions = array('foobar')
        $this->_instance->setOption('cacheByDefault', false);
        $this->_instance->setOption('cachedFunctions', array('foobar'));
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('bar', $return);
        $this->assertEquals('foo', $data);
    }    
    
    public function testCallCorrectCall5()
    {
        // cacheByDefault = true
        // nonCachedFunctions = array('foobar')
        $this->_instance->setOption('cacheByDefault', true);
        $this->_instance->setOption('nonCachedFunctions', array('foobar'));
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance->call('foobar', array('param1', 'param2'));
        $data = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('foobar_return(param1, param2)', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }
    
}

?>
