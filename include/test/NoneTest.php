<?php
//----------------------------------------------------------------------------------------------------------------------
class NoneTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    TST_DL::Connect( 'localhost', 'test', 'test', 'test' );  
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type none must return the nummber of rows affected.
   */
  public function testTestNone1()
  {
    $ret = TST_DL::TestNone( 0 );   
    $this->assertEquals( 0, $ret );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type none must return the nummber of rows affected.
   */
  public function testTestNone2()
  {
    $ret = TST_DL::TestNone( 1 );   
    $this->assertEquals( 1, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type none must return the nummber of rows affected.
   */
  public function testTestNone3()
  {
    $ret = TST_DL::TestNone( 20 );   
    $this->assertEquals( 20, $ret );
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------