<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

//----------------------------------------------------------------------------------------------------------------------
class NoneTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test1()
  {
    $ret = DataLayer::tstTestNone(0);
    $this->assertEquals(0, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test2()
  {
    $ret = DataLayer::tstTestNone(1);
    $this->assertEquals(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test3()
  {
    $ret = DataLayer::tstTestNone(20);
    $this->assertEquals(20, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
