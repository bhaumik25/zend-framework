<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Placeholder.php 7078 2007-12-11 14:29:33Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/** Zend_View_Exception */
require_once 'Zend/View/Exception.php';

/**
 * Zend_Layout_View_Helper_HeadMeta
 *
 * @see        http://www.w3.org/TR/xhtml1/dtds.html
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_HeadMeta extends Zend_View_Helper_Placeholder_Container_Standalone
{
    /**#@+
     * Types of attributes
     * @var array
     */
    protected $_typeKeys     = array('name', 'http-equiv');
    protected $_requiredKeys = array('content');
    protected $_modifierKeys = array('lang', 'scheme');
    /**#@-*/

    /**
     * @var string registry key
     */
    protected $_regKey = 'Zend_View_Helper_HeadMeta';

    /**
     * Constructor
     *
     * Set separator to PHP_EOL
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSeparator(PHP_EOL);
    }

    /**
     * Retrieve object instance; optionally add meta tag
     * 
     * @param  string $content 
     * @param  string $keyValue 
     * @param  string $keyType 
     * @param  array $modifiers 
     * @param  string $placement 
     * @return Zend_View_Helper_HeadMeta
     */
    public function headMeta($content = null, $keyValue = null, $keyType = 'name', $modifiers = array(), $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)
    {
        if ((null !== $content) && (null !== $keyValue)) {
            $item   = $this->createData($keyType, $keyValue, $content, $modifiers);
            $action = strtolower($placement);
            switch ($placement) {
                case 'append':
                case 'prepend':
                case 'set':
                    $this->$action($item);
                    break;
                default:
                    $this->append($item);
                    break;
            }
        }

        return $this;
    }

    protected function _normalizeType($type)
    {
        switch ($type) {
            case 'Name':
                return 'name';
            case 'HttpEquiv':
                return 'http-equiv';
            default:
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception(sprintf('Invalid type "%s" passed to _normalizeType', $type));
        }
    }

    /**
     * Overload method access
     *
     * Allows the following 'virtual' methods:
     * - appendName($keyValue, $content, $modifiers = array())
     * - offsetGetName($index, $keyValue, $content, $modifers = array())
     * - prependName($keyValue, $content, $modifiers = array())
     * - setName($keyValue, $content, $modifiers = array())
     * - appendHttpEquiv($keyValue, $content, $modifiers = array())
     * - offsetGetHttpEquiv($index, $keyValue, $content, $modifers = array())
     * - prependHttpEquiv($keyValue, $content, $modifiers = array())
     * - setHttpEquiv($keyValue, $content, $modifiers = array())
     * 
     * @param  string $method 
     * @param  array $args 
     * @return Zend_View_Helper_HeadMeta
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(?P<action>set|(pre|ap)pend|offsetSet)(?P<type>Name|HttpEquiv)$/', $method, $matches)) {
            $action = $matches['action'];
            $type   = $this->_normalizeType($matches['type']);
            $argc   = count($args);
            $index  = null;

            if ('offsetSet' == $action) {
                if (0 < $argc) {
                    $index = array_shift($args);
                    --$argc;
                }
            }

            if (2 > $argc) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception('Too few arguments provided; requires key value, and content');
            }

            if (3 > $argc) {
                $args[] = array();
            }

            $item  = $this->createData($type, $args[0], $args[1], $args[2]);

            if ('offsetSet' == $action) {
                return $this->offsetSet($index, $item);
            }

            $this->$action($item);
            return $this;
        }

        require_once 'Zend/View/Exception.php';
        throw new Zend_View_Exception(sprintf('Invalid action "%s"', $method));
    }

    /**
     * Determine if item is valid
     * 
     * @param  mixed $item 
     * @return boolean
     */
    protected function _isValid($item)
    {
        if ((!$item instanceof stdClass)
            || !isset($item->type)
            || !isset($item->content)
            || !isset($item->modifiers))
        {
            return false;
        }

        return true;
    }

    /**
     * Append
     * 
     * @param  string $value 
     * @return void
     * @throws Zend_View_Exception
     */
    public function append($value)
    {
        if (!$this->_isValid($value)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('Invalid value passed to append; please use appendMeta()');
        }

        return parent::append($value);
    }

    /**
     * OffsetSet
     * 
     * @param  string|int $index
     * @param  string $value 
     * @return void
     * @throws Zend_View_Exception
     */
    public function offsetSet($index, $value)
    {
        if (!$this->_isValid($value)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('Invalid value passed to offsetSet; please use offsetSetMeta()');
        }

        return parent::offsetSet($index, $value);
    }

    /**
     * Prepend
     * 
     * @param  string $value 
     * @return void
     * @throws Zend_View_Exception
     */
    public function prepend($value)
    {
        if (!$this->_isValid($value)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('Invalid value passed to prepend; please use prependMeta()');
        }

        return parent::prepend($value);
    }

    /**
     * Set
     * 
     * @param  string $value 
     * @return void
     * @throws Zend_View_Exception
     */
    public function set($value)
    {
        if (!$this->_isValid($value)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('Invalid value passed to set; please use setMeta()');
        }

        return parent::set($value);
    }

    /**
     * Build meta HTML string
     * 
     * @param  string $type 
     * @param  string $typeValue 
     * @param  string $content 
     * @param  array $modifiers 
     * @return string
     */
    public function itemToString(stdClass $item)
    {
        if (!in_array($item->type, $this->_typeKeys)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception(sprintf('Invalid type "%s" provided for meta', $item->type));
        }
        $type = $item->type;

        $modifiersString = '';
        foreach ($item->modifiers as $key => $value) {
            if (!in_array($key, $this->_modifierKeys)) {
                continue;
            }
            $modifiersString .= $key . '="' . htmlentities($value) . '" ';
        }

        $meta = sprintf(
            '<meta %s="%s" content="%s" %s/>',
            $type,
            htmlentities($item->$type),
            htmlentities($item->content),
            $modifiersString
        );
        return $meta;
    }

    /**
     * Render placeholder as string
     * 
     * @param  string|int $indent 
     * @return string
     */
    public function toString($indent = null)
    {
        $indent = (null !== $indent)
                ? $this->_getWhitespace($indent)
                : $this->getIndent();

        $items = array();
        foreach ($this as $item) {
            $items[] = $this->itemToString($item);
        }
        return $indent . implode($this->getSeparator() . $indent, $items);
    }

    /**
     * Create data item for inserting into stack
     * 
     * @param  string $type 
     * @param  string $typeValue 
     * @param  string $content 
     * @param  array $modifiers 
     * @return stdClass
     */
    public function createData($type, $typeValue, $content, array $modifiers)
    {
        $data            = new stdClass;
        $data->type      = $type;
        $data->$type     = $typeValue;
        $data->content   = $content;
        $data->modifiers = $modifiers;
        return $data;
    }
}
