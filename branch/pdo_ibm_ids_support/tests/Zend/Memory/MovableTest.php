<?php
/**
 * @package    Zend_Memory
 * @subpackage UnitTests
 */

error_reporting( E_ALL | E_STRICT ); // now required for each test suite

/** Zend_Memory */
require_once 'Zend/Memory.php';


/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework.php';


/**
 * Memory value container
 *
 * (Should be presented for value object)
 */
class Zend_Memory_Manager_Dummy extends Zend_Memory_Manager
{
    /** @var boolean */
    public $processUpdatePassed = false;

    /** @var integer */
    public $processedId;

    /** @var Zend_Memory_Container_Movable */
    public $processedObject;

    /**
     * Dummy object constructor
     */
    public function __construct()
    {
        // Do nothing
    }

    /**
     * Dummy value update callback method
     */
    public function processUpdate(Zend_Memory_Container_Movable $container, $id)
    {
        $this->processUpdatePassed = true;
        $this->processedId         = $id;
        $this->processedObject     = $container;
    }
}


/**
 * @package    Zend_Memory
 * @subpackage UnitTests
 */
class Zend_Memory_Container_MovableTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memoryManager = new Zend_Memory_Manager_Dummy();
        $memObject = new Zend_Memory_Container_Movable($memoryManager, 10, '0123456789');

        $this->assertTrue($memObject instanceof Zend_Memory_Container_Movable);
    }


    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memoryManager = new Zend_Memory_Manager_Dummy();
        $memObject = new Zend_Memory_Container_Movable($memoryManager, 10, '0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        if (version_compare(PHP_VERSION, '5.2') < 0) {
            // Skip next tests for PHP versions before 5.2
            return;
        }

        // value property
        $this->assertEquals((string)$memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string)$memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertTrue($memObject->value instanceof Zend_Memory_Value);
        $this->assertEquals((string)$memObject->value, 'another value');
    }


    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memoryManager = new Zend_Memory_Manager_Dummy();
        $memObject = new Zend_Memory_Container_Movable($memoryManager, 10, '0123456789');

        $this->assertFalse((boolean)$memObject->isLocked());

        $memObject->lock();
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->unlock();
        $this->assertFalse((boolean)$memObject->isLocked());
    }

    /**
     * tests the touch() method
     */
    public function testTouch()
    {
        $memoryManager = new Zend_Memory_Manager_Dummy();
        $memObject = new Zend_Memory_Container_Movable($memoryManager, 10, '0123456789');

        $this->assertFalse($memoryManager->processUpdatePassed);

        $memObject->touch();

        $this->assertTrue($memoryManager->processUpdatePassed);
        $this->assertTrue($memoryManager->processedObject === $memObject);
        $this->assertEquals($memoryManager->processedId, 10);
    }

    /**
     * tests the value update tracing
     */
    public function testValueUpdateTracing()
    {
        if (version_compare(PHP_VERSION, '5.2') < 0) {
            // Skip next tests for PHP versions before 5.2
            return;
        }

        $memoryManager = new Zend_Memory_Manager_Dummy();
        $memObject = new Zend_Memory_Container_Movable($memoryManager, 10, '0123456789');

        // startTrace() method is usually invoked by memory manager, when it need to be notified
        // about value update
        $memObject->startTrace();

        $this->assertFalse($memoryManager->processUpdatePassed);

        $memObject->value[6] = '_';

        $this->assertTrue($memoryManager->processUpdatePassed);
        $this->assertTrue($memoryManager->processedObject === $memObject);
        $this->assertEquals($memoryManager->processedId, 10);
    }
}