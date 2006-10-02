<?php
/**
 * Zend_XmlRpc_Request
 */
require_once 'Zend/XmlRpc/Request.php';

/**
 * XmlRpc Request object -- Request via STDIN
 *
 * Extends {@link Zend_XmlRpc_Request} to accept a request via STDIN. Request is 
 * built at construction time using data from STDIN; if no data is available, the 
 * request is declared a fault.
 *
 * @package Zend_XmlRpc
 * @version $Id$
 */
class Zend_XmlRpc_Request_Stdin
{
    /**
     * Raw XML as received via request
     * @var string 
     */
    protected $_xml;

    /**
     * Constructor
     *
     * Attempts to read from php://stdin to get raw POST request; if an error 
     * occurs in doing so, or if the XML is invalid, the request is declared a 
     * fault.
     * 
     * @return void
     */
    public function __construct()
    {
        $fh = fopen('php://stdin', 'r');
        if (!$fh) {
            $this->_fault = new Zend_XmlRpc_Server_Exception(630);
            return;
        }

        $xml = '';
        while (!feof($fh)) {
            $xml .= fgets($fh);
        }
        fclose($fh);

        $this->_xml = $xml;

        $this->loadXml($xml);
    }

    /**
     * Retrieve the raw XML request
     * 
     * @return string
     */
    public function getRawRequest()
    {
        return $this->_xml;
    }
}
