<?php
// Call Zend_View_Helper_HeadStyleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HeadStyleTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_HeadStyle */
require_once 'Zend/View/Helper/HeadStyle.php';

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_HeadStyle.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_HeadStyleTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_HeadStyle
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HeadStyleTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $regKey = Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY;
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_HeadStyle();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testNamespaceRegisteredInPlaceholderRegistryAfterInstantiation()
    {
        $registry = Zend_View_Helper_Placeholder_Registry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_HeadStyle')) {
            $registry->deleteContainer('Zend_View_Helper_HeadStyle');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_HeadStyle'));
        $helper = new Zend_View_Helper_HeadStyle();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadStyle'));
    }

    public function testHeadStyleReturnsObjectInstance()
    {
        $placeholder = $this->helper->headStyle();
        $this->assertTrue($placeholder instanceof Zend_View_Helper_HeadStyle);
    }

    public function testAppendPrependAndSetThrowExceptionsWhenNonStyleValueProvided()
    {
        try {
            $this->helper->append('foo');
            $this->fail('Non-style value should not append');
        } catch (Zend_View_Exception $e) { }
        try {
            $this->helper->offsetSet(5, 'foo');
            $this->fail('Non-style value should not offsetSet');
        } catch (Zend_View_Exception $e) { }
        try {
            $this->helper->prepend('foo');
            $this->fail('Non-style value should not prepend');
        } catch (Zend_View_Exception $e) { }
        try {
            $this->helper->set('foo');
            $this->fail('Non-style value should not set');
        } catch (Zend_View_Exception $e) { }
    }

    public function testOverloadAppendStyleAppendsStyleToStack()
    {
        $string = 'a {}';
        for ($i = 0; $i < 3; ++$i) {
            $string .= PHP_EOL . 'a {}';
            $this->helper->appendStyle($string);
            $values = $this->helper->getArrayCopy();
            $this->assertEquals($i + 1, count($values));
            $item = $values[$i];

            $this->assertTrue($item instanceof stdClass);
            $this->assertObjectHasAttribute('content', $item);
            $this->assertObjectHasAttribute('attributes', $item);
            $this->assertEquals($string, $item->content);
        }
    }

    public function testOverloadPrependStylePrependsStyleToStack()
    {
        $string = 'a {}';
        for ($i = 0; $i < 3; ++$i) {
            $string .= PHP_EOL . 'a {}';
            $this->helper->prependStyle($string);
            $values = $this->helper->getArrayCopy();
            $this->assertEquals($i + 1, count($values));
            $item = array_shift($values);

            $this->assertTrue($item instanceof stdClass);
            $this->assertObjectHasAttribute('content', $item);
            $this->assertObjectHasAttribute('attributes', $item);
            $this->assertEquals($string, $item->content);
        }
    }

    public function testOverloadSetOversitesStack()
    {
        $string = 'a {}';
        for ($i = 0; $i < 3; ++$i) {
            $this->helper->appendStyle($string);
            $string .= PHP_EOL . 'a {}';
        }
        $this->helper->setStyle($string);
        $values = $this->helper->getArrayCopy();
        $this->assertEquals(1, count($values));
        $item = array_shift($values);

        $this->assertTrue($item instanceof stdClass);
        $this->assertObjectHasAttribute('content', $item);
        $this->assertObjectHasAttribute('attributes', $item);
        $this->assertEquals($string, $item->content);
    }

    public function testCanBuildStyleTagsWithAttributes()
    {
        $this->helper->setStyle('a {}', array(
            'lang'  => 'us_en', 
            'title' => 'foo', 
            'media' => 'screen', 
            'dir'   => 'rtol', 
            'bogus' => 'unused'
        ));
        $value = $this->helper->getValue();

        $this->assertObjectHasAttribute('attributes', $value);
        $attributes = $value->attributes;

        $this->assertTrue(isset($attributes['lang']));
        $this->assertTrue(isset($attributes['title']));
        $this->assertTrue(isset($attributes['media']));
        $this->assertTrue(isset($attributes['dir']));
        $this->assertTrue(isset($attributes['bogus']));
        $this->assertEquals('us_en', $attributes['lang']);
        $this->assertEquals('foo', $attributes['title']);
        $this->assertEquals('screen', $attributes['media']);
        $this->assertEquals('rtol', $attributes['dir']);
        $this->assertEquals('unused', $attributes['bogus']);
    }

    public function testCanBuildStyleTagsWithCdataEscaping()
    {
        $this->helper->useCdata = true;
        $this->helper->setStyle('a {}', array(
            'lang'  => 'us_en', 
            'title' => 'foo', 
            'media' => 'screen', 
            'dir'   => 'rtol', 
            'bogus' => 'unused'
        ));
        $value = $this->helper->toString();
        $this->assertContains('<![CDATA[', $value);
        $this->assertContains(']]>', $value);
        $this->assertNotContains('<!--', $value);
        $this->assertNotContains('-->', $value);
    }

    public function testHeadStyleProxiesProperly()
    {
        $style1 = 'a {}';
        $style2 = 'a {}' . PHP_EOL . 'h1 {}';
        $style3 = 'a {}' . PHP_EOL . 'h2 {}';

        $this->helper->headStyle($style1, 'SET')
                     ->headStyle($style2, 'PREPEND')
                     ->headStyle($style3, 'APPEND');
        $this->assertEquals(3, count($this->helper));
        $values = $this->helper->getArrayCopy();
        $this->assertTrue((strstr($values[0]->content, $style2)) ? true : false);
        $this->assertTrue((strstr($values[1]->content, $style1)) ? true : false);
        $this->assertTrue((strstr($values[2]->content, $style3)) ? true : false);
    }

    public function testToStyleGeneratesValidHtml()
    {
        $style1 = 'a {}';
        $style2 = 'body {}' . PHP_EOL . 'h1 {}';
        $style3 = 'div {}' . PHP_EOL . 'li {}';

        $this->helper->headStyle($style1, 'SET')
                     ->headStyle($style2, 'PREPEND')
                     ->headStyle($style3, 'APPEND');
        $html = $this->helper->toString();
        $doc  = new DOMDocument;
        $dom  = $doc->loadHtml($html);
        $this->assertTrue(($dom !== false));

        $styles = substr_count($html, '<style type="text/css"');
        $this->assertEquals(3, $styles);
        $styles = substr_count($html, '</style>');
        $this->assertEquals(3, $styles);
        $this->assertContains($style3, $html);
        $this->assertContains($style2, $html);
        $this->assertContains($style1, $html);
    }
}

// Call Zend_View_Helper_HeadStyleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HeadStyleTest::main") {
    Zend_View_Helper_HeadStyleTest::main();
}
