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
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 4412 2007-04-06 21:17:32Z zendbot $
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * @see Zend_Filter_Input
 */
require_once 'Zend/Filter/Input.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

class Zend_Filter_InputTest extends PHPUnit_Framework_TestCase
{

    public function testFilterDeclareSingle()
    {
        $data = array(
            'month' => '6abc '
        );
        $filters = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input($filters, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $month = $input->month;
        $this->assertEquals('6', $month);
    }

    public function testFilterDeclareByObject()
    {
        $data = array(
            'month' => '6abc '
        );
        Zend_Loader::loadClass('Zend_Filter_Digits');
        $filters = array(
            'month' => array(new Zend_Filter_Digits())
        );
        $input = new Zend_Filter_Input($filters, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $month = $input->month;
        $this->assertEquals('6', $month);
    }

    public function testFilterDeclareByArray()
    {
        $data = array(
            'month' => '_6_'
        );
        $filters = array(
            'month' => array(
                array('StringTrim', '_')
            )
        );
        $input = new Zend_Filter_Input($filters, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $month = $input->month;
        $this->assertEquals('6', $month);
    }

    public function testFilterDeclareByChain()
    {
        $data = array(
            'field1' => ' ABC '
        );
        $filters = array(
            'field1' => array('StringTrim', 'StringToLower')
        );
        $input = new Zend_Filter_Input($filters, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals('abc', $input->field1);
    }

    public function testFilterWildcardRule()
    {
        $data = array(
            'field1'  => ' 12abc ',
            'field2'  => ' 24abc '
        );
        $filters = array(
            '*'       => 'stringTrim',
            'field1'  => 'digits'
        );
        $input = new Zend_Filter_Input($filters, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals('12', $input->field1);
        $this->assertEquals('24abc', $input->field2);
    }

    public function testFilterMultiValue()
    {
        $data = array(
            'field1' => array('FOO', 'BAR', 'BaZ')
        );
        $filters = array(
            'field1' => 'StringToLower'
        );
        $input = new Zend_Filter_Input($filters, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $f1 = $input->field1;
        $this->assertType('array', $f1);
        $this->assertEquals(array('foo', 'bar', 'baz'), $f1);
    }

    public function testValidatorSingle()
    {
        $data = array(
            'month' => '6'
        );
        $validators = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $month = $input->month;
        $this->assertEquals('6', $month);
    }

    public function testValidatorSingleInvalid()
    {
        $data = array(
            'month' => '6abc '
        );
        $validators = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('month'), array_keys($messages));
        $this->assertType('array', $messages['month']);
        $this->assertEquals("'6abc ' contains not only digit characters", $messages['month'][0]);

        $errors = $input->getErrors();
        $this->assertType('array', $errors);
        $this->assertEquals(array('month'), array_keys($errors));
        $this->assertType('array', $errors['month']);
        $this->assertEquals("notDigits", $errors['month'][0]);
    }

    public function testValidatorDeclareByObject()
    {
        $data = array(
            'month' => '6'
        );
        Zend_Loader::loadClass('Zend_Validate_Digits');
        $validators = array(
            'month' => array(
                new Zend_Validate_Digits()
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $month = $input->month;
        $this->assertEquals('6', $month);
    }

    public function testValidatorDeclareByArray()
    {
        $data = array(
            'month' => '6',
            'month2' => 13
        );
        $validators = array(
            'month' => array(
                'digits',
                array('Between', 1, 12)
            ),
            'month2' => array(
                'digits',
                array('Between', 1, 12)
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $month = $input->month;
        $this->assertEquals('6', $month);

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('month2'), array_keys($messages));
        $this->assertEquals("'13' is not between '1' and '12', inclusively", $messages['month2'][0]);
    }

    public function testValidatorChain()
    {
        $data = array(
            'field1' => '50',
            'field2' => 'abc123',
            'field3' => 150,
        );
        Zend_Loader::loadClass('Zend_Validate_Between');
        $btw = new Zend_Validate_Between(1, 100);
        $validators = array(
            'field1' => array('digits', $btw),
            'field2' => array('digits', $btw),
            'field3' => array('digits', $btw)
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('field2', 'field3'), array_keys($messages));
        $this->assertType('array', $messages['field2']);
        $this->assertType('array', $messages['field3']);
        $this->assertEquals("'abc123' contains not only digit characters",
            $messages['field2'][0]);
        $this->assertEquals("'150' is not between '1' and '100', inclusively",
            $messages['field3'][0]);
    }

    public function testValidatorInvalidFieldInMultipleRules()
    {
        $data = array(
            'field2' => 'abc123',
        );
        Zend_Loader::loadClass('Zend_Validate_Between');
        $validators = array(
            'field2a' => array(
                'digits',
                'fields' => 'field2'
            ),
            'field2b' => array(
                new Zend_Validate_Between(1, 100),
                'fields' => 'field2'
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('field2a', 'field2b'), array_keys($messages));
        $this->assertType('array', $messages['field2a']);
        $this->assertType('array', $messages['field2b']);
        $this->assertEquals("'abc123' contains not only digit characters",
            $messages['field2a'][0]);
        $this->assertEquals("'abc123' is not between '1' and '100', inclusively",
            $messages['field2b'][0]);
    }

    public function testValidatorWildcardRule()
    {
        $data = array(
            'field1'  => '123abc',
            'field2'  => '246abc'
        );
        $validators = array(
            '*'       => 'alnum',
            'field1'  => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertNull($input->field1);
        $this->assertEquals('246abc', $input->field2);
    }

    public function testValidatorMultiValue()
    {
        $data = array(
            'field1' => array('abc', 'def', 'ghi'),
            'field2' => array('abc', '123')
        );
        $validators = array(
            'field1' => 'alpha',
            'field2' => 'alpha'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('field2'), array_keys($messages));
        $this->assertEquals("'123' has not only alphabetic characters",
            $messages['field2'][0]);
    }

    public function testValidatorMultiField()
    {
        $data = array(
            'password1' => 'EREIAMJH',
            'password2' => 'EREIAMJH',
            'password3' => 'VESPER'
        );
        $validators = array(
            'rule1' => array(
                'StringEquals',
                'fields' => array('password1', 'password2')
            ),
            'rule2' => array(
                'StringEquals',
                'fields' => array('password1', 'password3')
            )
        );
        $options = array(
            Zend_Filter_Input::NAMESPACE => 'TestNamespace'
        );

        $ip = get_include_path();
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files';
        $newIp = $dir . PATH_SEPARATOR . $ip;
        set_include_path($newIp);

        $input = new Zend_Filter_Input(null, $validators, $data, $options);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        set_include_path($ip);
        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('rule2'), array_keys($messages));
        $this->assertEquals("Not all strings in the argument are equal",
            $messages['rule2'][0]);
    }

    public function testValidatorBreakChain()
    {
        $data = array(
            'field1' => '150',
            'field2' => '150'
        );
        Zend_Loader::loadClass('Zend_Validate_Between');
        $btw1 = new Zend_Validate_Between(1, 100);
        $btw2 = new Zend_Validate_Between(1, 125);
        $validators = array(
            'field1' => array($btw1, $btw2),
            'field2' => array($btw1, $btw2, Zend_Filter_Input::BREAK_CHAIN => true)
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('field1', 'field2'), array_keys($messages));
        $this->assertEquals(2, count($messages['field1']), 'Expected rule for field1 to break 2 validators');
        $this->assertEquals(1, count($messages['field2']), 'Expected rule for field2 to break 1 validator');
        $this->assertEquals("'150' is not between '1' and '100', inclusively",
            $messages['field1'][0]);
        $this->assertEquals("'150' is not between '1' and '125', inclusively",
            $messages['field1'][1]);
        $this->assertEquals("'150' is not between '1' and '100', inclusively",
            $messages['field2'][0]);
    }

    public function testValidatorAllowEmpty()
    {
        $data = array(
            'field1' => '',
            'field2' => ''
        );
        $validators = array(
            'field1' => array(
                'alpha',
                Zend_Filter_Input::ALLOW_EMPTY => false
            ),
            'field2' => array(
                'alpha',
                Zend_Filter_Input::ALLOW_EMPTY => true
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertNull($input->field1);
        $this->assertNotNull($input->field2);
        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('field1'), array_keys($messages));
        $this->assertEquals("'' has not only alphabetic characters", $messages['field1'][0]);
    }

    public function testValidatorAllowEmptyNoValidatorChain()
    {
        Zend_Loader::loadClass('Zend_Filter_StringTrim');
        Zend_Loader::loadClass('Zend_Filter_StripTags');
        Zend_Loader::loadClass('Zend_Validate_EmailAddress');

        $data = array(   
            'nick'    => '',
            'email'   => 'someemail@server.com'
        );

        $filters = array(
            '*'       => new Zend_Filter_StringTrim(),
            'nick'    => new Zend_Filter_StripTags()
        );

        $validators = array(
            'email'   => array(
                new Zend_Validate_EmailAddress(),
                Zend_Filter_Input::ALLOW_EMPTY => true
            ),
            /*
             * This is the case we're testing - when presense is required,
             * but there are no validators besides disallowing empty values.
             */
            'nick'    => array(
                Zend_Filter_Input::PRESENCE    => Zend_Filter_Input::PRESENCE_REQUIRED,
                Zend_Filter_Input::ALLOW_EMPTY => false
            )
        );

        $input = new Zend_Filter_Input($filters, $validators, $data);

        if ($input->hasInvalid()) {
            $message = $input->getMessages();
        }

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('nick'), array_keys($messages));
        $this->assertEquals(1, count($messages['nick']));
    }

    public function testValidatorAllowEmptySetNotEmptyMessage()
    {
        $data = array(
            'field1' => '',
        );
        $validators = array(
            'field1Rule' => array(
                Zend_Filter_Input::ALLOW_EMPTY => false,
                'fields' => 'field1'
            )
        );

        $options = array(
            Zend_Filter_Input::NOT_EMPTY_MESSAGE => "You cannot give an empty value for field '%field%', according to rule '%rule%'"
        );

        $input = new Zend_Filter_Input(null, $validators, $data, $options);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $this->assertNull($input->field1);
        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('field1Rule'), array_keys($messages));
        $this->assertType('array', $messages['field1Rule']);
        $this->assertEquals("You cannot give an empty value for field 'field1', according to rule 'field1Rule'", $messages['field1Rule'][0]);
    }

    public function testValidatorMessagesSingle()
    {
        $data = array('month' => '13abc');
        $digitsMesg = 'Month should consist of digits';
        $validators = array(
            'month' => array(
                'digits',
                'messages' => $digitsMesg
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('month'), array_keys($messages));
        $this->assertEquals(1, count($messages['month']));
        $this->assertEquals($digitsMesg, $messages['month'][0]);
    }

    public function testValidatorMessagesMultiple()
    {
        $data = array('month' => '13abc');
        $digitsMesg = 'Month should consist of digits';
        $betweenMesg = 'Month should be between 1 and 12';
        Zend_Loader::loadClass('Zend_Validate_Between');
        $validators = array(
            'month' => array(
                'digits',
                new Zend_Validate_Between(1, 12),
                'messages' => array(
                    $digitsMesg,
                    $betweenMesg
                )
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('month'), array_keys($messages));
        $this->assertEquals(2, count($messages['month']));
        // $this->assertEquals($digitsMesg, $messages['month'][0]);
        // $this->assertEquals($betweenMesg, $messages['month'][1]);
    }

    public function testValidatorMessagesIntIndex()
    {
        $data = array('month' => '13abc');
        $betweenMesg = 'Month should be between 1 and 12';
        Zend_Loader::loadClass('Zend_Validate_Between');
        $validators = array(
            'month' => array(
                'digits',
                new Zend_Validate_Between(1, 12),
                'messages' => array(
                    1 => $betweenMesg
                )
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('month'), array_keys($messages));
        $this->assertEquals(2, count($messages['month']));
        $this->assertEquals("'13abc' contains not only digit characters", $messages['month'][0]);
        // $this->assertEquals($betweenMesg, $messages['month'][1]);
    }

    public function testValidatorMessagesSingleWithKeys()
    {
        $data = array('month' => '13abc');
        $digitsMesg = 'Month should consist of digits';
        $validators = array(
            'month' => array(
                'digits',
                'messages' => array('notDigits' => $digitsMesg)
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('month'), array_keys($messages));
        $this->assertEquals(1, count($messages['month']));
        // $this->assertEquals($digitsMesg, $messages['month'][0]);
    }

    public function testValidatorMessagesMultipleWithKeys()
    {
        $data = array('month' => '13abc');
        $digitsMesg = 'Month should consist of digits';
        $betweenMesg = 'Month should be between 1 and 12';
        Zend_Loader::loadClass('Zend_Validate_Between');
        $validators = array(
            'month' => array(
                'digits',
                new Zend_Validate_Between(1, 12),
                'messages' => array(
                    array('notDigits' => $digitsMesg),
                    array('notBetween' => $betweenMesg)
                )
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('month'), array_keys($messages));
        $this->assertEquals(2, count($messages['month']));
        // $this->assertEquals($digitsMesg, $messages['month'][0]);
        // $this->assertEquals($betweenMesg, $messages['month'][1]);
    }

    public function testValidatorMessagesMixedWithKeys()
    {
        $data = array('month' => '13abc');
        $digitsMesg = 'Month should consist of digits';
        $betweenMesg = 'Month should be between 1 and 12';
        Zend_Loader::loadClass('Zend_Validate_Between');
        $validators = array(
            'month' => array(
                'digits',
                new Zend_Validate_Between(1, 12),
                'messages' => array(
                    $digitsMesg,
                    array('notBetween' => $betweenMesg)
                )
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('month'), array_keys($messages));
        $this->assertEquals(2, count($messages['month']));
        // $this->assertEquals($digitsMesg, $messages['month'][0]);
        // $this->assertEquals($betweenMesg, $messages['month'][1]);
    }

    public function testValidatorHasMissing()
    {
        $data = array();
        $validators = array(
            'month' => array(
                'digits',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return true');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');
    }

    public function testValidatorFieldOptional()
    {
        $data = array();
        $validators = array(
            'month' => array(
                'digits',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');
    }

    public function testValidatorGetMissing()
    {
        $data = array();
        $validators = array(
            'month' => array(
                'digits',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return true');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $missing = $input->getMissing();
        $this->assertType('array', $missing);
        $this->assertEquals(array('month'), array_keys($missing));
        $this->assertEquals("Field 'month' is required by rule 'month', but the field is missing", $missing['month'][0]);
    }

    public function testValidatorSetMissingMessage()
    {
        $data = array();
        $validators = array(
            'monthRule' => array(
                'digits',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                'fields' => 'month'
            )
        );
        $options = array(
            Zend_Filter_Input::MISSING_MESSAGE => 'I looked for %field% but I did not find it; it is required by rule %rule%'
        );
        $input = new Zend_Filter_Input(null, $validators, $data, $options);

        $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return true');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $missing = $input->getMissing();
        $this->assertType('array', $missing);
        $this->assertEquals(array('monthRule'), array_keys($missing));
        $this->assertEquals("I looked for month but I did not find it; it is required by rule monthRule", $missing['monthRule'][0]);
    }

    public function testValidatorHasUnknown()
    {
        $data = array(
            'unknown' => 'xxx'
        );
        $validators = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expecting hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expecting hasInvalid() to return false');
        $this->assertTrue($input->hasUnknown(), 'Expecting hasUnknown() to return true');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');
    }

    public function testValidatorGetUnknown()
    {
        $data = array(
            'unknown' => 'xxx'
        );
        $validators = array(
            'month' => 'digits'
        );
        $input = new Zend_Filter_Input(null, $validators, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertTrue($input->hasUnknown(), 'Expected hasUnknown() to retrun true');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $unknown = $input->getUnknown();
        $this->assertType('array', $unknown);
        $this->assertThat($unknown, $this->arrayHasKey('unknown'));
    }

    public function testAddNamespace()
    {
        $data = array(
            'field1' => 'abc',
            'field2' => '123',
            'field3' => '123'
        );
        $validators = array(
            'field1' => 'MyDigits',
            'field2' => 'MyDigits',
            'field3' => 'digits'
        );
        $options = array(
            Zend_Filter_Input::NAMESPACE => 'TestNamespace'
        );

        $ip = get_include_path();
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files';
        $newIp = $dir . PATH_SEPARATOR . $ip;
        set_include_path($newIp);

        $input = new Zend_Filter_Input(null, $validators, $data);
        $input->addNamespace('TestNamespace');

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');
        set_include_path($ip);

        $this->assertEquals('123', (string) $input->field2);
        $this->assertEquals('123', (string) $input->field3);

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertThat($messages, $this->arrayHasKey('field1'));
        $this->assertEquals("'abc' contains not only digit characters", $messages['field1'][0]);
    }

    public function testNamespaceExceptionClassNotFound()
    {
        $data = array(
            'field1' => 'abc'
        );
        $validators = array(
            'field1' => 'MyDigits'
        );
        // Do not add namespace on purpose, so MyDigits will not be found
        $input = new Zend_Filter_Input(null, $validators, $data);
        try {
            $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Could not find a class based on name 'MyDigits' implementing Zend_Validate_Interface",
                $e->getMessage());
        }
    }

    public function testSetDefaultEscapeFilter()
    {
        $data = array(
            'field1' => ' ab&c '
        );
        $options = array(
            Zend_Filter_Input::ESCAPE_FILTER => 'StringTrim'
        );
        $input = new Zend_Filter_Input(null, null, $data);
        $input->setDefaultEscapeFilter('StringTrim');

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals('ab&c', $input->field1);
    }

    public function testSetDefaultEscapeFilterExceptionWrongClassType()
    {
        $input = new Zend_Filter_Input(null, null);
        try {
            $input->setDefaultEscapeFilter(new StdClass());
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Escape filter specified does not implement Zend_Filter_Interface", $e->getMessage());
        }
    }

    public function testOptionAllowEmpty()
    {
        $data = array(
            'field1' => ''
        );
        $validators = array(
            'field1' => 'alpha'
        );
        $options = array(
            Zend_Filter_Input::ALLOW_EMPTY => true
        );
        $input = new Zend_Filter_Input(null, $validators, $data, $options);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertNotNull($input->field1);
        $this->assertEquals('', $input->field1);
    }

    public function testOptionBreakChain()
    {
        $data = array(
            'field1' => '150'
        );
        Zend_Loader::loadClass('Zend_Validate_Between');
        $btw1 = new Zend_Validate_Between(1, 100);
        $btw2 = new Zend_Validate_Between(1, 125);
        $validators = array(
            'field1' => array($btw1, $btw2),
        );
        $options = array(
            Zend_Filter_Input::BREAK_CHAIN => true
        );
        $input = new Zend_Filter_Input(null, $validators, $data, $options);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertFalse($input->hasValid(), 'Expected hasValid() to return false');

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertEquals(array('field1'), array_keys($messages));
        $this->assertEquals(1, count($messages['field1']), 'Expected rule for field1 to break 1 validator');
        $this->assertEquals("'150' is not between '1' and '100', inclusively",
            $messages['field1'][0]);
    }

    public function testOptionEscapeFilter()
    {
        $data = array(
            'field1' => ' ab&c '
        );
        $options = array(
            Zend_Filter_Input::ESCAPE_FILTER => 'StringTrim'
        );
        $input = new Zend_Filter_Input(null, null, $data, $options);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals('ab&c', $input->field1);
    }

    public function testOptionNamespace()
    {
        $data = array(
            'field1' => 'abc',
            'field2' => '123',
            'field3' => '123'
        );
        $validators = array(
            'field1' => 'MyDigits',
            'field2' => 'MyDigits',
            'field3' => 'digits'
        );
        $options = array(
            Zend_Filter_Input::NAMESPACE => 'TestNamespace'
        );

        $ip = get_include_path();
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files';
        $newIp = $dir . PATH_SEPARATOR . $ip;
        set_include_path($newIp);

        $input = new Zend_Filter_Input(null, $validators, $data, $options);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');
        set_include_path($ip);

        $this->assertEquals('123', (string) $input->field2);
        $this->assertEquals('123', (string) $input->field3);

        $messages = $input->getMessages();
        $this->assertType('array', $messages);
        $this->assertThat($messages, $this->arrayHasKey('field1'));
        $this->assertEquals("'abc' contains not only digit characters", $messages['field1'][0]);
    }

    public function testOptionPresence()
    {
        $data = array(
            'field1' => '123'
            // field2 is missing deliberately
        );
        $validators = array(
            'field1' => 'Digits',
            'field2' => 'Digits'
        );
        $options = array(
            Zend_Filter_Input::PRESENCE => true
        );
        $input = new Zend_Filter_Input(null, $validators, $data, $options);

        $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $missing = $input->getMissing();
        $this->assertType('array', $missing);
        $this->assertEquals(array('field2'), array_keys($missing));
        $this->assertEquals("Field 'field2' is required by rule 'field2', but the field is missing", $missing['field2'][0]);
    }

    public function testOptionExceptionUnknown()
    {
        $options = array(
            'unknown' => 'xxx'
        );
        try {
            $input = new Zend_Filter_Input(null, null, null, $options);
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Unknown option 'unknown'", $e->getMessage());
        }
    }

    public function testGetEscaped()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals('ab&amp;c', $input->getEscaped('field1'));
        $this->assertNull($input->getEscaped('field2'));
    }

    public function testGetEscapedAllFields()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals(array('field1' => 'ab&amp;c'), $input->getEscaped());
    }

    public function testMagicGetEscaped()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals('ab&amp;c', $input->field1);
        $this->assertNull($input->field2);
    }

    public function testGetEscapedMultiValue()
    {
        $data = array(
            'multiSelect' => array('C&H', 'B&O', 'AT&T')
        );
        $input = new Zend_Filter_Input(null, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $multi = $input->getEscaped('multiSelect');
        $this->assertType('array', $multi);
        $this->assertEquals(3, count($multi));
        $this->assertEquals(array('C&amp;H', 'B&amp;O', 'AT&amp;T'), $multi);
    }

    public function testGetUnescaped()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals('ab&c', $input->getUnescaped('field1'));
        $this->assertNull($input->getUnescaped('field2'));
    }

    public function testGetUnescapedAllFields()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertEquals(array('field1' => 'ab&c'), $input->getUnescaped());
    }

    public function testMagicIsset()
    {
        $data = array(
            'field1' => 'ab&c'
        );
        $input = new Zend_Filter_Input(null, null, $data);

        $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
        $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
        $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
        $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');

        $this->assertTrue(isset($input->field1));
        $this->assertFalse(isset($input->field2));
    }

    public function testProcess()
    {
        $data = array(
            'field1' => 'ab&c',
            'field2' => '123abc'
        );
        $filters = array(
            '*'      => 'StringTrim',
            'field2' => 'digits'
        );
        $validators = array(
            'field1' => array(Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL),
            'field2' => array(
                'digits', 
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input($filters, $validators, $data);
        try {
            $input->process();
            $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
            $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
            $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
            $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');
        } catch (Zend_Exception $e) {
            $this->fail('Received Zend_Exception where none was expected');
        }
    }

    public function testProcessUnknownThrowsNoException()
    {
        $data = array(
            'field1' => 'ab&c',
            'field2' => '123abc',
            'field3' => 'unknown'
        );
        $filters = array(
            '*'      => 'StringTrim',
            'field2' => 'digits'
        );
        $validators = array(
            'field1' => array(Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL),
            'field2' => array(
                'digits', 
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input($filters, $validators, $data);
        try {
            $input->process();
            $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
            $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
            $this->assertTrue($input->hasUnknown(), 'Expected hasUnknown() to retrun true');
            $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');
        } catch (Zend_Exception $e) {
            $this->fail('Received Zend_Exception where none was expected');
        }
    }

    public function testProcessInvalidThrowsException()
    {
        $data = array(
            'field1' => 'ab&c',
            'field2' => 'abc' // invalid because no digits
        );
        $filters = array(
            '*'      => 'StringTrim',
            'field2' => 'digits'
        );
        $validators = array(
            'field1' => array(Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL),
            'field2' => array(
                'digits', 
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input($filters, $validators, $data);
        try {
            $input->process();
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Input has invalid fields", $e->getMessage());
            $this->assertFalse($input->hasMissing(), 'Expected hasMissing() to return false');
            $this->assertTrue($input->hasInvalid(), 'Expected hasInvalid() to return true');
            $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
            $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');
        }
    }

    public function testProcessMissingThrowsException()
    {
        $data = array(
            'field1' => 'ab&c'
            // field2 is missing on purpose for this test
        );
        $filters = array(
            '*'      => 'StringTrim',
            'field2' => 'digits'
        );
        $validators = array(
            'field1' => array(
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL
            ),
            'field2' => array(
                'digits', 
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );
        $input = new Zend_Filter_Input($filters, $validators, $data);
        try {
            $input->process();
            $this->fail('Expected to catch Zend_Filter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Filter_Exception', $e,
                'Expected object of type Zend_Filter_Exception, got '.get_class($e));
            $this->assertEquals("Input has missing fields", $e->getMessage());
            $this->assertTrue($input->hasMissing(), 'Expected hasMissing() to return true');
            $this->assertFalse($input->hasInvalid(), 'Expected hasInvalid() to return false');
            $this->assertFalse($input->hasUnknown(), 'Expected hasUnknown() to return false');
            $this->assertTrue($input->hasValid(), 'Expected hasValid() to return true');
        }
    }

}
