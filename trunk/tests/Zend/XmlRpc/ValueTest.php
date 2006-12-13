<?php
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Test case for Zend_XmlRpc_Value
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id: ServerTest.php 1977 2006-11-30 22:18:12Z matthew $
 */
class Zend_XmlRpc_ValueTest extends PHPUnit_Framework_TestCase 
{
    // Boolean
    
    public function testFactoryAutodetectsBoolean()
    {
        foreach (array(true, false) as $native) {
            $val = Zend_XmlRpc_Value::getXmlRpcValue($native);
            $this->assertXmlRpcType('boolean', $val);
        }
    }

    public function testMarshalBooleanFromNative()
    {
        $native = true;
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native, 
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_BOOLEAN);
                    
        $this->assertXmlRpcType('boolean', $val);
        $this->assertSame($native, $val->getValue());
    }

    public function testMarshalBooleanFromXmlRpc()
    {
        $xml = '<value><boolean>1</boolean></value>';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('boolean', $val);
        $this->assertEquals('boolean', $val->getType());
        $this->assertSame(true, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->getAsXML());
    }    

    // Integer

    public function testFactoryAutodetectsInteger()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue(1);
        $this->assertXmlRpcType('integer', $val);
    }

    public function testMarshalIntegerFromNative()
    {
        $native = 1;
        $types = array(Zend_XmlRpc_Value::XMLRPC_TYPE_I4,
                       Zend_XmlRpc_Value::XMLRPC_TYPE_INTEGER);
        
        foreach ($types as $type) {
            $val = Zend_XmlRpc_Value::getXmlRpcValue($native, $type);
            $this->assertXmlRpcType('integer', $val);
            $this->assertSame($native, $val->getValue());
        }
    }

    public function testMarshalIntegerFromXmlRpc()
    {
        $native = 1;
        $xmls = array("<value><int>$native</int></value>",
                      "<value><i4>$native</i4></value>");

        foreach ($xmls as $xml) {
            $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                        Zend_XmlRpc_Value::XML_STRING);

            $this->assertXmlRpcType('integer', $val);
            $this->assertEquals('int', $val->getType());
            $this->assertSame($native, $val->getValue());
            $this->assertType('DomElement', $val->getAsDOM());
            $this->assertEquals($this->wrapXml($xml), $val->getAsXML());
        }
    }

    // Double

    public function testFactoryAutodetectsFloat()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue((float)1);
        $this->assertXmlRpcType('double', $val);
    }
    
    public function testMarshalDoubleFromNative()
    {
        $native = 1.1;
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native, 
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_DOUBLE);
                    
        $this->assertXmlRpcType('double', $val);
        $this->assertSame($native, $val->getValue());        
    }
    
    public function testMarshalDoubleFromXmlRpc()
    {
        $native = 1.1;
        $xml = "<value><double>$native</double></value>";
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('double', $val);
        $this->assertEquals('double', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->getAsXML());        
    }

    // String

    public function testFactoryAutodetectsString()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue('');
        $this->assertXmlRpcType('string', $val);
    }


    public function testMarshalStringFromNative()
    {
        $native = 'foo';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native, 
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_STRING);
                    
        $this->assertXmlRpcType('string', $val);
        $this->assertSame($native, $val->getValue());        
    }

    public function testMarshalStringFromXmlRpc()
    {
        $native = 'foo';
        $xml = "<value><string>$native</string></value>";
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertEquals('string', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->getAsXML());        
    }

    // Array

    public function testFactoryAutodetectsArray()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue(array(0, 'foo'));
        $this->assertXmlRpcType('array', $val);
    }
    
    public function testMarshalArrayFromNative()
    {
        $native = array(0,1);
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native, 
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_ARRAY);
                    
        $this->assertXmlRpcType('array', $val);
        $this->assertSame($native, $val->getValue()); 
    }
    
    public function testMarshalArrayFromXmlRpc()
    {
        $native = array(0,1);
        $xml = '<value><array><data><value><int>0</int></value>'
             . '<value><int>1</int></value></data></array></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('array', $val);
        $this->assertEquals('array', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->getAsXML());   
    }
    
    // Struct

    public function testFactoryAutodetectsStruct()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue(array('foo' => 0));
        $this->assertXmlRpcType('struct', $val);
    }

    public function testFactoryAutodetectsStructFromObject()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue((object)array('foo' => 0));
        $this->assertXmlRpcType('struct', $val);
    }

    public function testMarshalStructFromNative()
    {
        $native = array('foo' => 0);
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native, 
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_STRUCT);
                    
        $this->assertXmlRpcType('struct', $val);
        $this->assertSame($native, $val->getValue()); 
    }

    public function testMarshalStructFromXmlRpc()
    {
        $native = array('foo' => 0);
        $xml = '<value><struct><member><name>foo</name><value><int>0</int>'
             . '</value></member></struct></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->getAsXML());         
    }
    
    // DateTime

    public function testMarshalDateTimeFromNativeString()
    {   
        $native = '1997-07-16T19:20+01:00';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);
                    
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertSame(strtotime($native), $val->getValue()); 
    }

    public function testMarshalDateTimeFromNativeInteger()
    {   
        $native = strtotime('1997-07-16T19:20+01:00');
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);
                    
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertSame($native, $val->getValue()); 
    }

    public function testMarshalDateTimeFromXmlRpc()
    {
        $iso8601 = '1997-07-16T19:20+01:00';
        $xml = "<value><dateTime.iso8601>$iso8601</dateTime.iso8601></value>";
        
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame(strtotime($iso8601), $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->getAsXML());                    
    }

    // Base64

    public function testMarshalBase64FromString()
    {   
        $native = 'foo';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_BASE64);
                    
        $this->assertXmlRpcType('base64', $val);
        $this->assertSame($native, $val->getValue()); 
    }
    
    public function testMarshalBase64FromXmlRpc()
    {
        $native = 'foo';
        $xml = '<value><base64>' .base64_encode($native). '</base64></value>';
        
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('base64', $val);
        $this->assertEquals('base64', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->getAsXML());                    
    }    
    
    // Exceptions
    
    public function testFactoryThrowsWhenInvalidTypeSpecified()
    {
        try {
            Zend_XmlRpc_Value::getXmlRpcValue('', 'bad type here');
            $this->fail();
        } catch (Exception $e) {
            $this->assertRegexp('/given type is not/i', $e->getMessage());
        }
    }

    // Custom Assertions and Helper Methods

    public function assertXmlRpcType($type, $object)
    {
        $type = 'Zend_XmlRpc_Value_' . ucfirst($type);
        $this->assertType($type, $object);
    }
    
    public function wrapXml($xml)
    {
        return "<?xml version=\"1.0\"?>\n$xml\n";
    }
}
