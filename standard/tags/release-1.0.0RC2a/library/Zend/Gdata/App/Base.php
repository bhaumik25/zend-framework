<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Abstract class for all XML elements
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Gdata_App_Base
{

    /**
     * @var string The XML element name, including prefix if desired
     */
    protected $_rootElement = null;

    /**
     * @var string The XML namespace prefix
     */
    protected $_rootNamespace = 'atom';

    /**
     * @var string The XML namespace URI - takes precedence over lookup up the
     * corresponding URI for $_rootNamespace
     */
    protected $_rootNamespaceURI = null;

    /**
     * @var array Leftover elements which were not handled
     */
    protected $_extensionElements = array();

    /**
     * @var array Leftover attributes which were not handled
     */
    protected $_extensionAttributes = array();

    /**
     * @var string XML child text node content
     */
    protected $_text = null;

    /**
     * @var array
     */
    protected $_namespaces = array(
        'atom'       => 'http://www.w3.org/2005/Atom',
        'app'       => 'http://purl.org/atom/app#'
    );

    public function __construct()
    {
    }

    public function getText($trim = true)
    {
        if ($trim) {
            return trim($this->_text);
        } else {
            return $this->_text;
        }
    }

    public function setText($value)
    {
        $this->_text = $value;
        return $this;
    }

    public function getExtensionElements()
    {
        return $this->_extensionElements;
    }

    public function setExtensionElements($value)
    {
        $this->_extensionElements = $value;
        return $this;
    }


    public function getExtensionAttributes()
    {
        return $this->_extensionAttributes;
    }

    public function setExtensionAttributes($value)
    {
        $this->_extensionAttributes = $value;
        return $this;
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all 
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.  
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all 
     * child properties.
     */
    public function getDOM($doc = null)
    {
        if (is_null($doc)) {
            $doc = new DOMDocument('1.0', 'utf-8');
        }
        if ($this->_rootNamespaceURI != null) { 
            $element = $doc->createElementNS($this->_rootNamespaceURI, $this->_rootElement);
        } elseif ($this->_rootNamespace !== null) {
            if (strpos($this->_rootElement, ':') === false) {
                $elementName = $this->_rootNamespace . ':' . $this->_rootElement;
            } else {
                $elementName = $this->_rootElement;
            }
            $element = $doc->createElementNS($this->lookupNamespace($this->_rootNamespace), $elementName);
        } else {
            $element = $doc->createElement($this->_rootElement);
        }
        if ($this->_text != null) {
            $element->appendChild($element->ownerDocument->createTextNode($this->_text));
        }
        foreach ($this->_extensionElements as $extensionElement) {
            $element->appendChild($extensionElement->getDOM($element->ownerDocument));
        }
        foreach ($this->_extensionAttributes as $attribute) {
            $element->setAttribute($attribute['name'], $attribute['value']);
        }
        return $element;
    }

    /**
     * Given a child DOMNode, tries to determine how to map the data into
     * object instance members.  If no mapping is defined, Extension_Element
     * objects are created and stored in an array.
     *
     * @param DOMNode $child The DOMNode needed to be handled
     */
    protected function takeChildFromDOM($child)
    {
        if ($child->nodeType == XML_TEXT_NODE) {
            $this->_text = $child->nodeValue;
        } else {
            $extensionElement = new Zend_Gdata_App_Extension_Element();
            $extensionElement->transferFromDOM($child);
            $this->_extensionElements[] = $extensionElement; 
        }
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are 
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        $arrayIndex = ($attribute->namespaceURI != '')?(
                $attribute->namespaceURI . ':' . $attribute->name):
                $attribute->name;
        $this->_extensionAttributes[$arrayIndex] =
                array('namespaceUri' => $attribute->namespaceURI,
                      'name' => $attribute->localName,
                      'value' => $attribute->nodeValue);
    }

    /**
     * Transfers each child and attribute into member variables.
     * This is called when XML is received over the wire and the data
     * model needs to be built to represent this XML.
     *
     * @param DOMNode $node The DOMNode that represents this object's data
     */
    public function transferFromDOM($node)
    {
        foreach ($node->childNodes as $child) {
            $this->takeChildFromDOM($child);
        }
        foreach ($node->attributes as $attribute) {
            $this->takeAttributeFromDOM($attribute);
        }
    }

    public function transferFromXML($xml)
    {
        if ($xml) {
            // Load the feed as an XML DOMDocument object
            @ini_set('track_errors', 1);
            $doc = new DOMDocument();
            $success = @$doc->loadXML($xml);
            @ini_restore('track_errors');
            if (!$success) {
                require_once 'Zend/Gdata/App/Exception.php';
                throw new Zend_Gdata_App_Exception("DOMDocument cannot parse XML: $php_errormsg");
            }
            $element = $doc->getElementsByTagName($this->_rootElement)->item(0);
            if (!$element) {
                require_once 'Zend/Gdata/App/Exception.php';
                throw new Zend_Gdata_App_Exception('No root <' . $this->_rootElement . '> element');
            }
            $this->transferFromDOM($element);
        } else {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new Zend_Gdata_App_Exception('XML passed to transferFromXML cannot be null');
        }
    }

    /**
     * Converts this element and all children into XML using getDOM()
     *
     * @return string XML content
     */
    public function saveXML()
    {
        $element = $this->getDOM();
        return $element->ownerDocument->saveXML($element);
    }

    /**
     * Alias for saveXML() returns XML content for this element and all
     * children
     */
    public function getXML()
    {
        return $this->saveXML();
    }

    /**
     * Get the full version of a namespace prefix
     *
     * Looks up a prefix (atom:, etc.) in the list of registered
     * namespaces and returns the full namespace URI if
     * available. Returns the prefix, unmodified, if it's not
     * registered.
     *
     * @return string
     */
    public function lookupNamespace($prefix)
    {
        return isset($this->_namespaces[$prefix]) ?
            $this->_namespaces[$prefix] :
            $prefix;
    }


    /**
     * Add a namespace and prefix to the registered list
     *
     * Takes a prefix and a full namespace URI and adds them to the
     * list of registered namespaces for use by
     * $this->lookupNamespace().
     *
     * @param  string $prefix The namespace prefix
     * @param  string $namespaceUri The full namespace URI
     * @return void
     */
    public function registerNamespace($prefix, $namespaceUri)
    {
        $this->_namespaces[$prefix] = $namespaceUri;
    }

    /**
     * Magic getter to allow acces like $entry->foo to call $entry->getFoo()
     * Alternatively, if no getFoo() is defined, but a $_foo protected variable
     * is defined, this is returned.
     *
     * TODO Remove ability to bypass getFoo() methods??
     *
     * @param string $name The variable name sought
     */
    public function __get($name)
    {
        $method = 'get'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method));
        } else if (property_exists($this, "_${name}")) {
            return $this->{'_' . $name};
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Property ' . $name . '  does not exist');
        }
    }

    /**
     * Magic setter to allow acces like $entry->foo='bar' to call 
     * $entry->setFoo('bar') automatically.  
     *
     * Alternatively, if no setFoo() is defined, but a $_foo protected variable
     * is defined, this is returned.
     *
     * TODO Remove ability to bypass getFoo() methods??
     *
     * @param string $name 
     * @param string $value
     */
    public function __set($name, $val)
    {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method), $val);
        } else if (isset($this->{'_' . $name}) || is_null($this->{'_' . $name})) {
            $this->{'_' . $name} = $val;
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Property ' . $name . '  does not exist');
        }
    }

    /**
     * Magic __isset method 
     *
     * @param string $name 
     */
    public function __isset($name)
    {
        $rc = new ReflectionClass(get_class($this));
        $privName = '_' . $name;
        if (!($rc->hasProperty($privName))) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'Property ' . $name . ' does not exist');
        } else {
            if (isset($this->{$privName})) {
                if (is_array($this->{$privName})) {
                    if (count($this->{$privName}) > 0) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Magic __unset method 
     *
     * @param string $name 
     */
    public function __unset($name)
    {
        if (isset($this->{'_' . $name})) {
            if (is_array($this->{'_' . $name})) {
                $this->{'_' . $name} = array();
            } else {
                $this->{'_' . $name} = null;
            }
        }
    }

    /**
     * Magic toString method allows using this directly via echo
     * Works best in PHP >= 4.2.0
     */
    public function __toString()
    {
        return $this->getText();
    }

}
