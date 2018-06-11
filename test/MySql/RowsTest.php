<?php

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type rows.
 */
class RowsTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows must return an empty array when no rows are selected.
   */
  public function test1()
  {
    $ret = $this->dataLayer->tstTestRows1(0);
    $this->assertInternalType('array', $ret);
    $this->assertCount(0, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows must return an array with 1 row when only 1 row is selected.
   */
  public function test2()
  {
    $ret = $this->dataLayer->tstTestRows1(1);
    $this->assertInternalType('array', $ret);
    $this->assertCount(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows must return an array with 3 rows when 3 rows are selected.
   */
  public function test3()
  {
    $ret = $this->dataLayer->tstTestRows1(2);
    $this->assertInternalType('array', $ret);
    $this->assertCount(2, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

