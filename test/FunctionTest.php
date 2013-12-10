<?php
//----------------------------------------------------------------------------------------------------------------------
class FunctionTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /** Setups a form with a select form control.
   */
  protected function setUp()
  {
    DataLayer::connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type function execute stored function and return result.
   */
  public function test1()
  {
    $ret = DataLayer::testFunction( 2, 3 );
    $this->assertEquals( 5, $ret );
  }


  //--------------------------------------------------------------------------------------------------------------------
  /** Stored routine with designation type function execute stored function and return result.
   */
  public function test2()
  {
    $ret = DataLayer::testFunction( 3, 4 );
    $this->assertNotEquals( 5, $ret );
  }


}

//----------------------------------------------------------------------------------------------------------------------
