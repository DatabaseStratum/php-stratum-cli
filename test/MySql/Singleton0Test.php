<?php

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type singleton0.
 */
class Singleton0Test extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 must return null.
   */
  public function test01()
  {
    $value = $this->dataLayer->tstTestSingleton0a(0);
    $this->assertInternalType('null', $value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 must return 1 value.
   */
  public function test02()
  {
    $value = $this->dataLayer->tstTestSingleton0a(1);
    $this->assertInternalType('int', $value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton0 returns more than 1 rows.
   *
   * @expectedException \SetBased\Stratum\Exception\ResultException
   */
  public function test03()
  {
    $this->dataLayer->tstTestSingleton0a(2);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 and return type bool must return false when selecting 0 rows.
   */
  public function test11()
  {
    $value = $this->dataLayer->tstTestSingleton0b(0, 1);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 and return type bool|null must return false when selecting 1 row
   * with null value.
   */
  public function test12()
  {
    $value = $this->dataLayer->tstTestSingleton0b(1, null);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 and return type bool must return false when selecting 1 row
   * with empty value.
   */
  public function test13()
  {
    $value = $this->dataLayer->tstTestSingleton0b(1, 0);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 and return type bool must return true when selecting 1 row
   * with non-empty value.
   */
  public function test14()
  {
    $value = $this->dataLayer->tstTestSingleton0b(1, 123);
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
    $this->dataLayer->tstTestSingleton0b(2, 1);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

