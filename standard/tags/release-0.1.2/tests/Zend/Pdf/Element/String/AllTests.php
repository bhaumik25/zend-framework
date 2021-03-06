<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Pdf_Element_String_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Pdf/Element/String/BinaryTest.php';

class Zend_Pdf_Element_String_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Pdf_Element_String');

        $suite->addTestSuite('Zend_Pdf_Element_String_BinaryTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Pdf_Element_String_AllTests::main') {
    Zend_Pdf_Element_String_AllTests::main();
}
