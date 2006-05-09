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
 * @package    Zend_Http
 * @subpackage Client
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once "Zend/Http/Client/Exception.php";

/**
 * Zend_Http_Response represents an HTTP 1.0 / 1.1 response message. It 
 * includes easy access to all the response's different elemts, as well as some
 * convenience methods for parsing and validating HTTP responses. 
 * 
 * @package    Zend_Http
 * @subpackage Response
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_Response
{
	/**
	 * The HTTP version (1.x, 1.0, 1.1)
	 *
	 * @var string
	 */
	protected $version;
	
	/**
	 * The HTTP response code
	 *
	 * @var int
	 */
	protected $code;
	
	/**
	 * The HTTP response code as string (e.g. 'Not Found' for 404 or 'Internal Server Error' for 500)
	 *
	 * @var string
	 */
	protected $message;
	
	/**
	 * The HTTP response headers array
	 *
	 * @var array
	 */
	protected $headers = array();
	
	/**
	 * The HTTP response body
	 *
	 * @var string
	 */
	protected $body;
	
	/**
	 * HTTP response constructor
	 * 
	 * In most cases, you would use Zend_Http_Response::factory to parse an HTTP 
	 * response string and create a new Zend_Http_Response object.
	 * 
	 * NOTE: The constructor no longer accepts nulls or empty values  for the code and
	 * headers and will throw an exception if the passed values do not form a valid HTTP 
	 * responses. 
	 * 
	 * If no message is passed, the message will be guessed according to the response code.
	 *
	 * @param int $code Response code (200, 404, ...)
	 * @param array $headers Headers array
	 * @param string $body Response body
	 * @param string $version HTTP version
	 * @param string $message Response code as text
	 */
	public function __construct($code, $headers, $body = null, $version = '1.1', $message = null)
	{
		if (is_null(self::responseCodeAsText($code)))
			throw new Zend_Http_Exception("{$code} is not a valid HTTP response code");
		$this->code = $code;
		
		if (! (is_array($headers) && count($headers) > 0))
			throw new Zend_Http_Exception('No valid headers were passed');
		foreach ($headers as $name => $value) {
			$this->headers[ucwords(strtolower($name))] = $value;
		}
			
		$this->body = $body;
		
		if (! preg_match('|^1\.[10x]$|', $version))
			throw new Zend_Http_Exception("Unsupported HTTP response version: $version");
		$this->version = $version;
		
		if (is_string($message)) {
			$this->message = $message;
		} else {
			$this->message = self::responseCodeAsText($code);
		}
	}
	
	/**
	 * Check whether the response is an error
	 *
	 * @return boolean
	 */
	public function isError()
	{
		$restype = floor($this->code / 100);
		if ($restype == 4 || $restype == 5) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Check whether the response in successful
	 * 
	 * @return boolean
	 */
	public function isSuccessful()
	{
		$restype = floor($this->code / 100);
		if ($restype == 2 || $restype == 1) { // Shouldn't 3xx count as success as well ???
			return true;
		}
		
		return false;
	}
	
	/**
	 * Check whether the response is a redirection
	 * 
	 * @return boolean
	 */
	public function isRedirect()
	{
		$restype = floor($this->code / 100);
		if ($restype == 3) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get the response body as string
	 * 
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}
	
	/**
	 * Get the HTTP version of the response
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}
	
	/**
	 * Get the HTTP response status code
	 *
	 * @return int
	 */
	public function getStatus()
	{
		return $this->code;
	}
	
	/**
	 * Return a message describing the HTTP response code (Eg. "OK", "Not Found", "Moved Permanently")
	 *
	 * See http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10 for reference
	 * 
	 * @return string
	 */
	public function getStatusAsString()
	{
		return $this->message;
	}
	
	/**
	 * Get the response headers
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}
	
	/**
	 * Get a specific header as string, or null if it is not set
	 *
	 * @param string|null $header
	 */
	public function getHeader($header)
	{
		$header = ucwords(strtolower($header));
		if (! (is_string($header) && isset($this->headers[$header]))) return null;

		return $this->headers[$header];
	}
	
	/**
	 * Get all headers as string
	 *
	 * @param boolean $status_line Whether to return the first status (IE "HTTP 200 OK")
	 * @return string
	 */
	public function getHeadersAsString($status_line = true)
	{
		$str = '';
		
		if ($status_line) {
			$str = "HTTP/{$this->version} {$this->code} {$this->message}\n";
		}
		
		foreach ($this->headers as $name => $value)
		{
			if (is_string($value)) {
				$str .= "{$name}: {$value}\n";
			}
			elseif (is_array($value)) {
				foreach ($value as $subval) {
					$str .= "{$name}: {$subval}\n";
				}
			}
		}
		
		return $str;
	}
	
	/**
	 * Get the entire response as string
	 *
	 * @return string
	 */
	public function getAsString()
	{
		$str = $this->getHeadersAsString(true);
		$str .= "\n";
		$str .= $this->getBody();
		
		return $str;
	}
	
	/**
	 * A convenience function that returns a text representation of
	 * HTTP response codes. Returns null for unknown codes.
	 *
	 * Conforms to HTTP/1.1 as defined in RFC 2616
	 * See http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10 for reference
	 * 
	 * @param int $code HTTP response code
	 * @param boolean $http11 Use HTTP version 1.1 
	 * @return string|null
	 */ 
	static public function responseCodeAsText($code, $http11 = true)
	{
		$messages = array (
			// Informational 1xx
			100 => 'Continue',
			101 => 'Switching Protocols',
			// Success 2xx
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			// Redirection 3xx
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',  // 1.1
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			// 306 is deprecated but reserved
			307 => 'Temporary Redirect',
			// Client Error 4xx
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			// Server Error 5xx
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			509 => 'Bandwidth Limit Exceeded'
		);
		
		if (! $http11) $messages[302] = 'Moved Temporarily';

		if (isset($messages[$code])) {
			return $messages[$code];
		} else {
			return null;
		}
	}
	
	/**
	 * Extract the response code from a response string
	 *
	 * @param string $response_str
	 * @return int
	 */
	static public function extractCode(&$response_str)
	{
		preg_match("|^HTTP/[\d\.x]+ (\d+) |", $response_str, $m);
		
		if (isset($m[1])) {
			return (int) $m[1];
		} else {
			return false;
		}
	}
	
	/**
	 * Extract the HTTP message from a response
	 *
	 * @param string $response_str
	 * @return string
	 */
	static public function extractMessage(&$response_str)
	{
		preg_match("|^HTTP/[\d\.x]+ \d+ (.+)$|", $response_str, $m);
		
		if (isset($m[1])) {
			return $m[1];
		} else {
			return false;
		}
	}
	
	/**
	 * Extract the HTTP version from a response
	 *
	 * @param string $response_str
	 * @return string
	 */
	static public function extractVersion(&$response_str) 
	{
		preg_match("|^HTTP/([\d\.x]+) \d+ |", $response_str, $m);
		
		if (isset($m[1])) {
			return $m[1];
		} else {
			return false;
		}
	}
	
	/**
	 * Extract the headers from a response string
	 *
	 * @param string $response_str
	 * @return array
	 */
	static public function extractHeaders(&$response_str)
	{
		$headers = array();
		
 		$lines = explode("\r\n", $response_str);
		
 		$last_header = null;
		foreach($lines as $line) {
			$line = trim($line, "\r\n");
			if ($line == "") break;
			
			if (preg_match("|^([\w-]+):\s+(.+)|", $line, $m)) {
				unset($last_header);
				$h_name = $m[1];
				$h_value = $m[2];
				
				if (isset($headers[$h_name])) {
					if (! is_array($headers[$h_name])) {
						$headers[$h_name] = array($headers[$h_name]);
					} 
					
					$headers[$h_name][] = $h_value;
					$last_header = &end($headers[$h_name]);
				} else {
					$headers[$h_name] = $h_value;
					$last_header = &$headers[$h_name];
				}
			} elseif (preg_match("|^\s+(\S+)$|", $response_str, $m) && ! is_null($last_header)) {
				$last_header .= $m[1];
			}
		}
		
		return $headers;
	}
	
	/**
	 * Extract the body from a response string
	 *
	 * @param string $response_str
	 * @return string
	 */
	static public function extractBody(&$response_str)
	{
		$lines = explode("\r\n", $response_str);
		$line = null;
		while ($line !== "")
			$line = array_shift($lines);
		
		$body = implode("\r\n", $lines);
		
		return $body;
	}
	
	/**
	 * Create a new Zend_Http_Response object from a string
	 *
	 * @param string $response_str
	 * @return Zend_Http_Response
	 */
	static public function factory($response_str)
	{
		$code    = self::extractCode($response_str);
		$headers = self::extractHeaders($response_str);
		$body    = self::extractBody($response_str);
		$version = self::extractVersion($response_str);
		$message = self::extractMessage($response_str);
		
		return new Zend_Http_Response($code, $headers, $body);
	}
}