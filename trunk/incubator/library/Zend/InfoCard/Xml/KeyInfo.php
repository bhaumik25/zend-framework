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
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 * @author     John Coggeshall <john@zend.com>
 */

/**
 * Zend_InfoCard_Xml_Element
 */
require_once 'Zend/InfoCard/Xml/Element.php';


/**
 * Factory class to return a XML KeyInfo block based on input XML
 * 
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     John Coggeshall <john@zend.com>
 */
class Zend_InfoCard_Xml_KeyInfo {
	
	private function __construct() { }
	
	/**
	 * Returns an instance of KeyInfo object based on the input KeyInfo XML block
	 *
	 * @throws Zend_InfoCard_Xml_Exception
	 * @param string $xmlData The KeyInfo XML Block
	 * @return Zend_InfoCard_Xml_KeyInfo_Abstract
	 */
	static public function getInstance($xmlData) {
		
		if($xmlData instanceof Zend_InfoCard_Xml_Element) {
			$strXmlData = $xmlData->asXML();
		} else if (is_string($xmlData)) {
			$strXmlData = $xmlData;
		} else {
			throw new Zend_InfoCard_Xml_Exception("Invalid Data provided to create instance");
		}
		
		$sxe = simplexml_load_string($strXmlData);
		
		$namespaces = $sxe->getDocNameSpaces();
		
		if(!empty($namespaces)) {
			foreach($sxe->getDocNameSpaces() as $namespace) {
				switch($namespace) {
					case 'http://www.w3.org/2000/09/xmldsig#':
						include_once 'Zend/InfoCard/Xml/KeyInfo/XmlDSig.php';
						return simplexml_load_string($strXmlData, 'Zend_InfoCard_Xml_KeyInfo_XmlDSig');
					default:
						throw new Zend_InfoCard_Xml_Exception("Unknown KeyInfo Namespace provided");
				}
			}
		} 

		include_once 'Zend/InfoCard/Xml/KeyInfo/Default.php';
		return simplexml_load_string($strXmlData, 'Zend_InfoCard_Xml_KeyInfo_Default');
	}
	
}