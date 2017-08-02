<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type none.
 */
class NoneTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test1()
  {
    $ret = $this->dataLayer->tstTestNone(0);
    self::assertEquals(0, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test2()
  {
    $ret = $this->dataLayer->tstTestNone(1);
    self::assertEquals(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test3()
  {
    $ret = $this->dataLayer->tstTestNone(20);
    self::assertEquals(20, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
