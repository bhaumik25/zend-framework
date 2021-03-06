<?php
/**
 * @package    Zend_HttpClient
 * @subpackage UnitTests
 */


/**
 * Zend_HttpClient
 */
require_once 'Zend/HttpClient.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_HttpClient
 * @subpackage UnitTests
 */
class Zend_HttpClient_ResponseTest extends PHPUnit2_Framework_TestCase
{
	protected $_remoteEnabled = false;

    public function setUp()
    {
        /**
         * @todo reimplement with markAsSkipped for PHPUnit 3.0
         */
        if (defined('TESTS_ZEND_HTTPCLIENT_REMOTE_ENABLED')) {
            $this->_remoteEnabled = TESTS_ZEND_HTTPCLIENT_REMOTE_ENABLED;
        }
    }

    public function testSuccessfulRequest()
    {
    	if (!$this->_remoteEnabled) {
    		return;
    	}

    	$http = new Zend_HttpClient(TESTS_ZEND_HTTPCLIENT_REMOTE_URI);
    	$result = $http->get();
    	$this->assertTrue($result->isSuccessful(), 'Unsuccessful status code returned');
       	$this->assertEquals(200, $result->getStatus(),
       	                   'Success Status Code not detected in Zend_HttpClient_Response::isSuccess()');
	}

    public function testFailedRequest()
    {
    	if (!$this->_remoteEnabled) {
    		return;
    	}

    	$http = new Zend_HttpClient(TESTS_ZEND_HTTPCLIENT_REMOTE_URI . '/intentionallywrongdir');
    	$result = $http->get();
    	$this->assertEquals(404, $result->getStatus(), 'Expected 404 status returned');
    	$this->assertTrue($result->isError(), 'Error Status Code not detected in Zend_HttpClient_Response::isError()');
    }

    public function testRedirectRequest()
    {
    	if (!$this->_remoteEnabled) {
    		return;
    	}
    	/**
    	 * @todo complete
    	 */
    }

    public function testGetBody()
    {
    	if (!$this->_remoteEnabled) {
    		return;
    	}
    	$http = new Zend_HttpClient(TESTS_ZEND_HTTPCLIENT_REMOTE_URI . '/intentionallywrongdir');
    	$result = $http->get();
    	$this->assertNotNull($result->getBody(), 'Document body not returned');
    	$this->assertContains('could not be found', $result->getBody(), 'Incorrect document body returned');
    }

    public function testHeaders()
    {
    	if (!$this->_remoteEnabled) {
    		return;
    	}
    	$http = new Zend_HttpClient(TESTS_ZEND_HTTPCLIENT_REMOTE_URI);
    	$result = $http->get();
    	$this->assertType('array', $result->getHeaders(),
    	                  'Headers not returned; Zend_HttpClient_Response::$_requestHeaders is not an array');
    	$this->assertTrue(in_array('Content-Type', array_keys($result->getHeaders())),
    	                  'Required Content-Type header not found');
    }
}
