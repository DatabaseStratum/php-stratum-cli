<?php

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type singleton1 with LOBs.
 */
class Singleton1WithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton1 must return 1 value and 1 value only.
   */
  public function test1()
  {
    $ret = $this->dataLayer->tstTestSingleton1aWithLob(1, 'blob');
    self::assertEquals('1', $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns 0 values.
   *
   * @expectedException SetBased\Stratum\Exception\ResultException
   */
  public function test2()
  {
    $this->dataLayer->tstTestSingleton1aWithLob(0, 'blob');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns more than 1 values.
   *
   * @expectedException SetBased\Stratum\Exception\ResultException
   */
  public function test3()
  {
    $this->dataLayer->tstTestSingleton1aWithLob(2, 'blob');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

