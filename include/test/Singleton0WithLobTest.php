<?php
//----------------------------------------------------------------------------------------------------------------------
class Singleton0WithLobTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type singleton0 must return null.
   */
  public function test1()
  {
    $ret = TST_DL::TestSingleton0aWithLob( 0, 'blob' );
    $this->assertInternalType( 'null', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type singleton0 must return 1 value.
   */
  public function test2()
  {
    $ret = TST_DL::TestSingleton0aWithLob( 1, 'blob' );
    $this->assertEquals( '1', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** An exception must be thrown when a stored routine with designation type singleton0 returns more than 1 values.
   *  @expectedException Exception
   */
  public function test3()
  {
    TST_DL::TestSingleton0aWithLob( 2, 'blob' );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

