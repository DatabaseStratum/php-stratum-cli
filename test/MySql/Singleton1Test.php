<?php

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
  public function test01()
  {
    $ret = $this->dataLayer->tstTestSingleton1a(1);
    self::assertEquals(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns 0 rows.
   *
   * @expectedException \SetBased\Stratum\Exception\ResultException
   */
  public function test02()
  {
    $this->dataLayer->tstTestSingleton1a(0);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns more than 1 rows.
   *
   * @expectedException \SetBased\Stratum\Exception\ResultException
   */
  public function test03()
  {
    $this->dataLayer->tstTestSingleton1a(2);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *  An exception must be thrown when a stored routine with designation type singleton1 return type bool returns 0 rows.
   *
   * @expectedException \SetBased\Stratum\Exception\ResultException
   */
  public function test11()
  {
    $value = $this->dataLayer->tstTestSingleton1b(0, 1);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 and return type bool must return false when selecting 1 row
   * with null value.
   */
  public function test12()
  {
    $value = $this->dataLayer->tstTestSingleton1b(1, null);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 and return type bool must return false when selecting 1 row
   * with empty value.
   */
  public function test13()
  {
    $value = $this->dataLayer->tstTestSingleton1b(1, 0);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 and return type bool must return true when selecting 1 row
   * with non-empty value.
   */
  public function test14()
  {
    $value = $this->dataLayer->tstTestSingleton1b(1, 123);
    $this->assertTrue($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton0  and return type bool returns
   * more than 1 rows.
   *
   * @expectedException \SetBased\Stratum\Exception\ResultException
   */
  public function test15()
  {
    $this->dataLayer->tstTestSingleton1b(2, 1);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

