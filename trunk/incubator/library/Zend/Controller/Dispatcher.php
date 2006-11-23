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
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 

/** Zend */
require_once 'Zend.php';

/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Dispatcher implements Zend_Controller_Dispatcher_Interface
{
    /**
     * Default action name; defaults to 'index'
     * @var string 
     */
    protected $_defaultAction = 'index';

    /**
     * Default controller name; defaults to 'index'
     * @var string 
     */
    protected $_defaultController = 'index';

    /**
     * Directories where Zend_Controller_Action files are stored.
     * @var array
     */
    protected $_directories = array();

    /**
     * Array of invocation parameters to use when instantiating action 
     * controllers
     * @var array 
     */
    protected $_invokeParams = array();

    /**
     * Response object to pass to action controllers, if any
     * @var Zend_Controller_Response_Abstract|null 
     */
    protected $_response = null;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
    }

    /**
     * Formats a string into a controller name.  This is used to take a raw
     * controller name, such as one that would be packaged inside a Zend_Controller_Dispatcher_Token
     * object, and reformat it to a proper class name that a class extending
     * Zend_Controller_Action would use.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatControllerName($unformatted)
    {
        return ucfirst($this->_formatName($unformatted)) . 'Controller';
    }


    /**
     * Formats a string into an action name.  This is used to take a raw
     * action name, such as one that would be packaged inside a Zend_Controller_Dispatcher_Token
     * object, and reformat into a proper method name that would be found
     * inside a class extending Zend_Controller_Action.
     *
     * @todo Should action method names allow underscores?
     * @param string $unformatted
     * @return string
     */
    public function formatActionName($unformatted)
    {
        $formatted = $this->_formatName($unformatted);
        return strtolower(substr($formatted, 0, 1)) . substr($formatted, 1) . 'Action';
    }


    /**
     * Formats a string from a URI into a PHP-friendly name.  Replaces words
     * separated by "-" or "." with camelCaps, title-cases words separated by 
     * underscores,  and removes any characters that are not alphanumeric.
     *
     * @param string $unformatted
     * @return string
     */
    protected function _formatName($unformatted)
    {
        $unformatted = str_replace(array('-', '.'), ' ', strtolower($unformatted));
        $unformatted = preg_replace('/[^a-z0-9_ ]/', '', $unformatted);
        $unformatted = str_replace(' ', '', ucwords($unformatted));

        $unformatted = str_replace('_', ' ', $unformatted);
        return str_replace(' ', '_', ucwords($unformatted));
    }

    /**
     * Add a single path to the controller directory stack
     * 
     * @param string $path 
     * @return self
     */
    public function addControllerDirectory($path)
    {
        if (!is_string($path) || !is_dir($path) || !is_readable($path)) {
            throw new Zend_Controller_Dispatcher_Exception("Directory \"$path\" not found or not readable");
        }

        return $this->_directories[] = rtrim($path, '/\\');
    }

    /**
     * Sets the directory(ies) where the Zend_Controller_Action class files are stored.
     *
     * @param string|array $path
     * @return self
     */
    public function setControllerDirectory($path)
    {
        $dirs = (array) $path;
        foreach ($dirs as $key => $dir) {
            if (!is_dir($dir) or !is_readable($dir)) {
                throw new Zend_Controller_Dispatcher_Exception("Directory \"$dir\" not found or not readable");
            }
            $dirs[$key] = rtrim($dir, '/\\');
        }

        $this->_directories = $dirs;
        return $this;
    }

    /**
     * Return the currently set directory for Zend_Controller_Action class 
     * lookup
     * 
     * @return string
     */
    public function getControllerDirectory()
    {
        return $this->_directories;
    }

    /**
     * Returns TRUE if the Zend_Controller_Request_Abstract object can be dispatched to a controller.
     * This only verifies that the Zend_Controller_Action can be dispatched and does not
     * guarantee that the action will be accepted by the Zend_Controller_Action.
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return unknown
     */
    public function isDispatchable(Zend_Controller_Request_Abstract $request)
    {
        if ($request->isDispatched()) {
            return false;
        }

        return $this->_dispatch($request, false);
    }

    /**
     * Add or modify a parameter to use when instantiating an action controller
     * 
     * @param string $name
     * @param mixed $value 
     * @return self
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     * 
     * @param array $params 
     * @return self
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
     * 
     * @param string $name 
     * @return mixed
     */
    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name])) {
            return $this->_invokeParams[$name];
        }

        return null;
    }

    /**
     * Retrieve action controller instantiation parameters
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_invokeParams;
    }

    /**
     * Clear the controller parameter stack
     * 
     * By default, clears all parameters. If a parameter name is given, clears 
     * only that parameter; if an array of parameter names is provided, clears 
     * each.
     * 
     * @param null|string|array single key or array of keys for params to clear
     * @return self
     */
    public function clearParams($name = null)
    {
        if (null === $name) {
            $this->_invokeParams = array();
        } elseif (is_string($name) && isset($this->_invokeParams[$name])) {
            unset($this->_invokeParams[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                if (is_string($key) && isset($this->_invokeParams[$key])) {
                    unset($this->_invokeParams[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Set response object to pass to action controllers
     * 
     * @param Zend_Controller_Response_Abstract|null $response 
     * @return self
     */
    public function setResponse(Zend_Controller_Response_Abstract $response = null)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Return the registered response object
     * 
     * @return Zend_Controller_Response_Abstract|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Set the default controller (minus any formatting)
     * 
     * @param string $controller 
     * @return self
     */
    public function setDefaultController($controller)
    {
        $this->_defaultController = (string) $controller;
    }

    /**
     * Retrive the default controller name (minus formatting)
     * 
     * @return string
     */
    public function getDefaultController()
    {
        return $this->_defaultController;
    }

    /**
     * Set the default action (minus any formatting)
     * 
     * @param string $action 
     * @return self
     */
    public function setDefaultAction($action)
    {
        $this->_defaultAction = (string) $action;
    }

    /**
     * Retrive the default action name (minus formatting)
     * 
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }

    /**
     * Dispatch to a controller/action
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @return boolean
     */
    public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);
        return $this->_dispatch($request);
    }


    /**
     * If $performDispatch is FALSE, this method will check if a controller
     * file exists.  This still doesn't necessarily mean that it can be dispatched
     * in the stricted sense, as file may not contain the controller class or the
     * controller may reject the action.
     *
     * If $performDispatch is TRUE, then this method will actually
     * instantiate the controller and call its action.  Calling the action
     * is done by passing a Zend_Controller_Dispatcher_Token to the controller's constructor.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param boolean $performDispatch
     * @return void
     */
    protected function _dispatch(Zend_Controller_Request_Abstract $request, $performDispatch = true)
    {
        /**
         * Controller directory check
         */
        $directories  = $this->getControllerDirectory();
        if (empty($directories)) {
            throw new Zend_Controller_Dispatcher_Exception('Controller directory never set.  Use setControllerDirectory() first');
        }

        /**
         * Get controller name
         *
         * Try request first; if not found, try pulling from request parameter; 
         * if still not found, fallback to default
         */
        $controllerName = $request->getControllerName();
        if (empty($controllerName)) {
            $controllerName = $this->getDefaultController();
        }
        $className = $this->formatControllerName($controllerName);

        /**
         * Determine if controller is dispatchable
         */
        $dispatchable = false;
        foreach ($directories as $directory) {
            $dispatchable = Zend::isReadable($directory . DIRECTORY_SEPARATOR . $className . '.php');
            if ($dispatchable) {
                break;
            }
        }

        /**
         * If $performDispatch is FALSE, only determine if the controller file
         * can be accessed.
         */
        if (!$performDispatch) {
            return $dispatchable;
        }

        /**
         * If not dispatchable, get the default controller; if this is already 
         * the default controller, throw an exception
         */
        if (!$dispatchable) {
            if ($controllerName == $this->getDefaultController()) {
                throw new Zend_Controller_Dispatcher_Exception('Default controller class not defined');
            }
            $className = $this->formatControllerName($this->getDefaultController());
        }

        /**
         * Load the controller class file
         */
        Zend::loadClass($className, $directories);

        /**
         * Instantiate controller with request, response, and invocation 
         * arguments; throw exception if it's not an action controller
         */
        $controller = new $className($request, $this->getResponse(), $this->getParams());
        if (!$controller instanceof Zend_Controller_Action) {
            throw Zend::exception('Zend_Controller_Dispatcher_Exception', "Controller '$className' is not an instance of Zend_Controller_Action");
        }

        /**
         * Determine the action name
         *
         * First attempt to retrieve from request; then from request params 
         * using action key; default to default action
         */
        $action = $request->getActionName();
        if (empty($action)) {
            $action = $this->getDefaultAction();
        }
        $action = $this->formatActionName($action);

        /**
         * If method does not exist, default to __call()
         */
        $doCall = !method_exists($controller, $action);

        /**
         * Dispatch the method call
         */
        $request->setDispatched(true);
        $controller->preDispatch();
        if ($request->isDispatched()) {
            // preDispatch() didn't change the action, so we can continue
            if ($doCall) {
                $controller->__call($action, array());
            } else {
                $controller->$action();
            }
            $controller->postDispatch();
        }

        // Destroy the page controller instance and reflection objects
        $controller = null;
        $reflection = null;
        $method     = null;
    }
}
