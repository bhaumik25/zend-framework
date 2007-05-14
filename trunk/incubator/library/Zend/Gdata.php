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
 * Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * Zend_Gdata_Exception
 */
require_once 'Zend/Gdata/Exception.php';

/**
 * Zend_Gdata_App
 */
require_once 'Zend/Gdata/App.php';

/**
 * Zend_Gdata_HttpException
 */
require_once 'Zend/Gdata/HttpException.php';

/**
 * Zend_Gdata_InvalidArgumentException
 */
require_once 'Zend/Gdata/InvalidArgumentException.php';

/**
 * Provides functionality to interact with Google data APIs
 * Subclasses exist to implement service-specific features
 * 
 * As the Google data API protocol is based upon the Atom Publishing Protocol 
 * (APP), GData functionality extends the appropriate Zend_Gdata_App classes
 *
 * @link http://code.google.com/apis/gdata/overview.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata extends Zend_Gdata_App
{

    const AUTH_SERVICE_NAME = 'xapi';

    /**
     * Default URI to which to POST.
     *
     * @var string
     */
    protected $_defaultPostUri = null;

    protected $_registeredPackages = array(
            'Zend_Gdata_Kind',
            'Zend_Gdata_Extension',
            'Zend_Gdata',
            'Zend_Gdata_App_Extension',
            'Zend_Gdata_App');

    /**
     * Create Gdata object
     *
     * @param Zend_Http_Client $client
     */
    public function __construct($client = null)
    {
        parent::__construct($client);
    }

    /**
     * Retreive feed object
     *
     * @param (string|Zend_Gdata_Query) $location
     * @return Zend_Gdata_Feed
     */     
    public function getFeed($location, $className='Zend_Gdata_Feed')
    {   
        if (is_string($location)) {
            $uri = $location;
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            throw new Zend_Gdata_InvalidArgumentException(
                    'You must specify the location as either a string URI ' .
                    'or a child of Zend_Gdata_Query');
        }
        return parent::getFeed($uri, $className);
    }

    /**
     * Retreive entry object
     *
     * @param (string) $location
     * @return Zend_Gdata_Feed
     */     
    public function getEntry($location, $className='Zend_Gdata_Entry')
    {   
        if (is_string($location)) {
            $uri = $location;
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            throw new Zend_Gdata_InvalidArgumentException(
                    'You must specify the location as either a string URI ' .
                    'or a child of Zend_Gdata_Query');
        }
        return parent::getEntry($uri, $className);
    }

}
