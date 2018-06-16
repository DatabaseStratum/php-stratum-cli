<?php

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type row1 with LOBs.
 */
class Row1WithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type row1 must return 1 row and 1 row only.
   */
  public function test1()
  {
    $ret = $this->dataLayer->tstTestRow1aWithLob(1, 'blob');
    $this->assertInternalType('array', $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type row1 returns 0 rows.
   *
   * @expectedException \SetBased\Stratum\Exception\ResultException
   */
  public function test2()
  {
    $this->dataLayer->tstTestRow1aWithLob(0, 'blob');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type row1 returns more than 1 rows.
   *
   * @expectedException \SetBased\Stratum\Exception\ResultException
   */
  public function test3()
  {
    $this->dataLayer->tstTestRow1aWithLob(2, 'blob');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

