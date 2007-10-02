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
 * @package    Zend_InfoCard
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 * @author     John Coggeshall <john@zend.com>
 */

/**
 * Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * Zend_Auth_Result
 */
require_once 'Zend/Auth/Result.php';

/**
 * Zend_InfoCard
 */
require_once 'Zend/InfoCard.php';

/**
 * A Zend_Auth Authentication Adapter allowing the use of Information Cards as an
 * authentication mechanism
 * 
 * @category   Zend
 * @package    Zend_InfoCard_Auth
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     John Coggeshall <john@zend.com>
 */
class Zend_InfoCard_Auth_Adapter implements Zend_Auth_Adapter_Interface 
{
	/**
	 * The XML Token being authenticated
	 *
	 * @var string
	 */
	protected $_xmlToken;
	
	/**
	 * The instance of Zend_InfoCard
	 *
	 * @var Zend_InfoCard
	 */
	protected $_InfoCard;
	
	/**
	 * Constructor
	 *
	 * @param string $strXmlDocument The XML Token provided by the client
	 */
	public function __construct($strXmlDocument) {
		$this->_xmlToken = $strXmlDocument;
		$this->_InfoCard = new Zend_InfoCard();
	}
	
	/**
	 * Sets the InfoCard component Adapter to use
	 *
	 * @param Zend_InfoCard_Adapter_Interface $a
	 * @return Zend_InfoCard_Auth_Adapter
	 */
	public function setAdapter(Zend_InfoCard_Adapter_Interface $a) {
		$this->_InfoCard->setAdapter($a);
		return $this;
	}
	
	/**
	 * Retrieves the InfoCard component adapter being used
	 *
	 * @return Zend_InfoCard_Adapter_Interface
	 */
	public function getAdapter() {
		return $this->_InfoCard->getAdapter();
	}
	
	/**
	 * Retrieves the InfoCard public key cipher object being used
	 *
	 * @return Zend_InfoCard_Cipher_PKI_Interface
	 */
	public function getPKCipherObject() {
		return $this->_InfoCard->getPKCipherObject();
	}
	
	/**
	 * Sets the InfoCard public key cipher object to use
	 *
	 * @param Zend_InfoCard_Cipher_PKI_Interface $cipherObj
	 * @return Zend_InfoCard_Auth_Adapter
	 */
	public function setPKICipherObject(Zend_InfoCard_Cipher_PKI_Interface $cipherObj) {
		$this->_InfoCard->setPKICipherObject($cipherObj);
		return $this;
	}
	
	/**
	 * Retrieves the Symmetric cipher object being used
	 *
	 * @return Zend_InfoCard_Cipher_Symmetric_Interface
	 */
	public function getSymCipherObject() {
		return $this->_InfoCard->getSymCipherObject();
	}
	
	/**
	 * Sets the InfoCard symmetric cipher object to use
	 *
	 * @param Zend_InfoCard_Cipher_Symmetric_Interface $cipherObj
	 * @return Zend_InfoCard_Auth_Adapter
	 */
	public function setSymCipherObject(Zend_InfoCard_Cipher_Symmetric_Interface $cipherObj) {
		$this->_InfoCard->setSymCipherObject($cipherObj);
		return $this;
	}	
	
	/**
	 * Remove a Certificate Pair by Key ID from the search list
	 *
	 * @throws Zend_InfoCard_Exception
	 * @param string $key_id The Certificate Key ID returned from adding the certificate pair 
	 * @return Zend_InfoCard_Auth_Adapter
	 */
	public function removeCertificatePair($key_id) {
		$this->_InfoCard->removeCertificatePair($key_id);
		return $this;
	}
	
	/**
	 * Add a Certificate Pair to the list of certificates searched by the component
	 *
	 * @throws Zend_InfoCard_Exception
	 * @param string $private_key_file The path to the private key file for the pair
	 * @param string $public_key_file The path to the certificate / public key for the pair
	 * @param string $type (optional) The URI for the type of key pair this is (default RSA with OAEP padding)
	 * @param string $password (optional) The password for the private key file if necessary
	 * @return string A key ID representing this key pair in the component
	 */	
	public function addCertificatePair($private_key_file, $public_key_file, $type = Zend_InfoCard_Cipher::ENC_RSA_OAEP_MGF1P, $password = null) {
		return $this->_InfoCard->addCertificatePair($private_key_file, $public_key_file, $type, $password);
	}

	/**
	 * Return a Certificate Pair from a key ID
	 *
	 * @throws Zend_InfoCard_Exception
	 * @param string $key_id The Key ID of the certificate pair in the component
	 * @return array An array containing the path to the private/public key files, 
	 *               the type URI and the password if provided
	 */	
	public function getCertificatePair($key_id) {
		return $this->_InfoCard->getCertificatePair($key_id);
	}
	
	/**
	 * Set the XML Token to be processed
	 *
	 * @param string $strXmlToken The XML token to process
	 * @return Zend_InfoCard_Auth_Adapter
	 */
	public function setXmlToken($strXmlToken) {
		$this->_xmlToken = $strXmlToken;
		return $this;
	}
	
	/**
	 * Get the XML Token being processed
	 *
	 * @return string The XML token to be processed
	 */
	public function getXmlToken() {
		return $this->_xmlToken;
	}
	
	/**
	 * Authenticates the XML token
	 *
	 * @return Zend_Auth_Result The result of the authentication
	 */
	public function authenticate() {

		try {
			$claims = $this->_InfoCard->process($this->getXmlToken());
		} catch(Exception $e) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE , null, array('Exception Thrown', 
			                                                                    $e->getMessage(),
			                                                                    $e->getTraceAsString(),
			                                                                    serialize($e)));
		}
		
		if(!$claims->isValid()) {
			switch($claims->getCode()) {
				case Zend_InfoCard_Claims::RESULT_PROCESSING_FAILURE:
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE,
					                            $claims, array('Processing Failure',
					                                         $claims->getErrorMsg()));
					break;
				case Zend_InfoCard_Claims::RESULT_VALIDATION_FAILURE:
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
					                            $claims, array('Validation Failure',
					                                            $claims->getErrorMsg()));
					break;
				default:
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE,
					                            $claims, array('Unknown Failure',
					                                           $claims->getErrorMsg()));
					break;
			}
		} 
		
		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,
		                            $claims);
	}
}