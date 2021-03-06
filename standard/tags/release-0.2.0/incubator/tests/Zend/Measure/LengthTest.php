<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Length
 */
require_once 'Zend/Measure/Length.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_LengthTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }


    /**
     * test for Length initialisation
     * expected instance
     */
    public function testLengthInit()
    {
        $value = new Zend_Measure_Length('100',Zend_Measure_Length::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Length,'Zend_Measure_Length Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testLengthUnknownType()
    {
        try {
            $value = new Zend_Measure_Length('100','Length::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testLengthUnknownValue()
    {
        try {
            $value = new Zend_Measure_Length('novalue',Zend_Measure_Length::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testLengthUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Length('100',Zend_Measure_Length::STANDARD,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testLengthNoLocale()
    {
        $value = new Zend_Measure_Length('100',Zend_Measure_Length::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Length value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testLengthValuePositive()
    {
        $value = new Zend_Measure_Length('100',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Length value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testLengthValueNegative()
    {
        $value = new Zend_Measure_Length('-100',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Length value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testLengthValueDecimal()
    {
        $value = new Zend_Measure_Length('-100,200',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Length value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testLengthValueDecimalSeperated()
    {
        $value = new Zend_Measure_Length('-100.100,200',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Length Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testLengthValueString()
    {
        $value = new Zend_Measure_Length('string -100.100,200',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Length Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testLengthEquality()
    {
        $value = new Zend_Measure_Length('string -100.100,200',Zend_Measure_Length::STANDARD,'de');
        $newvalue = new Zend_Measure_Length('otherstring -100.100,200',Zend_Measure_Length::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Length Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testLengthNoEquality()
    {
        $value = new Zend_Measure_Length('string -100.100,200',Zend_Measure_Length::STANDARD,'de');
        $newvalue = new Zend_Measure_Length('otherstring -100,200',Zend_Measure_Length::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Length Object should be not equal');
    }


    /**
     * test for serialization
     * expected string
     */
    public function testLengthSerialize()
    {
        $value = new Zend_Measure_Length('string -100.100,200',Zend_Measure_Length::STANDARD,'de');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial),'Zend_Measure_Length not serialized');
    }


    /**
     * test for unserialization
     * expected object
     */
    public function testLengthUnSerialize()
    {
        $value = new Zend_Measure_Length('string -100.100,200',Zend_Measure_Length::STANDARD,'de');
        $serial = $value->serialize();
        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Length not unserialized');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testLengthSetPositive()
    {
        $value = new Zend_Measure_Length('100',Zend_Measure_Length::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Length value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testLengthSetNegative()
    {
        $value = new Zend_Measure_Length('-100',Zend_Measure_Length::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Length value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testLengthSetDecimal()
    {
        $value = new Zend_Measure_Length('-100,200',Zend_Measure_Length::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Length value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testLengthSetDecimalSeperated()
    {
        $value = new Zend_Measure_Length('-100.100,200',Zend_Measure_Length::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Length Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testLengthSetString()
    {
        $value = new Zend_Measure_Length('string -100.100,200',Zend_Measure_Length::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Length Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testLengthSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Length('100',Zend_Measure_Length::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Length::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testLengthSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Length('100',Zend_Measure_Length::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Length::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testLengthSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Length('100',Zend_Measure_Length::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Length::STANDARD,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testLengthSetWithNoLocale()
    {
        $value = new Zend_Measure_Length('100', Zend_Measure_Length::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Length::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Length value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testLengthSetType()
    {
        $value = new Zend_Measure_Length('-100',Zend_Measure_Length::STANDARD,'de');
        $value->setType(Zend_Measure_Length::MILE);
        $this->assertEquals($value->getType(), Zend_Measure_Length::MILE, 'Zend_Measure_Length type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testLengthSetComputedType1()
    {
        $value = new Zend_Measure_Length('-100',Zend_Measure_Length::STANDARD,'de');
        $value->setType(Zend_Measure_Length::LINK);
        $this->assertEquals($value->getType(), Zend_Measure_Length::LINK, 'Zend_Measure_Length type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testLengthSetComputedType2()
    {
        $value = new Zend_Measure_Length('-100',Zend_Measure_Length::LINK,'de');
        $value->setType(Zend_Measure_Length::KEN);
        $this->assertEquals($value->getType(), Zend_Measure_Length::KEN, 'Zend_Measure_Length type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testLengthSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Length('-100',Zend_Measure_Length::STANDARD,'de');
            $value->setType('Length::UNKNOWN');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // OK
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testLengthToString()
    {
        $value = new Zend_Measure_Length('-100',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals($value->toString(), '-100 m', 'Value -100 m expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testLength_ToString()
    {
        $value = new Zend_Measure_Length('-100',Zend_Measure_Length::STANDARD,'de');
        $this->assertEquals($value->__toString(), '-100 m', 'Value -100 m expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testLengthConversionList()
    {
        $value = new Zend_Measure_Length('-100',Zend_Measure_Length::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
