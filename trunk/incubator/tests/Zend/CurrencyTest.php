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
 * @package    Zend_Currency
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DataTest.php 3776 2007-03-06 22:50:56Z thomas $
 */


/**
 * Zend_Currency
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Currency.php';


/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework.php';


/**
 * @package    Zend_Currency
 * @subpackage UnitTests
 */
class Zend_CurrencyTest extends PHPUnit_Framework_TestCase
{

    /**
     * tests the creation of Zend_Currency
     */
    public function testCreation()
    {
        $locale = new Zend_Locale('en_US');

        $currency = new Zend_Currency('USD','en_US','Arab');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('USD','en_US','Arab');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('USD',$locale,'Arab');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('en_US', 'USD','Arab');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency($locale, 'USD','Arab');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('en_US','Arab','USD');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency($locale,'Arab', 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('en_US', 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency($locale, 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('USD', 'en_US');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('USD', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('Arab', 'en_US');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('Arab', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('en_US', 'Arab' );
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency($locale, 'Arab');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('en_US');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency($locale);
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('USD');
        $this->assertTrue($currency instanceof Zend_Currency);

        $currency = new Zend_Currency('Arab');
        $this->assertTrue($currency instanceof Zend_Currency);

        try{
            $currency = new Zend_Currency('aG_ea');
            $this->fail();
        } catch(Zend_Currency_Exception $e) {
            // success
        }

        try{
            $currency = new Zend_Currency('XYZ');
            $this->fail();
        } catch(Zend_Currency_Exception $e) {
            // success
        }

        try{
            $currency = new Zend_Currency('wxyz');
            $this->fail();
        } catch(Zend_Currency_Exception $e) {
            // success
        }
    }


    /*
     * testing toCurrency
     */
    public function testToCurrency()
    {
        $USD = new Zend_Currency('USD','en_US');
        $EGP = new Zend_Currency('EGP','ar_EG');

        $this->assertSame($USD->toCurrency(253292.1832), '$ 253,292.1832');
        $this->assertSame($USD->toCurrency(253292.1832, 'Arab'), '$ ٢٥٣,٢٩٢.١٨٣٢');
        $this->assertSame($USD->toCurrency(253292.1832, 'Arab', 'de_AT'), '$ ٢٥٣.٢٩٢,١٨٣٢');
        $this->assertSame($USD->toCurrency(253292.1832, null, 'de_AT'), '$ 253.292,1832');

        $this->assertSame($EGP->toCurrency(253292.1832), 'ج.م.‏ 253٬292٫1832');
        $this->assertSame($EGP->toCurrency(253292.1832, 'Arab'), 'ج.م.‏ ٢٥٣٬٢٩٢٫١٨٣٢');
        $this->assertSame($EGP->toCurrency(253292.1832, 'Arab', 'de_AT'), 'ج.م.‏ ٢٥٣.٢٩٢,١٨٣٢');
        $this->assertSame($EGP->toCurrency(253292.1832, null, 'de_AT'), 'ج.م.‏ 253.292,1832');
    }


    /**
     * testing setFormat
     *
     */
    public function testSetFormat()
    {
        $USD = new Zend_Currency('USD','en_US');

        $USD->setFormat(null, 'Arab');
        $this->assertSame($USD->toCurrency(253292.1832), '$ ٢٥٣,٢٩٢.١٨٣٢');

        $USD->setFormat(null, 'Arab', 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), '$ ٢٥٣.٢٩٢,١٨٣٢');

        $USD->setFormat(null, 'Default', 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), '$ 253.292,1832');

        // allignment of currency signs
        $USD->setFormat(Zend_Currency::RIGHT, null, 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), '253.292,1832 $');

        $USD->setFormat(Zend_Currency::LEFT, null, 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), '$ 253.292,1832');

        $USD->setFormat(Zend_Currency::STANDARD, null, 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), '$ 253.292,1832');

        // enable/disable currency symbols & currency names
        $USD->setFormat(Zend_Currency::NO_SYMBOL, null, 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), 'US Dollar 253.292,1832');

        $USD->setFormat(Zend_Currency::NO_NAME, null, 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), 'USD 253.292,1832');

        $USD->setFormat(Zend_Currency::USE_NAME, null, 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), 'US Dollar 253.292,1832');

        $USD->setFormat(Zend_Currency::USE_SYMBOL, null, 'de_AT');
        $this->assertSame($USD->toCurrency(253292.1832), '$ 253.292,1832');
    }


    /**
     * test getSign
     */
    public function testGetSign()
    {
        $this->assertSame(Zend_Currency::getSign('EGP','ar_EG'), 'ج.م.‏');
        $this->assertSame(Zend_Currency::getSign('ar_EG'), 'ج.م.‏');
    }


    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertSame(Zend_Currency::getName('EGP','ar_EG'), 'EGP');
        $this->assertSame(Zend_Currency::getName('ar_EG'), 'EGP');
    }


    /**
     * testing getRegionList
     */
    public function testGetRegionList()
    {
        $this->assertTrue( is_array(Zend_Currency::getRegionList('USD')) );
    }


    /**
     * testing getCurrencyList
     */
    public function testGetCurrencyList()
    {
        $this->assertTrue( is_array(Zend_Currency::getCurrencyList('EG')) );
    }


    /**
     * testing toString
     *
     */
    public function testToString()
    {
        $USD = new Zend_Currency('USD','en_US');
        $this->assertSame($USD->toString(), 'US Dollar');
    }
}
