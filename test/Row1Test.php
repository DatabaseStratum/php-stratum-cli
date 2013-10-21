<?php
//----------------------------------------------------------------------------------------------------------------------
class Row1Test extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type row1 must return 1 row and 1 row only.
   */
  public function test1()
  {
    $ret = TST_DL::TestRow1a( 1 );
    $this->assertInternalType( 'array', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** An exception must be thrown when a stored routine with designation type row1 returns 0 rows.
   *  @expectedException Exception
   */
  public function test2()
  {
    TST_DL::TestRow1a( 0 );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** An exception must be thrown when a stored routine with designation type row1 returns more than 1 rows.
   *  @expectedException Exception
   */
  public function test3()
  {
    TST_DL::TestRow1a( 2 );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

