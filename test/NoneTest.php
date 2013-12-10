<?php
//----------------------------------------------------------------------------------------------------------------------
class NoneTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    DataLayer::connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type none must return the number of rows affected.
   */
  public function test1()
  {
    $ret = DataLayer::testNone( 0 );
    $this->assertEquals( 0, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type none must return the number of rows affected.
   */
  public function test2()
  {
    $ret = DataLayer::testNone( 1 );
    $this->assertEquals( 1, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type none must return the number of rows affected.
   */
  public function test3()
  {
    $ret = DataLayer::testNone( 20 );
    $this->assertEquals( 20, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
