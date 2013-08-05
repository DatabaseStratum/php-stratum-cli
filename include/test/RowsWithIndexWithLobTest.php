<?php
//----------------------------------------------------------------------------------------------------------------------
class RowsWithIndexWithLobTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows_with_index must return multi dimensional array.
   */
  public function testSelectIndexRows()
  {
    $rows = TST_DL::TestRowsWithIndex1( 100, 'blob' );
    $this->assertInternalType( 'array', $rows );

    $this->assertArrayHasKey( 'a', $rows );
    $this->assertArrayHasKey( 'b', $rows['a'] );

    $this->assertNotCount( 0, $rows['a']['b'] );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows_with_index must return empty array when no rwos are selected.
   */
  public function testSelectIndex0Rows()
  {
    $rows = TST_DL::TestRowsWithIndex1( 0, 'blob' );
    $this->assertInternalType( 'array', $rows );
    $this->assertCount( 0, $rows );

  }
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

