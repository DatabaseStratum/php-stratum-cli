<?php
//----------------------------------------------------------------------------------------------------------------------
class RowsWithIndexTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    DataLayer::connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows_with_index must return multi dimensional array.
   */
  public function test1()
  {
    $rows = DataLayer::testRowsWithIndex1( 100 );
    $this->assertInternalType( 'array', $rows );

    $this->assertArrayHasKey( 'a', $rows );
    $this->assertArrayHasKey( 'b', $rows['a'] );

    $this->assertNotCount( 0, $rows['a']['b'] );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows_with_index must return empty array when no rwos are selected.
   */
  public function test2()
  {
    $rows = DataLayer::testRowsWithIndex1( 0 );
    $this->assertInternalType( 'array', $rows );
    $this->assertCount( 0, $rows );

  }
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

