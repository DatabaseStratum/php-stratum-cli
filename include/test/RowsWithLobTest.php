<?php
//----------------------------------------------------------------------------------------------------------------------
class RowsWithLobTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows must return an empty array when no rows are selected.
   */
  public function testSelect0Rows()
  {
    $ret = TST_DL::TestRows1( 0, 'blob' );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 0, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows must return an array with 1 row when only 1 row is selected.
   */
  public function testSelect1Rows()
  {
    $ret = TST_DL::TestRows1( 1, 'blob' );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 1, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows must return an array with 3 rows when 3 rows are selected.
   */
  public function testSelect2Rows()
  {
    $ret = TST_DL::TestRows1( 3, 'blob' );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 3, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

