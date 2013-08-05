<?php
//----------------------------------------------------------------------------------------------------------------------
class RowsWithKeyTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows_with_key must return multi dimensional array.
   */
  public function testSelectKeyRows()
  {
    $rows = TST_DL::TestRowsWithKey1( 100 );
    $this->assertInternalType( 'array', $rows );
    $this->assertCount( 1, $rows );

    $this->assertArrayHasKey( 'a', $rows );
    $this->assertArrayHasKey( 'b', $rows['a'] );

    $this->assertNotCount( 0, $rows['a']['b'] );

    $this->assertArrayHasKey( 'c1', $rows['a']['b'] );

    $this->assertNotCount( 0, $rows['a']['b']['c1'] );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows_with_key must return empty array when no rwos are selected.
   */
  public function testSelectKey0Rows()
  {
    $rows = TST_DL::TestRowsWithKey1( 0 );
    $this->assertInternalType( 'array', $rows );
    $this->assertCount( 0, $rows );

  }
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

