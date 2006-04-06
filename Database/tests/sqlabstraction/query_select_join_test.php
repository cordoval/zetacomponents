<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license BSD {@link http://ez.no/licenses/bsd}
 * @version //autogentag//
 * @filesource
 * @package Database
 * @subpackage Tests
 */

/**
 * Testing the JOIN functionality in the SQL abstraction layer.
 * These tests are performed on a real database and tests that
 * the implementations return the correct result.
 *
 * @package Database
 * @subpackage Tests
 */
class ezcQuerySelectJoinTestImpl extends ezcTestCase
{
    private $q;
    private $e;
    private $db;
    public function setUp()
    {

        $this->db = ezcDbInstance::get();
        $this->q = $this->db->createSelectQuery();
        $this->e = $this->q->expr;
        $this->assertNotNull( $this->db, 'Database instance is not initialized.' );

        try
        {
            $this->db->exec( 'DROP TABLE employees' );
        }
        catch ( Exception $e ) {} // eat
        try
        {
            $this->db->exec( 'DROP TABLE orders' );
        }
        catch ( Exception $e ) {} // eat
        try
        {
            $this->db->exec( 'DROP TABLE in_use' );
        }
        catch ( Exception $e ) {} // eat

        // insert some data
        $this->db->exec( 'CREATE TABLE employees ( id int, name VARCHAR(255) )' );
        $this->db->exec( "INSERT INTO employees VALUES ( 1, 'Raymond Bosman' )" );
        $this->db->exec( "INSERT INTO employees VALUES ( 2, 'Derick Rethans' )" );
        $this->db->exec( "INSERT INTO employees VALUES ( 3, 'Jan Borsodi' )" );
        $this->db->exec( "INSERT INTO employees VALUES ( 4, 'Frederik Holljen' )" );

        $this->db->exec( 'CREATE TABLE orders ( id int, product VARCHAR(255), employee_id int )' );
        $this->db->exec( "INSERT INTO orders VALUES ( 1001, 'Glass', 1 )" );
        $this->db->exec( "INSERT INTO orders VALUES ( 1002, 'Table', 3 )" );
        $this->db->exec( "INSERT INTO orders VALUES ( 1003, 'CPU', 3 )" );
        $this->db->exec( "INSERT INTO orders VALUES ( 1004, 'Cat', 5 )" );
        
        $this->db->exec( 'CREATE TABLE in_use ( id int, product_id int, employee_id int, amount int )' );
        $this->db->exec( "INSERT INTO in_use VALUES ( 2001, 1001, 1, 5 )" );
        $this->db->exec( "INSERT INTO in_use VALUES ( 2002, 1002, 3, 3 )" );
        $this->db->exec( "INSERT INTO in_use VALUES ( 2003, 1003, 3, 2 )" );
        $this->db->exec( "INSERT INTO in_use VALUES ( 2004, 1004, 4, 1 )" );
        $this->db->exec( "INSERT INTO in_use VALUES ( 2005, 1005, 1, 1 )" );
        $this->db->exec( "INSERT INTO in_use VALUES ( 2006, 1005, 2, 1 )" );
    }

    public function tearDown()
    {
        $this->db->exec( 'DROP TABLE employees' );
        $this->db->exec( 'DROP TABLE orders' );
        $this->db->exec( 'DROP TABLE in_use' );
    }

    public function testNormal()
    {
        $this->q->select( 'employees.name', 'orders.product' )->from( 'employees', 'orders' )
                ->where( $this->e->eq( 'employees.id', 'orders.employee_id' ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 3, $rows );
    }

    public function testInnerJoinAsFromArgument()
    {
        $this->q->select( 'employees.name', 'orders.product' )
                 ->from( $this->q->innerJoin( 'employees', 'orders', 'employees.id', 'orders.employee_id' ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 3, $rows );
    }

    public function testInnerJoinAfterFrom()
    {
        $this->q->select( 'employees.name', 'orders.product' )
                 ->from( 'employees' )->innerJoin( 'orders', $this->e->eq( 'employees.id', 'orders.employee_id' ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 3, $rows );
    }

    public function testInnerJoinAfterFromSimplified()
    {
        $this->q->select( 'employees.name', 'orders.product' )
                 ->from( 'employees' )->innerJoin( 'orders', 'employees.id', 'orders.employee_id' );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 3, $rows );
    }

    public function testInnerMultiJoin()
    {
        $this->q->select( 'employees.name', 'orders.product', 'in_use.amount' )
                 ->from( 'employees' )
                   ->innerJoin( 'orders', $this->e->eq( 'employees.id', 'orders.employee_id' ) )
                   ->innerJoin( 'in_use', $this->e->eq( 'in_use.employee_id', 'employees.id' ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 6, $rows );
    }

    public function testInnerMultiJoinWithWhere()
    {
        $this->q->select( 'employees.name', 'orders.product', 'in_use.amount' )
                 ->from( 'employees' )
                   ->innerJoin( 'orders', $this->e->eq( 'employees.id', 'orders.employee_id' ) )
                   ->innerJoin( 'in_use', $this->e->eq( 'in_use.employee_id', 'employees.id' ) )
                 ->where( $this->q->expr->not( $this->q->expr->eq( 'orders.product', "'CPU'" ) ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 4, $rows );
    }

    public function testInnerJoinNotAfterFrom()
    {
        try
        {
            $this->q->select( '*' )->innerJoin( 'table1', 'column1', 'column2' );
        }
        catch( ezcQueryException $e )
        {
            return;
        }
        $this->fail( "Call to innerJoin() not after from() did not fail" );
    }

    public function testLeftJoinAsFromArgument()
    {
        $this->q->select( 'employees.name', 'orders.product' )
                 ->from( $this->q->leftJoin( 'employees', 'orders', 'employees.id', 'orders.employee_id' ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 5, $rows );
    }

    public function testLeftJoinAfterFrom()
    {
        $this->q->select( 'employees.name', 'orders.product' )
                    ->from( 'employees' )
                        ->leftJoin( 'orders', $this->e->eq( 'employees.id', 'orders.employee_id' ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 5, $rows );
    }

    public function testLeftJoinAfterFromSimplified()
    {
        $this->q->select( 'employees.name', 'orders.product' )
                        ->from( 'employees' )
                            ->leftJoin( 'orders', 'employees.id', 'orders.employee_id' );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 5, $rows );
    }

    public function testLeftMultiJoin()
    {
        $this->q->select( 'employees.name', 'orders.product', 'in_use.amount' )
                 ->from( 'employees' )
                    ->leftJoin(  'orders', 'employees.id', 'orders.employee_id' )
                    ->leftJoin(  'in_use', 'in_use.product_id', 'orders.id' );

        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }

        $this->assertEquals( 5, $rows );
    }

    public function testLeftMultiJoinWithWhere()
    {
        $this->q->select( 'employees.name', 'orders.product', 'in_use.amount' )
                 ->from( 'employees' )
                    ->leftJoin(  'orders', 'employees.id', 'orders.employee_id' )
                    ->leftJoin(  'in_use', 'in_use.product_id', 'orders.id' )
                 ->where( $this->q->expr->not( $this->q->expr->isNull( 'orders.product' ) ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 3, $rows );
    }

    public function testLeftJoinNotAfterFrom()
    {
        try
        {
            $this->q->select( '*' )->leftJoin( 'table1', 'column1', 'column2' );
        }
        catch( ezcQueryException $e )
        {
            return;
        }
        $this->fail( "Call to leftJoin() not after from() did not fail" );
    }


    public function testRightJoinAsFromArgument()
    {
        $this->q->select( 'employees.name', 'orders.product' )
                 ->from( $this->q->rightJoin( 'employees', 'orders', 'employees.id', 'orders.employee_id' ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 4, $rows );
    }

    public function testRightJoinAfterFrom()
    {
        if ( $this->db->getName() == 'sqlite' ) //complex right joins not supported by sqlite yet
        {
            return;
        }
        $this->q->select( 'employees.name', 'orders.product' )
                 ->from( 'employees' )->rightJoin( 'orders', $this->e->eq('employees.id', 'orders.employee_id') );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 4, $rows );
    }

    public function testRightJoinAfterFromSimplified()
    {
        if ( $this->db->getName() == 'sqlite' ) //complex right joins not supported by sqlite yet
        {
            return;
        }
        $this->q->select( 'employees.name', 'orders.product' )
                 ->from( 'employees' )->rightJoin( 'orders', 'employees.id', 'orders.employee_id' );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 4, $rows );
    }

    public function testRightMultiJoin()
    {
        if ( $this->db->getName() == 'sqlite' ) //complex right joins not supported by sqlite yet
        {
            return;
        }

        $this->q->select( 'employees.name', 'orders.product', 'in_use.amount' )
                 ->from( 'employees' )
                   ->rightJoin( 'orders', 'employees.id', 'orders.employee_id' )
                   ->rightJoin( 'in_use', 'in_use.product_id', 'orders.id' );

        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 6, $rows );
    }

    public function testRightMultiJoinWithWhere()
    {
        if ( $this->db->getName() == 'sqlite' ) //complex right joins not supported by sqlite yet
        {
            return;
        }

        $this->q->select( 'employees.name', 'orders.product', 'in_use.amount' )
                 ->from( 'employees' )
                   ->rightJoin( 'orders', 'employees.id', 'orders.employee_id' )
                   ->rightJoin( 'in_use', 'in_use.product_id', 'orders.id' )
                 ->where( $this->q->expr->gt( 'in_use.amount', '2' ) );
        $stmt = $this->db->query( $this->q->getQuery() );
        $rows = 0;
        foreach ( $stmt as $row )
        {
            $rows++;
        }
        $this->assertEquals( 2, $rows );
    }

    public function testRightJoinNotAfterFrom()
    {
        try
        {
            $this->q->select( '*' )->rightJoin( 'table1', 'column1', 'column2' );
        }
        catch( ezcQueryException $e )
        {
            return;
        }
        $this->fail( "Call to rightJoin() not after from() did not fail" );
    }

    public static function suite()
    {
        return new ezcTestSuite( 'ezcQuerySelectJoinTestImpl' );
    }
}
?>
