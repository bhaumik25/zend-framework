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
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Decorator_Interface */
require_once 'Zend/Form/Decorator/Interface.php';

/**
 * Zend_Form_Decorator_Abstract
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
abstract class Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Interface
{
    /**#@+
     * Placement constants
     * @const string
     */
    const APPEND  = 'APPEND';
    const PREPEND = 'PREPEND';
    /**#@-*/

    /**
     * Default placement: append
     * @var string
     */
    protected $_placement = 'APPEND';

    /** 
     * @var Zend_Form_Element|Zend_Form
     */
    protected $_element;

    /**
     * Decorator options
     * @var array
     */
    protected $_options = array();

    /**
     * Separator between new content and old
     * @var string
     */
    protected $_separator = PHP_EOL;

    /**
     * Constructor
     * 
     * @param  array|Zend_Config $options 
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Set options
     * 
     * @param  array $options 
     * @return Zend_Form_Decorator_Element_Label
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Set options from config object
     * 
     * @param  Zend_Config $config 
     * @return Zend_Form_Decorator_Element_Label
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Retrieve label options
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set current form element
     * 
     * @param  Zend_Form_Element|Zend_Form $element 
     * @return Zend_Form_Decorator_Abstract
     * @throws Zend_Form_Decorator_Exception on invalid element type
     */
    public function setElement($element)
    {
        if ((!$element instanceof Zend_Form_Element)
            && (!$element instanceof Zend_Form)
            && (!$element instanceof Zend_Form_DisplayGroup))
        {
            require_once 'Zend/Form/Decorator/Exception.php';
            throw new Zend_Form_Decorator_Exception('Invalid element type passed to decorator');
        }

        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve current element
     * 
     * @return Zend_Form_Element|Zend_Form
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Determine if decorator should append or prepend content
     * 
     * @return string
     */
    public function getPlacement()
    {
        $placement = $this->_placement;
        $options   = $this->getOptions();
        if (isset($options['placement'])) {
            $options['placement'] = strtoupper($options['placement']);
            switch ($options['placement']) {
                case self::APPEND:
                case self::PREPEND:
                    $placement = $options['placement'];
                    break;
                default:
                    break;
            }
            unset($options['placement']);
            $this->setOptions($options);
        }

        return $placement;
    }

    /**
     * Retrieve separator to use between old and new content
     * 
     * @return string
     */
    public function getSeparator()
    {
        $separator = $this->_separator;
        $options   = $this->getOptions();
        if (isset($options['separator'])) {
            $separator = (string) $options['separator'];
            unset($options['separator']);
            $this->setOptions($options);
        }
        return $separator;
    }

    /**
     * Decorate content and/or element
     * 
     * @param  string $content
     * @return string
     * @throws Zend_Dorm_Decorator_Exception when unimplemented
     */
    public function render($content)
    {
        require_once 'Zend/Form/Decorator/Exception.php';
        throw new Zend_Form_Decorator_Exception('render() not implemented');
    }
}