<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type none with LOBs.
 */
class NoneWithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test1()
  {
    $ret = $this->dataLayer->tstTestNoneWithLob(0, 'blob');
    self::assertEquals(0, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test2()
  {
    $ret = $this->dataLayer->tstTestNoneWithLob(1, 'blob');
    self::assertEquals(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test3()
  {
    $ret = $this->dataLayer->tstTestNoneWithLob(20, 'blob');
    self::assertEquals(20, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
