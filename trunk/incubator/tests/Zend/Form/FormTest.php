<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_FormTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Zend/Form.php';

require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Form/Element.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Form/SubForm.php';
require_once 'Zend/Loader/PluginLoader.php';

class Zend_Form_FormTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Form_FormTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->form = new Zend_Form();
    }

    public function tearDown()
    {
    }

    // Configuration

    public function testSetOptionsSetsObjectState()
    {
        $this->markTestIncomplete();
    }

    public function testSetConfigSetsObjectState()
    {
        $this->markTestIncomplete();
    }

    // Attribs:

    public function testAttribsArrayInitiallyEmpty()
    {
        $attribs = $this->form->getAttribs();
        $this->assertTrue(is_array($attribs));
        $this->assertTrue(empty($attribs));
    }

    public function testRetrievingUndefinedAttribReturnsNull()
    {
        $this->assertNull($this->form->getAttrib('foo'));
    }
    
    public function testCanAddAndRetrieveSingleAttribs()
    {
        $this->testRetrievingUndefinedAttribReturnsNull();
        $this->form->setAttrib('foo', 'bar');
        $this->assertEquals('bar', $this->form->getAttrib('foo'));
    }

    public function testCanAddAndRetrieveMultipleAttribs()
    {
        $this->form->setAttrib('foo', 'bar');
        $this->assertEquals('bar', $this->form->getAttrib('foo'));
        $this->form->addAttribs(array(
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'foo'
        ));
        $test = $this->form->getAttribs();
        $attribs = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'foo'
        );
        $this->assertSame($attribs, $test);
    }

    public function testSetAttribsOverwritesExistingAttribs()
    {
        $this->testCanAddAndRetrieveMultipleAttribs();
        $array = array('bogus' => 'value', 'not' => 'real');
        $this->form->setAttribs($array);
        $this->assertSame($array, $this->form->getAttribs());
    }

    public function testCanRemoveSingleAttrib()
    {
        $this->testCanAddAndRetrieveSingleAttribs();
        $this->assertTrue($this->form->removeAttrib('foo'));
        $this->assertNull($this->form->getAttrib('foo'));
    }

    public function testRemoveAttribReturnsFalseIfAttribDoesNotExist()
    {
        $this->assertFalse($this->form->removeAttrib('foo'));
    }

    public function testCanClearAllAttribs()
    {
        $this->testCanAddAndRetrieveMultipleAttribs();
        $this->form->clearAttribs();
        $attribs = $this->form->getAttribs();
        $this->assertTrue(is_array($attribs));
        $this->assertTrue(empty($attribs));
    }

    // Plugin loaders

    public function testGetPluginLoaderRetrievesDefaultDecoratorPluginLoader()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Form_Decorator');
        $this->assertTrue(is_array($paths), var_export($loader, 1));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Form', $paths[0]);
        $this->assertContains('Decorator', $paths[0]);
    }

    public function testCanSetCustomDecoratorPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($loader, 'decorator');
        $test = $this->form->getPluginLoader('decorator');
        $this->assertSame($loader, $test);
    }

    public function testCanAddDecoratorPluginLoaderPrefixPath()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testAddDecoratorPluginLoaderPrefixPathUpdatesElementDecoratorLoaders()
    {
        $this->setupElements();
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $loader = $this->form->foo->getPluginLoader('decorator');
        $paths  = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $found = false;
        foreach ($paths as $path) {
            if (strstr($path, 'Foo')) {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    public function testUpdatedDecoratorPluginLoaderPrefixPathUsedForNewElements()
    {
        $this->markTestIncomplete();
    }

    public function testGetPluginLoaderRetrievesDefaultElementPluginLoader()
    {
        $loader = $this->form->getPluginLoader('element');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Form_Element');
        $this->assertTrue(is_array($paths), var_export($loader, 1));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Form', $paths[0]);
        $this->assertContains('Element', $paths[0]);
    }

    public function testCanSetCustomDecoratorElementLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($loader, 'element');
        $test = $this->form->getPluginLoader('element');
        $this->assertSame($loader, $test);
    }

    public function testCanAddElementPluginLoaderPrefixPath()
    {
        $loader = $this->form->getPluginLoader('element');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'element');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testAddAllPluginLoaderPrefixPathsSimultaneously()
    {
        $decoratorLoader = new Zend_Loader_PluginLoader();
        $elementLoader   = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($decoratorLoader, 'decorator')
                   ->setPluginLoader($elementLoader, 'element')
                   ->addPrefixPath('Zend', 'Zend/');

        $paths = $decoratorLoader->getPaths('Zend_Decorator');
        $this->assertTrue(is_array($paths), var_export($paths, 1));
        $this->assertContains('Decorator', $paths[0]);

        $paths = $elementLoader->getPaths('Zend_Element');
        $this->assertTrue(is_array($paths), var_export($paths, 1));
        $this->assertContains('Element', $paths[0]);
    }

    public function testAddingGlobalPrefixPathUpdatesAllElementPluginLoaders()
    {
        $this->markTestIncomplete();
    }

    // Elements:

    public function testCanAddAndRetrieveSingleElements()
    {
        $element = new Zend_Form_Element('foo');
        $this->form->addElement($element);
        $this->assertSame($element, $this->form->getElement('foo'));
    }

    public function testGetElementReturnsNullForUnregisteredElement()
    {
        $this->assertNull($this->form->getElement('foo'));
    }

    public function testCanAddAndRetrieveSingleElementsByStringType()
    {
        $this->form->addElement('text', 'foo');
        $element = $this->form->getElement('foo');
        $this->assertTrue($element instanceof Zend_Form_Element);
        $this->assertTrue($element instanceof Zend_Form_Element_Text);
        $this->assertEquals('foo', $element->getName());
    }

    public function testAddElementAsStringElementThrowsExceptionWhenNoNameProvided()
    {
        try {
            $this->form->addElement('text');
            $this->fail('Should not be able to specify string element type without name');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('must have', $e->getMessage());
        }
    }

    public function testCanAddAndRetrieveMultipleElements()
    {
        $this->form->addElements(array(
            'foo' => 'text',
            array('text', 'bar'),
            array('text', 'baz', array('foo' => 'bar')),
            new Zend_Form_Element_Text('bat'),
        ));
        $elements = $this->form->getElements();
        $names = array('foo', 'bar', 'baz', 'bat');
        $this->assertEquals($names, array_keys($elements));
        $foo = $elements['foo'];
        $this->assertTrue($foo instanceof Zend_Form_Element_Text);
        $bar = $elements['bar'];
        $this->assertTrue($bar instanceof Zend_Form_Element_Text);
        $baz = $elements['baz'];
        $this->assertTrue($baz instanceof Zend_Form_Element_Text);
        $this->assertEquals('bar', $baz->foo, var_export($baz->getAttribs(), 1));
        $bat = $elements['bat'];
        $this->assertTrue($bat instanceof Zend_Form_Element_Text);
    }

    public function testSetElementsOverwritesExistingElements()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->setElements(array(
            'bogus' => 'text'
        ));
        $elements = $this->form->getElements();
        $names = array('bogus');
        $this->assertEquals($names, array_keys($elements));
    }

    public function testCanRemoveSingleElement()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->assertTrue($this->form->removeElement('bar'));
        $this->assertNull($this->form->getElement('bar'));
    }

    public function testRemoveElementReturnsFalseWhenElementNotRegistered()
    {
        $this->assertFalse($this->form->removeElement('bogus'));
    }

    public function testCanClearAllElements()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->clearElements();
        $elements = $this->form->getElements();
        $this->assertTrue(is_array($elements));
        $this->assertTrue(empty($elements));
    }

    public function testCanSetElementDefaultValues()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $values = array(
            'foo' => 'foovalue',
            'bar' => 'barvalue',
            'baz' => 'bazvalue',
            'bat' => 'batvalue'
        );
        $this->form->setDefaults($values);
        $elements = $this->form->getElements();
        foreach (array_keys($values) as $name) {
            $this->assertEquals($name . 'value', $elements[$name]->getValue(), var_export($elements[$name], 1));
        }
    }

    public function testCanRetrieveSingleElementValue()
    {
        $this->form->addElement('text', 'foo', array('value' => 'foovalue'));
        $this->assertEquals('foovalue', $this->form->getValue('foo'));
    }

    public function testCanRetrieveAllElementValues()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $values = array(
            'foo' => 'foovalue',
            'bar' => 'barvalue',
            'baz' => 'bazvalue',
            'bat' => 'batvalue'
        );
        $this->form->setDefaults($values);
        $test     = $this->form->getValues();
        $elements = $this->form->getElements();
        foreach (array_keys($values) as $name) {
            $this->assertEquals($values[$name], $test[$name]);
        }
    }

    public function testCanRetrieveSingleUnfilteredElementValue()
    {
        $foo = new Zend_Form_Element_Text('foo');
        $foo->addFilter('StringToUpper')
            ->setValue('foovalue');
        $this->form->addElement($foo);
        $this->assertEquals('FOOVALUE', $this->form->getValue('foo'));
        $this->assertEquals('foovalue', $this->form->getUnfilteredValue('foo'));
    }

    public function testCanRetrieveAllUnfilteredElementValues()
    {
        $foo = new Zend_Form_Element_Text('foo');
        $foo->addFilter('StringToUpper')
            ->setValue('foovalue');
        $bar = new Zend_Form_Element_Text('bar');
        $bar->addFilter('StringToUpper')
            ->setValue('barvalue');
        $this->form->addElements(array($foo, $bar));
        $values     = $this->form->getValues();
        $unfiltered = $this->form->getUnfilteredValues();
        foreach (array('foo', 'bar') as $key) {
            $value = $key . 'value';
            $this->assertEquals(strtoupper($value), $values[$key]);
            $this->assertEquals($value, $unfiltered[$key]);
        }
    }

    public function testOverloadingElements()
    {
        $this->form->addElement('text', 'foo');
        $this->assertTrue(isset($this->form->foo));
        $element = $this->form->foo;
        $this->assertTrue($element instanceof Zend_Form_Element);
        unset($this->form->foo);
        $this->assertFalse(isset($this->form->foo));

        $bar = new Zend_Form_Element_Text('bar');
        $this->form->bar = $bar;
        $this->assertTrue(isset($this->form->bar));
        $element = $this->form->bar;
        $this->assertSame($bar, $element);
    }

    // Element groups

    public function testCanAddAndRetrieveSingleSubForm()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $this->form->addSubForm($subForm, 'page1');
        $test = $this->form->getSubForm('page1');
        $this->assertSame($subForm, $test);
    }

    public function testGetSubFormReturnsNullForUnregisteredSubForm()
    {
        $this->assertNull($this->form->getSubForm('foo'));
    }

    public function testCanAddAndRetrieveMultipleSubForms()
    {
        $page1 = new Zend_Form_SubForm();
        $page2 = new Zend_Form_SubForm();
        $page3 = new Zend_Form_SubForm();
        $this->form->addSubForms(array(
            'page1' => $page1,
            array($page2, 'page2'),
            array($page3, 'page3', 3)
        ));
        $subforms = $this->form->getSubForms();
        $keys = array('page1', 'page2', 'page3');
        $this->assertEquals($keys, array_keys($subforms));
        $this->assertSame($page1, $subforms['page1']);
        $this->assertSame($page2, $subforms['page2']);
        $this->assertSame($page3, $subforms['page3']);
    }

    public function testSetSubFormsOverwritesExistingSubForms()
    {
        $this->testCanAddAndRetrieveMultipleSubForms();
        $foo = new Zend_Form_SubForm();
        $this->form->setSubForms(array('foo' => $foo));
        $subforms = $this->form->getSubForms();
        $keys = array('foo');
        $this->assertEquals($keys, array_keys($subforms));
        $this->assertSame($foo, $subforms['foo']);
    }

    public function testCanRemoveSingleSubForm()
    {
        $this->testCanAddAndRetrieveMultipleSubForms();
        $this->assertTrue($this->form->removeSubForm('page2'));
        $this->assertNull($this->form->getSubForm('page2'));
    }

    public function testRemoveSubFormReturnsFalseForNonexistantSubForm()
    {
        $this->assertFalse($this->form->removeSubForm('foo'));
    }

    public function testCanClearAllSubForms()
    {
        $this->markTestIncomplete();
        $this->testCanAddAndRetrieveMultipleSubForms();
        $this->form->clearSubForms();
        $subforms = $this->form->getSubForms();
        $this->assertTrue(is_array($subforms));
        $this->assertTrue(empty($subforms));
    }

    public function testOverloadingSubForms()
    {
        $foo = new Zend_Form_SubForm;
        $this->form->addSubForm($foo, 'foo');
        $this->assertTrue(isset($this->form->foo));
        $subform = $this->form->foo;
        $this->assertSame($foo, $subform);
        unset($this->form->foo);
        $this->assertFalse(isset($this->form->foo));

        $bar = new Zend_Form_SubForm();
        $this->form->bar = $bar;
        $this->assertTrue(isset($this->form->bar));
        $subform = $this->form->bar;
        $this->assertSame($bar, $subform);
    }

    // Display groups

    public function testCanAddAndRetrieveSingleDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->addDisplayGroup(array('bar', 'bat'), 'barbat');
        $group = $this->form->getDisplayGroup('barbat');
        $this->assertTrue($group instanceof Zend_Form_DisplayGroup);
        $elements = $group->getElements();
        $expected = array('bar' => $this->form->bar, 'bat' => $this->form->bat);
        $this->assertEquals($expected, $elements);
    }

    public function testCanAddAndRetrieveMultipleDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->addDisplayGroups(array(
            array(array('bar', 'bat'), 'barbat'),
            'foobaz' => array('baz', 'foo')
        ));
        $groups = $this->form->getDisplayGroups();
        $expected = array(
            'barbat' => array('bar' => $this->form->bar, 'bat' => $this->form->bat),
            'foobaz' => array('baz' => $this->form->baz, 'foo' => $this->form->foo),
        );
        foreach ($groups as $group) {
            $this->assertTrue($group instanceof Zend_Form_DisplayGroup);
        }
        $this->assertEquals($expected['barbat'], $groups['barbat']->getElements());
        $this->assertEquals($expected['foobaz'], $groups['foobaz']->getElements());
    }

    public function testSetDisplayGroupsOverwritesExistingDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleDisplayGroups();
        $this->form->setDisplayGroups(array('foobar' => array('bar', 'foo')));
        $groups = $this->form->getDisplayGroups();
        $expected = array('bar' => $this->form->bar, 'foo' => $this->form->foo);
        $this->assertEquals(1, count($groups));
        $this->assertTrue(isset($groups['foobar']));
        $this->assertEquals($expected, $groups['foobar']->getElements());
    }

    public function testCanRemoveSingleDisplayGroup()
    {
        $this->testCanAddAndRetrieveMultipleDisplayGroups();
        $this->assertTrue($this->form->removeDisplayGroup('barbat'));
        $this->assertNull($this->form->getDisplayGroup('barbat'));
    }

    public function testRemoveDisplayGroupReturnsFalseForNonexistantGroup()
    {
        $this->assertFalse($this->form->removeDisplayGroup('bogus'));
    }

    public function testCanClearAllDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleDisplayGroups();
        $this->form->clearDisplayGroups();
        $groups = $this->form->getDisplayGroups();
        $this->assertTrue(is_array($groups));
        $this->assertTrue(empty($groups));
    }

    public function testOverloadingDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->addDisplayGroup(array('foo', 'bar'), 'foobar');
        $this->assertTrue(isset($this->form->foobar));
        $group = $this->form->foobar;
        $expected = array('foo' => $this->form->foo, 'bar' => $this->form->bar);
        $this->assertEquals($expected, $group->getElements());
        unset($this->form->foobar);
        $this->assertFalse(isset($this->form->foobar));

        $this->form->barbaz = array('bar', 'baz');
        $this->assertTrue(isset($this->form->barbaz));
        $group = $this->form->barbaz;
        $expected = array('bar' => $this->form->bar, 'baz' => $this->form->baz);
        $this->assertSame($expected, $group->getElements());
    }

    // Processing

    public function testPopulateProxiesToSetDefaults()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $values = array(
            'foo' => 'foovalue',
            'bar' => 'barvalue',
            'baz' => 'bazvalue',
            'bat' => 'batvalue'
        );
        $this->form->populate($values);
        $test     = $this->form->getValues();
        $elements = $this->form->getElements();
        foreach (array_keys($values) as $name) {
            $this->assertEquals($values[$name], $test[$name]);
        }
    }

    public function setupElements()
    {
        $foo = new Zend_Form_Element_Text('foo');
        $foo->addValidator('NotEmpty')
            ->addValidator('Alpha');
        $bar = new Zend_Form_Element_Text('bar');
        $bar->addValidator('NotEmpty')
            ->addValidator('Digits');
        $baz = new Zend_Form_Element_Text('baz');
        $bar->addValidator('NotEmpty')
            ->addValidator('Alnum');
        $this->form->addElements(array($foo, $bar, $baz));
        $this->elementValues = array(
            'foo' => 'fooBarBAZ',
            'bar' => '123456789',
            'baz' => 'foo123BAR',
        );
    }

    public function testCanValidateFullFormContainingOnlyElements()
    {
        $this->setupElements();
        $this->assertTrue($this->form->isValid($this->elementValues));
        $this->assertFalse($this->form->isValid(array(
            'foo' => '12345',
            'bar' => 'abc',
            'baz' => 'abc-123'
        )));
    }

    public function testValidationTakesElementRequiredFlagsIntoAccount()
    {
        $this->setupElements();

        $this->assertTrue($this->form->isValid(array()));

        $this->form->getElement('foo')->setRequired(true);
        $this->assertTrue($this->form->isValid(array(
            'foo' => 'abc',
            'baz' => 'abc123'
        )));
        $this->assertFalse($this->form->isValid(array(
            'baz' => 'abc123'
        )));
    }

    public function testCanValidatePartialFormContainingOnlyElements()
    {
        $this->setupElements();
        $this->form->getElement('foo')->setRequired(true);
        $this->form->getElement('bar')->setRequired(true);
        $this->form->getElement('baz')->setRequired(true);
        $this->assertTrue($this->form->isValidPartial(array(
            'foo' => 'abc',
            'baz' => 'abc123'
        )));
        $this->assertFalse($this->form->isValidPartial(array(
            'foo' => '123',
            'baz' => 'abc-123'
        )));
    }

    public function setupSubForm()
    {
        $subForm = new Zend_Form_SubForm();
        $foo = new Zend_Form_Element_Text('subfoo');
        $foo->addValidators(array('NotEmpty', 'Alpha'))->setRequired(true);
        $bar = new Zend_Form_Element_Text('subbar');
        $bar->addValidators(array('NotEmpty', 'Digits'));
        $baz = new Zend_Form_Element_Text('subbaz');
        $baz->addValidators(array('NotEmpty', 'Alnum'))->setRequired(true);
        $subForm->addElements(array($foo, $bar, $baz));
        $this->form->addSubForm($subForm, 'sub');
    }

    public function testFullDataArrayUsedToValidateSubFormByDefault()
    {
        $this->setupElements();
        $this->setupSubForm();
        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subfoo' => 'abcdef',
            'subbar' => '123456',
            'subbaz' => '123abc',
        );
        $this->assertTrue($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subfoo' => '123',
            'subbar' => 'abc',
            'subbaz' => '123-abc',
        );
        $this->assertFalse($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subfoo' => 'abc',
            'subbaz' => '123abc',
        );
        $this->assertTrue($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subbar' => '123',
            'subbaz' => '123abc',
        );
        $this->assertFalse($this->form->isValid($data));
    }

    public function testDataKeyWithSameNameAsSubFormIsUsedForValidatingSubForm()
    {
        $this->setupElements();
        $this->setupSubForm();
        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subfoo' => 'abcdef',
                'subbar' => '123456',
                'subbaz' => '123abc',
            ),
        );
        $this->assertTrue($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subfoo' => '123',
                'subbar' => 'abc',
                'subbaz' => '123-abc',
            )
        );
        $this->assertFalse($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subfoo' => 'abc',
                'subbaz' => '123abc',
            )
        );
        $this->assertTrue($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subbar' => '123',
                'subbaz' => '123abc',
            )
        );
        $this->assertFalse($this->form->isValid($data));
    }

    public function testCanValidatePartialFormContainingSubForms()
    {
        $this->setupElements();
        $this->setupSubForm();

        $data = array(
            'subfoo' => 'abcdef',
            'subbar' => '123456',
        );
        $this->assertTrue($this->form->isValidPartial($data));

        $data = array(
            'foo'    => 'abcdef',
            'baz'    => '123abc',
            'sub'    => array(
                'subbar' => '123',
            )
        );
        $this->assertTrue($this->form->isValidPartial($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subfoo' => '123',
            )
        );
        $this->assertFalse($this->form->isValidPartial($data));
    }

    public function testValidatingFormWithDisplayGroupsDoesSameAsWithout()
    {
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
        $this->assertTrue($this->form->isValid($this->elementValues));
        $this->assertFalse($this->form->isValid(array(
            'foo' => '123',
            'bar' => 'abc',
            'baz' => 'abc-123'
        )));
    }

    public function testValidatePartialFormWithDisplayGroupsDoesSameAsWithout()
    {
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
        $this->assertTrue($this->form->isValid(array(
            'foo' => 'abc',
            'baz' => 'abc123'
        )));
        $this->assertFalse($this->form->isValid(array(
            'foo' => '123',
            'baz' => 'abc-123'
        )));
    }

    public function testProcessAjaxReturnsJson()
    {
        $this->markTestIncomplete();
    }

    public function testProcessAjaxCanProcessPartialForm()
    {
        $this->markTestIncomplete();
    }

    public function testPersistDataStoresDataInSession()
    {
        $this->markTestIncomplete();
    }
    
    public function testCanRetrieveErrorCodesFromAllElementsAfterFailedValidation()
    {
        $this->testCanValidateFullFormContainingOnlyElements();
        $codes = $this->form->getErrors();
        $keys = array('foo', 'bar', 'baz');
        $this->assertEquals($keys, array_keys($codes));
    }

    public function testCanRetrieveErrorCodesFromSingleElementAfterFailedValidation()
    {
        $this->testCanValidateFullFormContainingOnlyElements();
        $codes  = $this->form->getErrors();
        $keys   = array('foo', 'bar', 'baz');
        $errors = $this->form->getErrors('foo');
        $foo    = $this->form->foo;
        $this->assertEquals($foo->getErrors(), $errors);
    }
    
    public function testCanRetrieveErrorMessagesFromAllElementsAfterFailedValidation()
    {
        $this->testCanValidateFullFormContainingOnlyElements();
        $codes = $this->form->getMessages();
        $keys = array('foo', 'bar', 'baz');
        $this->assertEquals($keys, array_keys($codes));
    }

    public function testCanRetrieveErrorMessagesFromSingleElementAfterFailedValidation()
    {
        $this->testCanValidateFullFormContainingOnlyElements();
        $codes    = $this->form->getMessages();
        $keys     = array('foo', 'bar', 'baz');
        $messages = $this->form->getMessages('foo');
        $foo      = $this->form->foo;
        $this->assertEquals($foo->getMessages(), $messages);
    }

    public function testErrorCodesFromSubFormReturnedInSeparateArray()
    {
        $this->testFullDataArrayUsedToValidateSubFormByDefault();
        $codes    = $this->form->getErrors();
        $this->assertTrue(array_key_exists('sub', $codes));
        $this->assertTrue(is_array($codes['sub']));
        $keys     = array('subfoo', 'subbar', 'subbaz');
        $this->assertEquals($keys, array_keys($codes['sub']));
    }

    public function testCanRetrieveErrorCodesFromSingleSubFormAfterFailedValidation()
    {
        $this->testFullDataArrayUsedToValidateSubFormByDefault();
        $codes    = $this->form->getErrors('sub');
        $this->assertTrue(is_array($codes));
        $this->assertFalse(empty($codes));
        $keys     = array('subfoo', 'subbar', 'subbaz');
        $this->assertEquals($keys, array_keys($codes));
    }

    public function testErrorMessagesFromSubFormReturnedInSeparateArray()
    {
        $this->testFullDataArrayUsedToValidateSubFormByDefault();
        $codes    = $this->form->getMessages();
        $this->assertTrue(array_key_exists('sub', $codes));
        $this->assertTrue(is_array($codes['sub']));
        $keys     = array('subfoo', 'subbar', 'subbaz');
        $this->assertEquals($keys, array_keys($codes['sub']));
    }

    public function testCanRetrieveErrorMessagesFromSingleSubFormAfterFailedValidation()
    {
        $this->testFullDataArrayUsedToValidateSubFormByDefault();
        $codes    = $this->form->getMessages('sub');
        $this->assertTrue(is_array($codes));
        $this->assertFalse(empty($codes));
        $keys     = array('subfoo', 'subbar', 'subbaz');
        $this->assertEquals($keys, array_keys($codes));
    }

    // View object

    public function getView()
    {
        $view = new Zend_View();
        $libPath = dirname(__FILE__) . '/../../../library';
        $view->addHelperPath($libPath . '/Zend/View/Helper');
        return $view;
    }
    
    public function testGetViewRetrievesFromViewRendererByDefault()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $view = $viewRenderer->view;
        $test = $this->form->getView();
        $this->assertSame($view, $test);
    }

    public function testGetViewReturnsNullWhenNoViewRegisteredWithViewRenderer()
    {
        $this->assertNull($this->form->getView());
    }

    public function testCanSetView()
    {
        $view = new Zend_View();
        $this->assertNull($this->form->getView());
        $this->form->setView($view);
        $received = $this->form->getView();
        $this->assertSame($view, $received);
    }

    // Decorators

    public function testViewHelperDecoratorRegisteredByDefault()
    {
        $decorator = $this->form->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    }

    public function testCanAddSingleDecoratorAsString()
    {
        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));

        $this->form->addDecorator('viewHelper');
        $decorator = $this->form->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    }

    public function testCanRetrieveSingleDecoratorRegisteredAsStringUsingClassName()
    {
        $decorator = $this->form->getDecorator('Zend_Form_Decorator_ViewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    }

    public function testCanAddSingleDecoratorAsDecoratorObject()
    {
        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));

        $decorator = new Zend_Form_Decorator_ViewHelper;
        $this->form->addDecorator($decorator);
        $test = $this->form->getDecorator('Zend_Form_Decorator_ViewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanRetrieveSingleDecoratorRegisteredAsDecoratorObjectUsingShortName()
    {
        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));

        $decorator = new Zend_Form_Decorator_ViewHelper;
        $this->form->addDecorator($decorator);
        $test = $this->form->getDecorator('viewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanAddMultipleDecorators()
    {
        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));

        $testDecorator = new Zend_Form_Decorator_Fieldset;
        $this->form->addDecorators(array(
            'ViewHelper',
            $testDecorator
        ));

        $viewHelper = $this->form->getDecorator('viewHelper');
        $this->assertTrue($viewHelper instanceof Zend_Form_Decorator_ViewHelper);
        $decorator = $this->form->getDecorator('fieldset');
        $this->assertSame($testDecorator, $decorator);
    }

    public function testCanRemoveDecorator()
    {
        $this->testViewHelperDecoratorRegisteredByDefault();
        $this->form->removeDecorator('viewHelper');
        $this->assertFalse($this->form->getDecorator('viewHelper'));
    }

    public function testCanClearAllDecorators()
    {
        $this->testCanAddMultipleDecorators();
        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));
        $this->assertFalse($this->form->getDecorator('fieldset'));
    }

    // Rendering

    public function testRenderReturnsMarkup()
    {
        $this->markTestIncomplete();
    }

    public function testRenderReturnsMarkupRepresentingAllElements()
    {
        $this->markTestIncomplete();
    }

    public function testRenderReturnsMarkupContainingGroups()
    {
        $this->markTestIncomplete();
    }

    public function testRenderDoesNotEmitMultipleFormTags()
    {
        $this->markTestIncomplete();
    }

    public function testRenderReturnsMarkupWithGroupedElementsWhenDisplayGroupPresent()
    {
        $this->markTestIncomplete();
    }

    public function testRenderDoesNotRepeateElementsInDisplayGroups()
    {
        $this->markTestIncomplete();
    }

    public function testToStringProxiesToRender()
    {
        $this->markTestIncomplete();
    }

    // Localization

    public function testTranslatorIsNullByDefault()
    {
        $this->assertNull($this->form->getTranslator());
    }

    public function testCanSetTranslator()
    {
        require_once 'Zend/Translate/Adapter/Array.php';
        $translator = new Zend_Translate_Adapter_Array(array());
        $this->form->setTranslator($translator);
        $received = $this->form->getTranslator($translator);
        $this->assertSame($translator, $received);
    }

    // Iteration
    
    public function testFormObjectIsIterableAndIteratesElements()
    {
        $this->setupElements();
        $expected = array('foo', 'bar', 'baz');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
        }
        $this->assertSame($expected, $received);
    }

    public function testFormObjectIsIterableAndIteratesElementsInExpectedOrder()
    {
        $this->setupElements();
        $this->form->addElement('text', 'checkorder', array('order' => 2));
        $expected = array('foo', 'bar', 'checkorder', 'baz');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
            $this->assertTrue($value instanceof Zend_Form_Element);
        }
        $this->assertSame($expected, $received);
    }

    public function testFormObjectIteratesElementsAndSubforms()
    {
        $this->setupElements();
        $this->setupSubForm();
        $expected = array('foo', 'bar', 'baz', 'sub');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
            $this->assertTrue(($value instanceof Zend_Form_Element)
                              or ($value instanceof Zend_Form_SubForm));
        }
        $this->assertSame($expected, $received);
    }

    public function testFormObjectIteratesDisplayGroupsButSkipsDisplayGroupElements()
    {
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
        $expected = array('bar', 'foobaz');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
            $this->assertTrue(($value instanceof Zend_Form_Element)
                              or ($value instanceof Zend_Form_DisplayGroup));
        }
        $this->assertSame($expected, $received);
    }

    // Countable

    public function testCanCountFormObject()
    {
        $this->setupElements();
        $this->assertEquals(3, count($this->form));
    }

    public function testCountingFormObjectCountsSubForms()
    {
        $this->setupElements();
        $this->setupSubForm();
        $this->assertEquals(4, count($this->form));
    }

    public function testCountingFormCountsDisplayGroupsButOmitsElementsInDisplayGroups()
    {
        $this->testCountingFormObjectCountsSubForms();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
        $this->assertEquals(3, count($this->form));
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_FormTest::main') {
    Zend_Form_FormTest::main();
}