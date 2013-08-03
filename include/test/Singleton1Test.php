<?php
//----------------------------------------------------------------------------------------------------------------------
class Singleton1Test extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );  
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type singleton1 must return 1 value and 1 value only.
   */
  public function testSelect1Singletons()
  {
    $ret = TST_DL::TestSingleton1a( 1 );      
    $this->assertInternalType( 'string', $ret );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** An exception must be thrown when a stored routine with designation type singleton1 returns 0 values.
   *  @expectedException Exception
   */ 
  public function testSelect0Singletons()
  {
    TST_DL::TestSingleton1a( 0 ); 
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** An exception must be thrown when a stored routine with designation type singleton1 returns more than 1 values.
   *  @expectedException Exception
   */
  public function testSelect2Singletons()
  {
    TST_DL::TestSingleton1a( 2 ); 
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
