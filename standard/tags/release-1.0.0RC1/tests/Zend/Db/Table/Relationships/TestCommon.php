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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_Table_TestSetup
 */
require_once 'Zend/Db/Table/TestSetup.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_Relationships_TestCommon extends Zend_Db_Table_TestSetup
{

    public function testTableRelationshipFindParentRow()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentRow('Zend_Db_Table_TableAccounts');
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->account_name);
    }

    public function testTableRelationshipMagicFindParentRow()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentZend_Db_Table_TableAccounts();
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->account_name);
    }

    public function testTableRelationshipMagicException()
    {
        $table = $this->_table['bugs'];

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        // Completely bogus method
        try {
            $result = $parentRow1->nonExistantMethod();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals("Unrecognized method 'nonExistantMethod()'", $e->getMessage());
        }
    }

    public function testTableRelationshipFindParentRowException()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $childRows = $table->fetchAll("$bug_id = 1");
        $childRow1 = $childRows->current();

        try {
            $parentRow = $childRow1->findParentRow('nonexistant_class');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('File "class.php" was not found', $e->getMessage());
        }

        try {
            $parentRow = $childRow1->findParentRow(new stdClass());
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for wrong table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('Parent table must be a Zend_Db_Table_Abstract, but it is stdClass', $e->getMessage());
        }
    }

    public function testTableRelationshipFindManyToManyRowset()
    {
        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('Zend_Db_Table_TableProducts', 'Zend_Db_Table_TableBugsProducts');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(3, $destRows->count());
    }

    public function testTableRelationshipMagicFindManyToManyRowset()
    {
        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findZend_Db_Table_TableProductsViaZend_Db_Table_TableBugsProducts();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(3, $destRows->count());
    }

    public function testTableRelationshipFindManyToManyRowsetException()
    {
        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        // Use nonexistant class for destination table
        try {
            $destRows = $originRow1->findManyToManyRowset('nonexistant_class', 'Zend_Db_Table_TableBugsProducts');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('File "class.php" was not found', $e->getMessage());
        }

        // Use stdClass instead of table class for destination table
        try {
            $destRows = $originRow1->findManyToManyRowset(new stdClass(), 'Zend_Db_Table_TableBugsProducts');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('Match table must be a Zend_Db_Table_Abstract, but it is stdClass', $e->getMessage());
        }

        // Use nonexistant class for intersection table
        try {
            $destRows = $originRow1->findManyToManyRowset('Zend_Db_Table_TableProducts', 'nonexistant_class');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('File "class.php" was not found', $e->getMessage());
        }

        // Use stdClass instead of table class for intersection table
        try {
            $destRows = $originRow1->findManyToManyRowset('Zend_Db_Table_TableProducts', new stdClass());
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('Intersection table must be a Zend_Db_Table_Abstract, but it is stdClass', $e->getMessage());
        }

    }

    public function testTableRelationshipFindDependentRowset()
    {
        $table = $this->_table['bugs'];

        $parentRows = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $parentRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($parentRows));
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findDependentRowset('Zend_Db_Table_TableBugsProducts');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(3, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->bug_id);
        $this->assertEquals(1, $childRow1->product_id);
    }

    public function testTableRelationshipMagicFindDependentRowset()
    {
        $table = $this->_table['bugs'];

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findZend_Db_Table_TableBugsProducts();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(3, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->bug_id);
        $this->assertEquals(1, $childRow1->product_id);
    }

    public function testTableRelationshipFindDependentRowsetException()
    {
        $table = $this->_table['bugs'];

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        try {
            $childRows = $parentRow1->findDependentRowset('nonexistant_class');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('File "class.php" was not found', $e->getMessage());
        }

        try {
            $childRows = $parentRow1->findDependentRowset(new stdClass());
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for wrong table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('Dependent table must be a Zend_Db_Table_Abstract, but it is stdClass', $e->getMessage());
        }
    }

    /**
     * Ensures that basic cascading update functionality succeeds using strings for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageBasicString()
    {
        $bug = $this->_getTable('Zend_Db_Table_TableBugsCustom')
                ->find(1)
                ->current();

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('Zend_Db_Table_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        $bug->bug_id = 333;

        $bug->save();

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('Zend_Db_Table_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        foreach ($bugProducts as $bugProduct) {
            $this->assertEquals(333, $bugProduct->bug_id);
        }

        $bug->bug_id = 1;

        $bug->save();

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('Zend_Db_Table_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        foreach ($bugProducts as $bugProduct) {
            $this->assertEquals(1, $bugProduct->bug_id);
        }
    }

    /**
     * Ensures that basic cascading update functionality succeeds using arrays for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageBasicArray()
    {
        $account1 = $this->_getTable('Zend_Db_Table_TableAccountsCustom')
                    ->find('mmouse')
                    ->current();

        $this->assertEquals(
            1,
            count($account1->findDependentRowset('Zend_Db_Table_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        $account1->account_name = 'daisy';

        $account1->save();

        $this->assertEquals(
            1,
            count($account1Bugs = $account1->findDependentRowset('Zend_Db_Table_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($account1Bugs as $account1Bug) {
            $this->assertEquals('daisy', $account1Bug->reported_by);
        }

        $account1->account_name = 'mmouse';

        $account1->save();

        $this->assertEquals(
            1,
            count($account1Bugs = $account1->findDependentRowset('Zend_Db_Table_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($account1Bugs as $account1Bug) {
            $this->assertEquals('mmouse', $account1Bug->reported_by);
        }
    }

    /**
     * Ensures that cascading update functionality is not run when onUpdate != self::CASCADE
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageInvalidNoop()
    {
        $product1 = $this->_getTable('Zend_Db_Table_TableProductsCustom')
                    ->find(1)
                    ->current();

        $this->assertEquals(
            1,
            count($product1->findDependentRowset('Zend_Db_Table_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product1->product_id = 333;

        $product1->save();

        $this->assertEquals(
            0,
            count($product1BugsProducts = $product1->findDependentRowset('Zend_Db_Table_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product1->product_id = 1;

        $product1->save();

        $this->assertEquals(
            1,
            count($product1BugsProducts = $product1->findDependentRowset('Zend_Db_Table_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($product1BugsProducts as $product1BugsProduct) {
            $this->assertEquals(1, $product1BugsProduct->product_id);
        }
    }

    /**
     * Ensures that basic cascading delete functionality succeeds using strings for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageBasicString()
    {
        $bug1 = $this->_getTable('Zend_Db_Table_TableBugsCustom')
                ->find(1)
                ->current();

        $this->assertEquals(
            3,
            count($bug1->findDependentRowset('Zend_Db_Table_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        $bug1->delete();

        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $this->assertEquals(
            0,
            count($this->_getTable('Zend_Db_Table_TableBugsProductsCustom')->fetchAll("$bug_id = 1")),
            'Expecting cascading delete to have reduced dependent rows to zero'
            );
    }

    /**
     * Ensures that basic cascading delete functionality succeeds using arrays for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageBasicArray()
    {
        $reported_by = $this->_db->quoteIdentifier('reported_by');

        $account1 = $this->_getTable('Zend_Db_Table_TableAccountsCustom')
                    ->find('mmouse')
                    ->current();

        $this->assertEquals(
            1,
            count($account1->findDependentRowset('Zend_Db_Table_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        $account1->delete();

        $tableBugsCustom = $this->_getTable('Zend_Db_Table_TableBugsCustom');

        $this->assertEquals(
            0,
            count(
                $tableBugsCustom->fetchAll(
                    $tableBugsCustom->getAdapter()
                                    ->quoteInto("$reported_by = ?", 'mmouse')
                    )
                ),
            'Expecting cascading delete to have reduced dependent rows to zero'
            );
    }

    /**
     * Ensures that cascading delete functionality is not run when onDelete != self::CASCADE
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageInvalidNoop()
    {
        $product1 = $this->_getTable('Zend_Db_Table_TableProductsCustom')
                    ->find(1)
                    ->current();

        $this->assertEquals(
            1,
            count($product1->findDependentRowset('Zend_Db_Table_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product1->delete();

        $product_id = $this->_db->quoteIdentifier('product_id');

        $this->assertEquals(
            1,
            count($this->_getTable('Zend_Db_Table_TableBugsProductsCustom')->fetchAll("$product_id = 1")),
            'Expecting to find one dependent row'
            );
    }

    public function testTableRelationshipGetReference()
    {
        $table = $this->_table['bugs'];

        $map = $table->getReference('Zend_Db_Table_TableAccounts', 'Reporter');

        $this->assertThat($map, $this->arrayHasKey('columns'));
        $this->assertThat($map, $this->arrayHasKey('refTableClass'));
        $this->assertThat($map, $this->arrayHasKey('refColumns'));
    }

    public function testTableRelationshipGetReferenceException()
    {
        $table = $this->_table['bugs'];

        try {
            $table->getReference('Zend_Db_Table_TableAccounts', 'Nonexistent');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent reference rule');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }

        try {
            $table->getReference('Nonexistent', 'Reporter');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent rule tableClass');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }

        try {
            $table->getReference('Nonexistent');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent rule tableClass');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }
    }

    /**
     * Ensures that findParentRow() returns an instance of a custom row class when passed an instance
     * of the table class having $_rowClass overridden.
     *
     * @return void
     */
    public function testTableRelationshipFindParentRowCustomInstance()
    {
        $myRowClass = 'Zend_Db_Table_Row_TestMyRow';

        Zend_Loader::loadClass($myRowClass);

        $bug1Reporter = $this->_table['bugs']
                        ->find(1)
                        ->current()
                        ->findParentRow($this->_table['accounts']->setRowClass($myRowClass));

        $this->assertType($myRowClass, $bug1Reporter,
            "Expecting object of type $myRowClass, got ".get_class($bug1Reporter));
    }

    /**
     * Ensures that findParentRow() returns an instance of a custom row class when passed a string class
     * name, where the class has $_rowClass overridden.
     *
     * @return void
     */
    public function testTableRelationshipFindParentRowCustomClass()
    {
        $myRowClass = 'Zend_Db_Table_Row_TestMyRow';

        Zend_Loader::loadClass($myRowClass);

        Zend_Loader::loadClass('Zend_Db_Table_TableAccountsCustom');

        $bug1Reporter = $this->_getTable('Zend_Db_Table_TableBugsCustom')
                        ->find(1)
                        ->current()
                        ->findParentRow(new Zend_Db_Table_TableAccountsCustom(array('db' => $this->_db)));

        $this->assertType($myRowClass, $bug1Reporter,
            "Expecting object of type $myRowClass, got ".get_class($bug1Reporter));
    }

    /**
     * Ensures that findDependentRowset() returns instances of custom row and rowset classes when
     * passed an instance of the table class.
     *
     * @return void
     */
    public function testTableRelationshipFindDependentRowsetCustomInstance()
    {
        $myRowsetClass = 'Zend_Db_Table_Rowset_TestMyRowset';
        $myRowClass    = 'Zend_Db_Table_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $account_name = $this->_db->quoteIdentifier('account_name');

        $bugs = $this->_table['accounts']
                ->fetchRow($this->_db->quoteInto("$account_name = ?", 'mmouse'))
                ->findDependentRowset(
                    $this->_table['bugs']
                        ->setRowsetClass($myRowsetClass)
                        ->setRowClass($myRowClass),
                    'Engineer'
                    );

        $this->assertType($myRowsetClass, $bugs,
            "Expecting object of type $myRowsetClass, got ".get_class($bugs));

        $this->assertEquals(3, count($bugs));

        foreach ($bugs as $bug) {
            $this->assertType($myRowClass, $bug,
                "Expecting object of type $myRowClass, got ".get_class($bug));
        }
    }

    /**
     * Ensures that findDependentRowset() returns instances of custom row and rowset classes when
     * passed the named class.
     *
     * @return void
     */
    public function testTableRelationshipFindDependentRowsetCustomClass()
    {
        $myRowsetClass = 'Zend_Db_Table_Rowset_TestMyRowset';
        $myRowClass    = 'Zend_Db_Table_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $account_name = $this->_db->quoteIdentifier('account_name');

        $bugs = $this->_getTable('Zend_Db_Table_TableAccountsCustom')
                ->fetchRow($this->_db->quoteInto("$account_name = ?", 'mmouse'))
                ->findDependentRowset('Zend_Db_Table_TableBugsCustom', 'Engineer');

        $this->assertType($myRowsetClass, $bugs,
            "Expecting object of type $myRowsetClass, got ".get_class($bugs));

        $this->assertEquals(3, count($bugs));

        foreach ($bugs as $bug) {
            $this->assertType($myRowClass, $bug,
                "Expecting object of type $myRowClass, got ".get_class($bug));
        }
    }

    /**
     * Ensures that findManyToManyRowset() returns instances of custom row and rowset class when
     * passed an instance of the table class.
     *
     * @return void
     */
    public function testTableRelationshipFindManyToManyRowsetCustomInstance()
    {
        $myRowsetClass = 'Zend_Db_Table_Rowset_TestMyRowset';
        $myRowClass    = 'Zend_Db_Table_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $bug1Products = $this->_table['bugs']
                        ->find(1)
                        ->current()
                        ->findManyToManyRowset(
                            $this->_table['products']
                                ->setRowsetClass($myRowsetClass)
                                ->setRowClass($myRowClass),
                            'Zend_Db_Table_TableBugsProducts'
                            );

        $this->assertType($myRowsetClass, $bug1Products,
            "Expecting object of type $myRowsetClass, got ".get_class($bug1Products));

        $this->assertEquals(3, count($bug1Products));

        foreach ($bug1Products as $bug1Product) {
            $this->assertType($myRowClass, $bug1Product,
                "Expecting object of type $myRowClass, got ".get_class($bug1Product));
        }
    }

    /**
     * Ensures that findManyToManyRowset() returns instances of custom row and rowset classes when
     * passed the named class.
     *
     * @return void
     */
    public function testTableRelationshipFindManyToManyRowsetCustomClass()
    {
        $myRowsetClass = 'Zend_Db_Table_Rowset_TestMyRowset';
        $myRowClass    = 'Zend_Db_Table_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $bug1Products = $this->_getTable('Zend_Db_Table_TableBugsCustom')
                        ->find(1)
                        ->current()
                        ->findManyToManyRowset(
                            'Zend_Db_Table_TableProductsCustom',
                            'Zend_Db_Table_TableBugsProductsCustom'
                            );

        $this->assertType($myRowsetClass, $bug1Products,
            "Expecting object of type $myRowsetClass, got ".get_class($bug1Products));

        $this->assertEquals(3, count($bug1Products));

        foreach ($bug1Products as $bug1Product) {
            $this->assertType($myRowClass, $bug1Product,
                "Expecting object of type $myRowClass, got ".get_class($bug1Product));
        }
    }

}
