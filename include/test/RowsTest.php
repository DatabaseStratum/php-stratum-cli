<?php
//----------------------------------------------------------------------------------------------------------------------
class RowsTest extends PHPUnit_Framework_TestCase
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
  public function test1()
  {
    $ret = TST_DL::TestRows1( 0 );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 0, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows must return an array with 1 row when only 1 row is selected.
   */
  public function test2()
  {
    $ret = TST_DL::TestRows1( 1 );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 1, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type rows must return an array with 3 rows when 3 rows are selected.
   */
  public function test3()
  {
    $ret = TST_DL::TestRows1( 2 );
    $this->assertInternalType( 'array', $ret );
    $this->assertCount( 2, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

