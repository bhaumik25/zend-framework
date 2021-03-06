<?php

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Session */
require_once 'Zend/Session.php';

/**
 * Functional testing scaffold for MVC applications
 * 
 * @uses       PHPUnit_Framework_TestCase
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (C) 2008 - Present, Zend Technologies, Inc.
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Zend_Test_PHPUnit_ControllerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var mixed Bootstrap file path or callback
     */
    public $bootstrap;

    /**
     * @var Zend_Controller_Front
     */
    protected $_frontController;

    /**
     * @var Zend_Dom_Query
     */
    protected $_query;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;
    
    /**
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response;

    public function __set($name, $value)
    {
        if (in_array($name, array('request', 'response', 'frontController'))) {
            throw new Zend_Exception(sprintf('Setting %s object manually is not allowed', $name));
        }

        if ('_' == substr($name, 0, 1)) {
            throw new Zend_Exception('Overloading of non-public properties is prohibited');
        }

        if (null === $this->_reflection) {
            $this->_reflection = new ReflectionObject($this);
        }

        if ($this->_reflection->hasProperty($name)) {
            $prop = $this->_reflection->getProperty($name);
            if (!$prop->isPublic()) {
                throw new Zend_Exception('Overloading of non-public properties is prohibited');
            }
        }

        $this->$name = $value;
    }

    /**
     * Overloading for common properties
     *
     * Provides overloading for request, response, and frontController objects.
     * 
     * @param mixed $name 
     * @return void
     */
    public function __get($name)
    {
        switch ($name) {
            case 'request':
                return $this->getRequest();
            case 'response':
                return $this->getResponse();
            case 'frontController':
                return $this->getFrontController();
        }

        return null;
    }

    /**
     * Set up MVC app
     *
     * Calls {@link bootstrap()} by default
     * 
     * @return void
     */
    protected function setUp()
    {
        $this->bootstrap();
    }

    /**
     * Bootstrap the front controller
     *
     * Resets the front controller, and then bootstraps it.
     *
     * If {@link $bootstrap} is a callback, executes it; if it is a file, it include's 
     * it. When done, sets the test case request and response objects into the 
     * front controller.
     * 
     * @return void
     */
    final public function bootstrap()
    {
        $this->reset();
        if (null !== $this->bootstrap) {
            if (is_callable($this->bootstrap)) {
                call_user_func($this->bootstrap);
            } elseif (is_string($this->bootstrap)) {
                require_once 'Zend/Loader.php';
                if (Zend_Loader::isReadable($this->bootstrap)) {
                    include $this->bootstrap;
                }
            }
        }
        $this->frontController
             ->setRequest($this->getRequest())
             ->setResponse($this->getResponse());
    }

    /**
     * Dispatch the MVC
     *
     * If a URL is provided, sets it as the request URI in the request object. 
     * Then sets test case request and response objects in front controller, 
     * disables throwing exceptions, and disables returning the response.
     * Finally, dispatches the front controller.
     * 
     * @param  string|null $url 
     * @return void
     */
    public function dispatch($url = null)
    {
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->setExit(false);
        $request    = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);
        $controller = $this->getFrontController();
        $this->frontController
             ->setRequest($request)
             ->setResponse($this->getResponse())
             ->throwExceptions(false)
             ->returnResponse(false);
        $this->frontController->dispatch();
    }

    /**
     * Reset MVC state
     * 
     * Creates new request/response objects, resets the front controller 
     * instance, and resets the action helper broker.
     *
     * @todo   Need to update Zend_Layout to add a resetInstance() method
     * @return void
     */
    public function reset()
    {
        $_SESSION = array();
        $this->_request  = null;
        $this->_response = null;
        Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->frontController->resetInstance();
        Zend_Session::$_unitTestEnabled = true;
    }

    /**
     * Reset the response object
     *
     * Useful for test cases that need to test multiple trips to the server.
     * 
     * @return Zend_Test_PHPUnit_ControllerTestCase
     */
    public function resetResponse()
    {
        $this->_response = null;
        return $this;
    }

    /**
     * Assert against DOM selection
     * 
     * @param  string $path CSS selector path
     * @param  string $message
     * @return void
     */
    public function assertSelect($path, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection
     * 
     * @param  string $path CSS selector path
     * @param  string $message
     * @return void
     */
    public function assertNotSelect($path, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should contain content
     * 
     * @param  string $path CSS selector path
     * @param  string $match content that should be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertSelectContentContains($path, $match, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should NOT contain content
     * 
     * @param  string $path CSS selector path
     * @param  string $match content that should NOT be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertNotSelectContentContains($path, $match, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should match content
     * 
     * @param  string $path CSS selector path
     * @param  string $pattern Pattern that should be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertSelectContentRegex($path, $pattern, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should NOT match content
     * 
     * @param  string $path CSS selector path
     * @param  string $pattern pattern that should NOT be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertNotSelectContentRegex($path, $pattern, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; should contain exact number of nodes
     * 
     * @param  string $path CSS selector path
     * @param  string $count Number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertSelectCount($path, $count, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; should NOT contain exact number of nodes
     * 
     * @param  string $path CSS selector path
     * @param  string $count Number of nodes that should NOT match
     * @param  string $message
     * @return void
     */
    public function assertNotSelectCount($path, $count, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; should contain at least this number of nodes
     * 
     * @param  string $path CSS selector path
     * @param  string $count Minimum number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertSelectCountMin($path, $count, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; should contain no more than this number of nodes
     * 
     * @param  string $path CSS selector path
     * @param  string $count Maximum number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertSelectCountMax($path, $count, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection
     * 
     * @param  string $path XPath path
     * @param  string $message
     * @return void
     */
    public function assertXpath($path, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection
     * 
     * @param  string $path XPath path
     * @param  string $message
     * @return void
     */
    public function assertNotXpath($path, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection; node should contain content
     * 
     * @param  string $path XPath path
     * @param  string $match content that should be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertXpathContentContains($path, $match, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection; node should NOT contain content
     * 
     * @param  string $path XPath path
     * @param  string $match content that should NOT be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertNotXpathContentContains($path, $match, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection; node should match content
     * 
     * @param  string $path XPath path
     * @param  string $pattern Pattern that should be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertXpathContentRegex($path, $pattern, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection; node should NOT match content
     * 
     * @param  string $path XPath path
     * @param  string $pattern pattern that should NOT be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertNotXpathContentRegex($path, $pattern, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $pattern)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection; should contain exact number of nodes
     * 
     * @param  string $path XPath path
     * @param  string $count Number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertXpathCount($path, $count, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection; should NOT contain exact number of nodes
     * 
     * @param  string $path XPath path
     * @param  string $count Number of nodes that should NOT match
     * @param  string $message
     * @return void
     */
    public function assertNotXpathCount($path, $count, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection; should contain at least this number of nodes
     * 
     * @param  string $path XPath path
     * @param  string $count Minimum number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertXpathCountMin($path, $count, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against XPath selection; should contain no more than this number of nodes
     * 
     * @param  string $path XPath path
     * @param  string $count Maximum number of nodes that should match
     * @param  string $message
     * @return void
     */
    public function assertXpathCountMax($path, $count, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
        $content    = $this->response->outputBody();
        if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert that response is a redirect
     * 
     * @param  string $message 
     * @return void
     */
    public function assertRedirect($message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert that response is NOT a redirect
     * 
     * @param  string $message 
     * @return void
     */
    public function assertNotRedirect($message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert that response redirects to given URL
     * 
     * @param  string $url 
     * @param  string $message 
     * @return void
     */
    public function assertRedirectTo($url, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $url)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert that response does not redirect to given URL
     * 
     * @param  string $url 
     * @param  string $message 
     * @return void
     */
    public function assertNotRedirectTo($url, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $url)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert that redirect location matches pattern
     * 
     * @param  string $pattern 
     * @param  string $message 
     * @return void
     */
    public function assertRedirectRegex($pattern, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $pattern)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert that redirect location does not match pattern
     * 
     * @param  string $pattern 
     * @param  string $message 
     * @return void
     */
    public function assertNotRedirectRegex($pattern, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/Redirect.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_Redirect();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $pattern)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert response code
     * 
     * @param  int $code 
     * @param  string $message 
     * @return void
     */
    public function assertResponseCode($code, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $code)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert response code
     * 
     * @param  int $code 
     * @param  string $message 
     * @return void
     */
    public function assertNotResponseCode($code, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
        $constraint->setNegate(true);
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $code)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert response header exists
     * 
     * @param  string $header 
     * @param  string $message 
     * @return void
     */
    public function assertHeader($header, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $header)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert response header does not exist
     * 
     * @param  string $header 
     * @param  string $message 
     * @return void
     */
    public function assertNotHeader($header, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
        $constraint->setNegate(true);
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $header)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert response header exists and contains the given string
     * 
     * @param  string $header 
     * @param  string $match 
     * @param  string $message 
     * @return void
     */
    public function assertHeaderContains($header, $match, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $header, $match)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert response header does not exist and/or does not contain the given string
     * 
     * @param  string $header 
     * @param  string $match
     * @param  string $message 
     * @return void
     */
    public function assertNotHeaderContains($header, $match, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
        $constraint->setNegate(true);
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $header, $match)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert response header exists and matches the given pattern
     * 
     * @param  string $header 
     * @param  string $pattern 
     * @param  string $message 
     * @return void
     */
    public function assertHeaderRegex($header, $pattern, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $header, $pattern)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert response header does not exist and/or does not match the given regex
     * 
     * @param  string $header 
     * @param  string $pattern
     * @param  string $message 
     * @return void
     */
    public function assertNotHeaderRegex($header, $pattern, $message = '')
    {
        require_once 'Zend/Test/PHPUnit/Constraint/ResponseHeader.php';
        $constraint = new Zend_Test_PHPUnit_Constraint_ResponseHeader();
        $constraint->setNegate(true);
        $response   = $this->response;
        if (!$constraint->evaluate($response, __FUNCTION__, $header, $pattern)) {
            $constraint->fail($response, $message);
        }
    }

    /**
     * Assert that the last handled request used the given module
     * 
     * @param  string $module 
     * @param  string $message 
     * @return void
     */
    public function assertModule($module, $message = '')
    {
        if ($module != $this->request->getModuleName()) {
            $msg = sprintf('Failed asserting last module used was "%s"', $module);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    /**
     * Assert that the last handled request did NOT use the given module
     * 
     * @param  string $module 
     * @param  string $message 
     * @return void
     */
    public function assertNotModule($module, $message = '')
    {
        if ($module == $this->request->getModuleName()) {
            $msg = sprintf('Failed asserting last module used was NOT "%s"', $module);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    /**
     * Assert that the last handled request used the given controller
     * 
     * @param  string $controller 
     * @param  string $message 
     * @return void
     */
    public function assertController($controller, $message = '')
    {
        if ($controller != $this->request->getControllerName()) {
            $msg = sprintf('Failed asserting last controller used was "%s"', $controller);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    /**
     * Assert that the last handled request did NOT use the given controller
     * 
     * @param  string $controller 
     * @param  string $message 
     * @return void
     */
    public function assertNotController($controller, $message = '')
    {
        if ($controller == $this->request->getControllerName()) {
            $msg = sprintf('Failed asserting last controller used was NOT "%s"', $controller);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    /**
     * Assert that the last handled request used the given action
     * 
     * @param  string $action 
     * @param  string $message 
     * @return void
     */
    public function assertAction($action, $message = '')
    {
        if ($action != $this->request->getActionName()) {
            $msg = sprintf('Failed asserting last action used was "%s"', $action);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    /**
     * Assert that the last handled request did NOT use the given action
     * 
     * @param  string $action 
     * @param  string $message 
     * @return void
     */
    public function assertNotAction($action, $message = '')
    {
        if ($action == $this->request->getActionName()) {
            $msg = sprintf('Failed asserting last action used was NOT "%s"', $action);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    /**
     * Assert that the specified route was used
     * 
     * @param  string $route 
     * @param  string $message 
     * @return void
     */
    public function assertRoute($route, $message = '')
    {
        $router = $this->frontController->getRouter();
        if ($route != $router->getCurrentRouteName()) {
            $msg = sprintf('Failed asserting route matched was "%s"', $route);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    /**
     * Assert that the route matched is NOT as specified
     * 
     * @param  string $route 
     * @param  string $message 
     * @return void
     */
    public function assertNotRoute($route, $message = '')
    {
        $router = $this->frontController->getRouter();
        if ($route == $router->getCurrentRouteName()) {
            $msg = sprintf('Failed asserting route matched was NOT "%s"', $route);
            if (!empty($message)) {
                $msg = $message . "\n" . $msg;
            }
            $this->fail($msg);
        }
    }

    /**
     * Retrieve front controller instance
     * 
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            require_once 'Zend/Controller/Front.php';
            $this->_frontController = Zend_Controller_Front::getInstance();
        }
        return $this->_frontController;
    }

    /**
     * Retrieve test case request object
     * 
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            require_once 'Zend/Controller/Request/HttpTestCase.php';
            $this->_request = new Zend_Controller_Request_HttpTestCase;
        }
        return $this->_request;
    }

    /**
     * Retrieve test case response object 
     * 
     * @return Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        if (null === $this->_response) {
            require_once 'Zend/Controller/Response/HttpTestCase.php';
            $this->_response = new Zend_Controller_Response_HttpTestCase;
        }
        return $this->_response;
    }

    /**
     * Retrieve DOM query object
     * 
     * @return Zend_Dom_Query
     */
    public function getQuery()
    {
        if (null === $this->_query) {
            require_once 'Zend/Dom/Query.php';
            $this->_query = new Zend_Dom_Query;
        }
        return $this->_query;
    }
}
