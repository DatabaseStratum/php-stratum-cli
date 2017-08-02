<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type singleton1.
 */
class Singleton1Test extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton1 must return 1 value and 1 value only.
   */
  public function test1()
  {
    $ret = $this->dataLayer->tstTestSingleton1a(1);
    self::assertEquals(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns 0 values.
   *
   * @expectedException SetBased\Stratum\Exception\ResultException
   */
  public function test2()
  {
    $this->dataLayer->tstTestSingleton1a(0);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns more than 1 values.
   *
   * @expectedException SetBased\Stratum\Exception\ResultException
   */
  public function test3()
  {
    $this->dataLayer->tstTestSingleton1a(2);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

