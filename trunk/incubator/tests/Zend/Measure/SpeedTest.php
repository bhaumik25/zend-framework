<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Speed
 */
require_once 'Zend/Measure/Speed.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_SpeedTest extends PHPUnit2_Framework_TestCase
{

    public function setUp()
    {
    }


    /**
     * test for Speed initialisation
     * expected instance
     */
    public function testSpeedInit()
    {
        $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Speed,'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testSpeedUnknownType()
    {
        try {
            $value = new Zend_Measure_Speed('100','Speed::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testSpeedUnknownValue()
    {
        try {
            $value = new Zend_Measure_Speed('novalue',Zend_Measure_Speed::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testSpeedUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testSpeedValuePositive()
    {
        $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Speed value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testSpeedValueNegative()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Speed value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testSpeedValueDecimal()
    {
        $value = new Zend_Measure_Speed('-100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Speed value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testSpeedValueDecimalSeperated()
    {
        $value = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testSpeedValueString()
    {
        $value = new Zend_Measure_Speed('string -100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testSpeedEquality()
    {
        $value = new Zend_Measure_Speed('string -100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $newvalue = new Zend_Measure_Speed('otherstring -100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Speed Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testSpeedNoEquality()
    {
        $value = new Zend_Measure_Speed('string -100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $newvalue = new Zend_Measure_Speed('otherstring -100,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Speed Object should be not equal');
    }


    /**
     * test for serialization
     * expected string
     */
    public function testSpeedSerialize()
    {
        $value = new Zend_Measure_Speed('string -100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial),'Zend_Measure_Speed not serialized');
    }


    /**
     * test for unserialization
     * expected object
     */
    public function testSpeedUnSerialize()
    {
        $value = new Zend_Measure_Speed('string -100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $serial = $value->serialize();
        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Speed not unserialized');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testSpeedSetPositive()
    {
        $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Speed value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testSpeedSetNegative()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Speed value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testSpeedSetDecimal()
    {
        $value = new Zend_Measure_Speed('-100,200',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Speed value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testSpeedSetDecimalSeperated()
    {
        $value = new Zend_Measure_Speed('-100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testSpeedSetString()
    {
        $value = new Zend_Measure_Speed('string -100.100,200',Zend_Measure_Speed::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Speed Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testSpeedSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Speed::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testSpeedSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Speed::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testSpeedSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Speed('100',Zend_Measure_Speed::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Speed::STANDARD,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test setting type
     * expected new type
     */
    public function testSpeedSetType()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $value->setType(Zend_Measure_Speed::METER_PER_HOUR);
        $this->assertEquals($value->getType(), Zend_Measure_Speed::METER_PER_HOUR, 'Zend_Measure_Speed type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testSpeedSetComputedType1()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $value->setType(Zend_Measure_Speed::METER_PER_HOUR);
        $this->assertEquals($value->getType(), Zend_Measure_Speed::METER_PER_HOUR, 'Zend_Measure_Speed type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testSpeedSetComputedType2()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::METER_PER_HOUR,'de');
        $value->setType(Zend_Measure_Speed::STANDARD);
        $this->assertEquals($value->getType(), Zend_Measure_Speed::STANDARD, 'Zend_Measure_Speed type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testSpeedSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
            $value->setType('Speed::UNKNOWN');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // OK
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testSpeedToString()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals($value->toString(), '-100 m/s', 'Value -100 m/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testSpeed_ToString()
    {
        $value = new Zend_Measure_Speed('-100',Zend_Measure_Speed::STANDARD,'de');
        $this->assertEquals($value->__toString(), '-100 m/s', 'Value -100 m/s expected');
    }
}
