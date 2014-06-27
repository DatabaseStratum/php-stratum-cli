<?php

//----------------------------------------------------------------------------------------------------------------------
class FunctionTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function executes a stored function and return result.
   */
  public function test1()
  {
    $ret = DataLayer::testFunction( 2, 3 );
    $this->assertEquals( 5, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function execute stored function and return result.
   */
  public function test2()
  {
    $ret = DataLayer::testFunction( 3, 4 );
    $this->assertNotEquals( 5, $ret );
  }


  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
