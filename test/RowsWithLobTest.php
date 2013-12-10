<?php
//----------------------------------------------------------------------------------------------------------------------
class RowsWithLobTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    DataLayer::connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows must return an empty array when no rows are selected.
   */
  public function testSelect0Rows()
  {
    $ret = DataLayer::testRows1WithLob( 0, 'blob' );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 0, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows must return an array with 1 row when only 1 row is selected.
   */
  public function test1()
  {
    $ret = DataLayer::testRows1WithLob( 1, 'blob' );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 1, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows must return an array with 3 rows when 3 rows are selected.
   */
  public function test2()
  {
    $ret = DataLayer::testRows1WithLob( 3, 'blob' );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 3, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

