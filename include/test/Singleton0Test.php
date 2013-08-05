<?php
//----------------------------------------------------------------------------------------------------------------------
class Singleton0Test extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type singleton0 must return null or 1 value.
   */
  public function testSelect0Singletons()
  {
    $ret = TST_DL::TestSingleton0a( 0 );
    $this->assertInternalType( 'null', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type singleton0 must return null or 1 value.
   */
  public function testSelect1Singletons()
  {
    $ret = TST_DL::TestSingleton0a( 1 );
    $this->assertInternalType( 'string', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** An exception must be thrown when a stored routine with designation type singleton0 returns more than 1 values.
   *  @expectedException Exception
   */
  public function testSelect2Singletons()
  {
    TST_DL::TestSingleton0a( 2 );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

