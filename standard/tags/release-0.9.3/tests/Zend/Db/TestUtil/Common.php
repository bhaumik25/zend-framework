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
 * @see Zend_Db_Expr
 */
require_once 'Zend/Db/Expr.php';

/**
 * PHPUnit_Util_Filter
 */
require_once 'PHPUnit/Util/Filter.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_TestUtil_Common
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @var array
     */
    protected $_tables = array();

    /**
     * @var array
     */
    protected $_sequences = array();

    protected function _getSqlCreateTable($tableName)
    {
        return 'CREATE TABLE ' . $this->_db->quoteIdentifier($tableName);
    }

    protected function _getSqlDropTable($tableName)
    {
        return 'DROP TABLE ' . $this->_db->quoteIdentifier($tableName);
    }

    public function getSqlType($type)
    {
        return $type;
    }

    public function createTable($tableId, array $columns = array())
    {
        if (!$columns) {
            $columns = $this->{'_getColumns'.$tableId}();
        }
        $tableName = $this->getTableName($tableId);
        $this->dropTable($tableName);

        if (isset($this->_tables[$tableName])) {
            return;
        }
        $sql = $this->_getSqlCreateTable($tableName);
        if (!$sql) {
            return;
        }
        $sql .= " (\n\t";

        $pKey = null;
        $pKeys = array();
        if (isset($columns['PRIMARY KEY'])) {
            $pKey = $columns['PRIMARY KEY'];
            unset($columns['PRIMARY KEY']);
            foreach (explode(',', $pKey) as $pKeyCol) {
                $pKeys[] = $this->_db->quoteIdentifier($pKeyCol);
            }
            $pKey = implode(', ', $pKeys);
        }

        foreach ($columns as $columnName => $type) {
            $col[] = $this->_db->quoteIdentifier($columnName) . ' ' . $this->getSqlType($type);
        }

        if ($pKey) {
            $col[] = "PRIMARY KEY ($pKey)";
        }

        $sql .= implode(",\n\t", $col);
        $sql .= "\n)";
        $result = $this->_rawQuery($sql);
        if ($result === false) {
            throw new Zend_Db_Exception("Statement failed:\n$sql\nError: " . $this->_db->getConnection()->error);
        }
        $this->_tables[$tableName] = true;
    }

    public function dropTable($tableName = null)
    {
        if (!$tableName) {
            foreach (array_keys($this->_tables) as $tab) {
                $this->dropTable($tab);
            }
            return;
        }

        $sql = $this->_getSqlDropTable($tableName);
        if (!$sql) {
            return;
        }
        $result = $this->_rawQuery($sql);
        if ($result === false) {
            throw new Zend_Db_Exception("DROP TABLE statement failed:\n$sql\nError: " . $this->_db->getConnection()->error);
        }
        unset($this->_tables[$tableName]);
    }

    protected function _getSqlCreateSequence($sequenceName)
    {
        return null;
    }

    protected function _getSqlDropSequence($sequenceName)
    {
        return null;
    }

    public function createSequence($sequenceName)
    {
        $this->dropSequence($sequenceName);
        if (isset($this->_sequences[$sequenceName])) {
            return;
        }
        $sql = $this->_getSqlCreateSequence($sequenceName);
        if (!$sql) {
            return;
        }
        $result = $this->_rawQuery($sql);
        if ($result === false) {
            throw new Zend_Db_Exception("CREATE SEQUENCE statement failed:\n$sql\nError: " . $this->_db->getConnection()->error);
        }
        $this->_sequences[$sequenceName] = true;
    }

    public function dropSequence($sequenceName = null)
    {
        if (!$sequenceName) {
            foreach (array_keys($this->_sequences) as $seq) {
                $this->dropSequence($seq);
            }
            return;
        }

        $sql = $this->_getSqlDropSequence($sequenceName);
        if (!$sql) {
            return;
        }
        $result = $this->_rawQuery($sql);
        if ($result === false) {
            throw new Zend_Db_Exception("DROP SEQUENCE statement failed:\n$sql\nError: " . $this->_db->getConnection()->error);
        }
        unset($this->_sequences[$sequenceName]);
    }

    public function getParams(array $constants = array())
    {
        $params = array();
        foreach ($constants as $key => $constant) {
            if (defined($constant)) {
                $params[$key] = constant($constant);
            }
        }
        return $params;
    }

    public function getSchema()
    {
        $param = $this->getParams();
        return isset($param['dbname']) ? $param['dbname'] : null;
    }

    protected $_tableName = array(
        'Accounts'      => 'zfaccounts',
        'Products'      => 'zfproducts',
        'Bugs'          => 'zfbugs',
        'BugsProducts'  => 'zfbugs_products',
        'special'       => 'My Table',
    );

    public function getTableName($tableId)
    {
        if (!isset($this->_tableName)) {
            throw new Exception("Invalid table id '$tableId'");
        }
        return $this->_tableName[$tableId];
    }

    protected function _getColumnsBugs()
    {
        return array(
            'bug_id'          => 'IDENTITY',
            'bug_description' => 'VARCHAR(100)',
            'bug_status'      => 'VARCHAR(20)',
            'created_on'      => 'DATETIME',
            'updated_on'      => 'DATETIME',
            'reported_by'     => 'VARCHAR(100)',
            'assigned_to'     => 'VARCHAR(100)',
            'verified_by'     => 'VARCHAR(100)'
        );
    }

    protected function _getColumnsAccounts()
    {
        return array(
            'account_name' => 'VARCHAR(100) NOT NULL',
            'PRIMARY KEY'  => 'account_name'
        );
    }

    protected function _getColumnsProducts()
    {
        return array(
            'product_id'   => 'IDENTITY',
            'product_name' => 'VARCHAR(100)'
        );
    }

    protected function _getColumnsBugsProducts()
    {
        return array(
            'bug_id'       => 'INTEGER NOT NULL',
            'product_id'   => 'INTEGER NOT NULL',
            'PRIMARY KEY'  => 'bug_id,product_id'
        );
    }

    protected function _getDataAccounts()
    {
        return array(
            array('account_name' => 'mmouse'),
            array('account_name' => 'dduck'),
            array('account_name' => 'goofy'),
        );
    }

    protected function _getDataBugs()
    {
        return array(
            array(
                'bug_description' => 'System needs electricity to run',
                'bug_status'      => 'NEW',
                'created_on'      => '2007-04-01',
                'updated_on'      => '2007-04-01',
                'reported_by'     => 'goofy',
                'assigned_to'     => 'mmouse',
            ),
            array(
                'bug_description' => 'Implement Do What I Mean function',
                'bug_status'      => 'VERIFIED',
                'created_on'      => '2007-04-02',
                'updated_on'      => '2007-04-02',
                'reported_by'     => 'goofy',
                'assigned_to'     => 'mmouse',
                'verified_by'     => 'dduck'
            ),
            array(
                'bug_description' => 'Where are my keys?',
                'bug_status'      => 'FIXED',
                'created_on'      => '2007-04-03',
                'updated_on'      => '2007-04-03',
                'reported_by'     => 'dduck',
                'assigned_to'     => 'mmouse',
                'verified_by'     => 'dduck'
            ),
            array(
                'bug_description' => 'Bug no product',
                'bug_status'      => 'INCOMPLETE',
                'created_on'      => '2007-04-04',
                'updated_on'      => '2007-04-04',
                'reported_by'     => 'mmouse'
            )
        );
    }

    protected function _getDataProducts()
    {
        return array(
            array('product_name' => 'Windows'),
            array('product_name' => 'Linux'),
            array('product_name' => 'OS X'),
        );
    }

    protected function _getDataBugsProducts()
    {
        return array(
            array(
                'bug_id'       => 1,
                'product_id'   => 1
            ),
            array(
                'bug_id'       => 1,
                'product_id'   => 2
            ),
            array(
                'bug_id'       => 1,
                'product_id'   => 3
            ),
            array(
                'bug_id'       => 2,
                'product_id'   => 3
            ),
            array(
                'bug_id'       => 3,
                'product_id'   => 2
            ),
            array(
                'bug_id'       => 3,
                'product_id'   => 3
            ),
        );
    }

    public function populateTable($tableId)
    {
        $tableName = $this->getTableName($tableId);
        $data = $this->{'_getData'.$tableId}();
        foreach ($data as $row) {
            $sql = 'INSERT INTO ' .  $this->_db->quoteIdentifier($tableName);
            $cols = array();
            $vals = array();
            foreach ($row as $col => $val) {
                $cols[] = $this->_db->quoteIdentifier($col);
                if ($val instanceof Zend_Db_Expr) {
                    $vals[] = $val->__toString();
                } else {
                    $vals[] = $this->_db->quote($val);
                }
            }
            $sql .=        ' (' . implode(', ', $cols) . ')';
            $sql .= ' VALUES (' . implode(', ', $vals) . ')';
            $result = $this->_rawQuery($sql);
            if ($result === false) {
                throw new Zend_Db_Exception("Statement failed:\n$sql\nError: " . $this->_db->getConnection()->error);
            }
        }
    }

    public function setUp(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
        $this->createTable('Accounts');
        $this->populateTable('Accounts');

        $this->createTable('Products');
        $this->populateTable('Products');

        $this->createTable('Bugs');
        $this->populateTable('Bugs');

        $this->createTable('BugsProducts');
        $this->populateTable('BugsProducts');
    }

    public function tearDown()
    {
        $this->dropTable();
        $this->dropSequence();
        $this->_db->closeConnection();
    }

    protected abstract function _rawQuery($sql);

}
