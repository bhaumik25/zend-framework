<?php
/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */


/**
 * Zend_Measure_Flow_Volume
 */
require_once 'Zend/Measure/Flow/Volume.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Measure
 * @subpackage UnitTests
 */
class Zend_Measure_Flow_VolumeTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }


    /**
     * test for Volume initialisation
     * expected instance
     */
    public function testFlow_VolumeInit()
    {
        $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertTrue($value instanceof Zend_Measure_Flow_Volume,'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_VolumeUnknownType()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100','Flow_Volume::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_VolumeUnknownValue()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('novalue',Zend_Measure_Flow_Volume::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected root value
     */
    public function testFlow_VolumeUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for standard locale
     * expected integer
     */
    public function testFlow_VolumeNoLocale()
    {
        $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD);
        $this->assertEquals(100, $value->getValue(),'Zend_Measure_Flow_Volume value expected');
    }


    /**
     * test for positive value
     * expected integer
     */
    public function testFlow_VolumeValuePositive()
    {
        $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(100, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a positive integer');
    }


    /**
     * test for negative value
     * expected integer
     */
    public function testFlow_VolumeValueNegative()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-100, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a negative integer');
    }


    /**
     * test for decimal value
     * expected float
     */
    public function testFlow_VolumeValueDecimal()
    {
        $value = new Zend_Measure_Flow_Volume('-100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-100.200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a decimal value');
    }


    /**
     * test for decimal seperated value
     * expected float
     */
    public function testFlow_VolumeValueDecimalSeperated()
    {
        $value = new Zend_Measure_Flow_Volume('-100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for string with integrated value
     * expected float
     */
    public function testFlow_VolumeValueString()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-100100.200, $value->getValue(),'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for equality
     * expected true
     */
    public function testFlow_VolumeEquality()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $newvalue = new Zend_Measure_Flow_Volume('otherstring -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Flow_Volume Object should be equal');
    }


    /**
     * test for no equality
     * expected false
     */
    public function testFlow_VolumeNoEquality()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $newvalue = new Zend_Measure_Flow_Volume('otherstring -100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertFalse($value->equals($newvalue),'Zend_Measure_Flow_Volume Object should be not equal');
    }


    /**
     * test for serialization
     * expected string
     */
    public function testFlow_VolumeSerialize()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial),'Zend_Measure_Flow_Volume not serialized');
    }


    /**
     * test for unserialization
     * expected object
     */
    public function testFlow_VolumeUnSerialize()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $serial = $value->serialize();
        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue),'Zend_Measure_Flow_Volume not unserialized');
    }


    /**
     * test for set positive value
     * expected integer
     */
    public function testFlow_VolumeSetPositive()
    {
        $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a positive integer');
    }


    /**
     * test for set negative value
     * expected integer
     */
    public function testFlow_VolumeSetNegative()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('-200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a negative integer');
    }


    /**
     * test for set decimal value
     * expected float
     */
    public function testFlow_VolumeSetDecimal()
    {
        $value = new Zend_Measure_Flow_Volume('-100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('-200,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-200.200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a decimal value');
    }


    /**
     * test for set decimal seperated value
     * expected float
     */
    public function testFlow_VolumeSetDecimalSeperated()
    {
        $value = new Zend_Measure_Flow_Volume('-100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('-200.200,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for set string with integrated value
     * expected float
     */
    public function testFlow_VolumeSetString()
    {
        $value = new Zend_Measure_Flow_Volume('string -100.100,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setValue('otherstring -200.200,200',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals(-200200.200, $value->getValue(),'Zend_Measure_Flow_Volume Object not returned');
    }


    /**
     * test for exception unknown type
     * expected exception
     */
    public function testFlow_VolumeSetUnknownType()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
            $value->setValue('otherstring -200.200,200','Flow_Volume::UNKNOWN','de');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown value
     * expected exception
     */
    public function testFlow_VolumeSetUnknownValue()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
            $value->setValue('novalue',Zend_Measure_Flow_Volume::STANDARD,'de');
            $this->assertTrue(false,'Exception expected because of empty value');
        } catch (Exception $e) {
            return; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_VolumeSetUnknownLocale()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('100',Zend_Measure_Flow_Volume::STANDARD,'de');
            $value->setValue('200',Zend_Measure_Flow_Volume::STANDARD,'nolocale');
            $this->assertTrue(false,'Exception expected because of unknown locale');
        } catch (Exception $e) {
            return true; // Test OK
        }
    }


    /**
     * test for exception unknown locale
     * expected exception
     */
    public function testFlow_VolumeSetWithNoLocale()
    {
        $value = new Zend_Measure_Flow_Volume('100', Zend_Measure_Flow_Volume::STANDARD, 'de');
        $value->setValue('200', Zend_Measure_Flow_Volume::STANDARD);
        $this->assertEquals(200, $value->getValue(), 'Zend_Measure_Flow_Volume value expected to be a positive integer');
    }


    /**
     * test setting type
     * expected new type
     */
    public function testFlow_VolumeSetType()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setType(Zend_Measure_Flow_Volume::CUSEC);
        $this->assertEquals($value->getType(), Zend_Measure_Flow_Volume::CUSEC, 'Zend_Measure_Flow_Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_VolumeSetComputedType1()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $value->setType(Zend_Measure_Flow_Volume::BARREL_PER_DAY);
        $this->assertEquals($value->getType(), Zend_Measure_Flow_Volume::BARREL_PER_DAY, 'Zend_Measure_Flow_Volume type expected');
    }


    /**
     * test setting computed type
     * expected new type
     */
    public function testFlow_VolumeSetComputedType2()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::BARREL_PER_DAY,'de');
        $value->setType(Zend_Measure_Flow_Volume::STANDARD);
        $this->assertEquals($value->getType(), Zend_Measure_Flow_Volume::STANDARD, 'Zend_Measure_Flow_Volume type expected');
    }


    /**
     * test setting unknown type
     * expected new type
     */
    public function testFlow_VolumeSetTypeFailed()
    {
        try {
            $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
            $value->setType('Flow_Volume::UNKNOWN');
            $this->assertTrue(false,'Exception expected because of unknown type');
        } catch (Exception $e) {
            return true; // OK
        }
    }


    /**
     * test toString
     * expected string
     */
    public function testFlow_VolumeToString()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals($value->toString(), '-100 m�/s', 'Value -100 m�/s expected');
    }


    /**
     * test __toString
     * expected string
     */
    public function testFlow_Volume_ToString()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $this->assertEquals($value->__toString(), '-100 m�/s', 'Value -100 m�/s expected');
    }


    /**
     * test getConversionList
     * expected array
     */
    public function testFlow_VolumeConversionList()
    {
        $value = new Zend_Measure_Flow_Volume('-100',Zend_Measure_Flow_Volume::STANDARD,'de');
        $unit  = $value->getConversionList();
        $this->assertTrue(is_array($unit), 'Array expected');
    }
}
