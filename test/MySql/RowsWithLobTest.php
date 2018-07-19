<?php
declare(strict_types=1);

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type rows with LOBs.
 */
class RowsWithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows must return an array with 1 row when only 1 row is selected.
   */
  public function test1()
  {
    $ret = $this->dataLayer->tstTestRows1WithLob(1, 'blob');
    $this->assertInternalType('array', $ret);
    $this->assertCount(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows must return an array with 3 rows when 3 rows are selected.
   */
  public function test2()
  {
    $ret = $this->dataLayer->tstTestRows1WithLob(3, 'blob');
    $this->assertInternalType('array', $ret);
    $this->assertCount(3, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows must return an empty array when no rows are selected.
   */
  public function testSelect0Rows()
  {
    $ret = $this->dataLayer->tstTestRows1WithLob(0, 'blob');
    $this->assertInternalType('array', $ret);
    $this->assertCount(0, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

