<?php
//----------------------------------------------------------------------------------------------------------------------
class NoneWithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the nummber of rows affected.
   */
  public function test1()
  {
    $ret = DataLayer::testNoneWithLob( 0, 'blob' );
    $this->assertEquals( 0, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the nummber of rows affected.
   */
  public function test2()
  {
    $ret = DataLayer::testNoneWithLob( 1, 'blob' );
    $this->assertEquals( 1, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the nummber of rows affected.
   */
  public function test3()
  {
    $ret = DataLayer::testNoneWithLob( 20, 'blob' );
    $this->assertEquals( 20, $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
