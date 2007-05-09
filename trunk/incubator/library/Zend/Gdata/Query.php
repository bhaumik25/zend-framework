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
 * Zend_Gdata_App_Exception
 */
require_once 'Zend/Gdata/App/Exception.php';

/**
 * Zend_Gdata_App_HttpException
 */
require_once 'Zend/Gdata/App/HttpException.php';

/**
 * Zend_Gdata_App_InvalidArgumentException
 */
require_once 'Zend/Gdata/App/InvalidArgumentException.php';

/**
 * Zend_Gdata_App_Util
 */
require_once 'Zend/Gdata/App/Util.php';

/**
 * Provides a mechanism to build a query URL for GData services.
 * Queries are not defined for APP, but are provided by GData services
 * as an extension.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Query
{

    /**
     * Query parameters.
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Default URI to which to POST.
     *
     * @var string
     */
    protected $_defaultPostUri = null;

    /**
     * Create Gdata_Query object
     */
    public function __construct()
    {
    }

    /**
     * @returns string querystring
     */
    protected function getQueryString()
    {
        $queryArray = array();
        foreach ($this->_params as $name => $value) {
            if (substr($name, 0, 1) == '_') {
                continue;
            }
            $queryArray[] = urlencode($name) . '=' . urlencode($value);
        }
        if (count($queryArray) > 0) {
            return '?' . implode('&', $queryArray);
        } else {
            return '';
        }
    }

    /**
     *
     */
    public function resetParameters()
    {
        $this->_params = array();
    }

    /**
     * @returns string url
     */
    public function getQueryUrl()
    {
        if ($uri == null) {
            $uri = $this->_defaultFeedUri; 
        }
        $uri .= $this->getQueryString();
        return $uri;
    }

    /**
     * @param string $name
     * @param string $value
     * @return Zend_Gdata_Query Provides a fluent interface
     */
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
        return $this; 
    }

    /**
     * @param string $name
     */
    public function getParam($name)
    {
        return $this->_params[$value];
    }

    /**
     * @param string $name
     */
    public function issetParam($name)
    {
        return array_key_exists($name, $this->_params);
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Query Provides a fluent interface
     */
    public function setAlt($value)
    {
        if ($value != null) {
            $this->_params['alt'] = $value;
        } else {
            unset($this->_params['alt']);
        }
        return $this;
    }

    /**
     * @param int $value
     * @return Zend_Gdata_Query Provides a fluent interface
     */
    public function setMaxResults($value)
    {
        if ($value != null) {
            $this->_params['max-results'] = $value;
        } else {
            unset($this->_params['max-results']);
        }
        return $this; 
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Query Provides a fluent interface
     */
    public function setQuery($value)
    {
        if ($value != null) {
            $this->_params['q'] = $value;
        } else {
            unset($this->_params['q']);
        }
        return $this; 
    }

    /**
     * @param int $value
     * @return Zend_Gdata_Query Provides a fluent interface
     */
    public function setStartIndex($value)
    {
        if ($value != null) {
            $this->_params['start-index'] = $value;
        } else {
            unset($this->_params['start-index']);
        }
        return $this; 
    }

    /**
     * @param int $value
     * @return Zend_Gdata_Query Provides a fluent interface
     */
    public function setUpdatedMax($value)
    {
        if ($value != null) {
            $this->_params['updated-max'] = Zend_Gdata_App_Util::formatTimestamp($value);
        } else {
            unset($this->_params['updated-max']);
        }
        return $this; 
    }

    /**
     * @param int $value
     * @return Zend_Gdata_Query Provides a fluent interface
     */
    public function setUpdatedMin($value)
    {
        if ($value != null) {
            $this->_params['updated-min'] = Zend_Gdata_App_Util::formatTimestamp($value);
        } else {
            unset($this->_params['updated-min']);
        }
        return $this; 
    }

    /**
     * @returns string rss or atom
     */
    public function getAlt()
    {
        if (array_key_exists('alt', $this->_params)) {
            return $this->_params['alt'];
        } else {
            return null;
        }
    }

    /**
     * @returns int maxResults
     */
    public function getMaxResults()
    {
        if (array_key_exists('max-results', $this->_params)) {
            return intval($this->_params['max-results']);
        } else {
            return null;
        }
    }

    /**
     * @returns string query
     */
    public function getQuery()
    {
        if (array_key_exists('q', $this->_params)) {
            return $this->_params['q'];
        } else {
            return null;
        }
    }

    /**
     * @returns int startIndex
     */
    public function getStartIndex()
    {
        if (array_key_exists('start-index', $this->_params)) {
            return intval($this->_params['start-index']);
        } else {
            return null;
        }
    }

    /**
     * @returns int updatedMax
     */
    public function getUpdatedMax()
    {
        if (array_key_exists('updated-max', $this->_params)) {
            return $this->_params['updated-max'];
        } else {
            return null;
        }
    }

    /**
     * @returns int updatedMin
     */
    public function getUpdatedMin()
    {
        if (array_key_exists('updated-min', $this->_params)) {
            return $this->_params['updated-min'];
        } else {
            return null;
        }
    }

    /**
     * @returns bool  
     */
    public function issetAlt()
    {
        return array_key_exists('alt', $this->_params);
    }

    /**
     * @returns int maxResults
     */
    public function issetMaxResults()
    {
        return array_key_exists('max-results', $this->_params);
    }

    /**
     * @returns string query
     */
    public function issetQuery()
    {
        return array_key_exists('q', $this->_params);
    }

    /**
     * @returns int startIndex
     */
    public function issetStartIndex()
    {
        return array_key_exists('q', $this->_params);
    }

    /**
     * @returns int updatedMax
     */
    public function issetUpdatedMax()
    {
        return array_key_exists('updated-max', $this->_params);
    }

    /**
     * @returns int updatedMin
     */
    public function issetUpdatedMin()
    {
        return array_key_exists('updated-min', $this->_params);
    }

    public function __get($name)
    {
        $method = 'get'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method));
        } else { 
            throw new Zend_Gdata_App_Exception('Property ' . $name . '  does not exist');
        }
    }

    public function __set($name, $val)
    {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method), $val);
        } else {
            throw new Zend_Gdata_App_Exception('Property ' . $name . '  does not exist');
        }
    }

    public function __isset($name)
    {
        $method = 'isset'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method));
        } else {
            throw new Zend_Gdata_App_Exception('Property ' . $name . '  does not exist');
        }
    }

    public function __unset($name)
    {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method), null);
        } else {
            throw new Zend_Gdata_App_Exception('Property ' . $name . '  does not exist');
        }
    }

}
