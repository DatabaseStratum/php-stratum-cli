<?php
//----------------------------------------------------------------------------------------------------------------------
class NoneWithLobTest extends PHPUnit_Framework_TestCase
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
  public function test1()
  {
    $ret = TST_DL::TestNoneWithLob( 0, 'blob' );
    $this->assertEquals( 0, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type none must return the nummber of rows affected.
   */
  public function test2()
  {
    $ret = TST_DL::TestNoneWithLob( 1, 'blob' );
    $this->assertEquals( 1, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type none must return the nummber of rows affected.
   */
  public function test3()
  {
    $ret = TST_DL::TestNoneWithLob( 20, 'blob' );
    $this->assertEquals( 20, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
