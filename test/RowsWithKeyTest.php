<?php

//----------------------------------------------------------------------------------------------------------------------
class RowsWithKeyTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows_with_key must return multi dimensional array.
   */
  public function test1()
  {
    $rows = DataLayer::testRowsWithKey1( 100 );
    $this->assertInternalType( 'array', $rows );
    $this->assertCount( 1, $rows );

    $this->assertArrayHasKey( 'a', $rows );
    $this->assertArrayHasKey( 'b', $rows['a'] );

    $this->assertNotCount( 0, $rows['a']['b'] );

    $this->assertArrayHasKey( 'c1', $rows['a']['b'] );

    $this->assertNotCount( 0, $rows['a']['b']['c1'] );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows_with_key must return empty array when no rwos are selected.
   */
  public function test2()
  {
    $rows = DataLayer::testRowsWithKey1( 0 );
    $this->assertInternalType( 'array', $rows );
    $this->assertCount( 0, $rows );

  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to the MySQL server.
   */
  protected function setUp()
  {
    DataLayer::connect( 'localhost', 'test', 'test', 'test' );
  }
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

