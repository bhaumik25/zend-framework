<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_Element_AllTests::main');
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Zend/Form/Element/ButtonTest.php';
require_once 'Zend/Form/Element/CheckboxTest.php';
require_once 'Zend/Form/Element/HiddenTest.php';
require_once 'Zend/Form/Element/ImageTest.php';
require_once 'Zend/Form/Element/MultiselectTest.php';
require_once 'Zend/Form/Element/ResetTest.php';
require_once 'Zend/Form/Element/SelectTest.php';
require_once 'Zend/Form/Element/SubmitTest.php';
require_once 'Zend/Form/Element/TextareaTest.php';
require_once 'Zend/Form/Element/TextTest.php';

class Zend_Form_Element_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Form');

        $suite->addTestSuite('Zend_Form_Element_ButtonTest');
        $suite->addTestSuite('Zend_Form_Element_CheckboxTest');
        $suite->addTestSuite('Zend_Form_Element_HiddenTest');
        $suite->addTestSuite('Zend_Form_Element_ImageTest');
        $suite->addTestSuite('Zend_Form_Element_MultiselectTest');
        $suite->addTestSuite('Zend_Form_Element_ResetTest');
        $suite->addTestSuite('Zend_Form_Element_SelectTest');
        $suite->addTestSuite('Zend_Form_Element_SubmitTest');
        $suite->addTestSuite('Zend_Form_Element_TextareaTest');
        $suite->addTestSuite('Zend_Form_Element_TextTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_Element_AllTests::main') {
    Zend_Form_Element_AllTests::main();
}
