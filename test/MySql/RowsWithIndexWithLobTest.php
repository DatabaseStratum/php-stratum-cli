<?php

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type rows_with_index with LOBs.
 */
class RowsWithIndexWithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows_with_index must return multi dimensional array.
   */
  public function test1()
  {
    $rows = $this->dataLayer->tstTestRowsWithIndex1WithLob(100, 'blob');
    $this->assertInternalType('array', $rows);

    $this->assertArrayHasKey('a', $rows);
    $this->assertArrayHasKey('b', $rows['a']);

    $this->assertNotCount(0, $rows['a']['b']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows_with_index must return empty array when no rwos are selected.
   */
  public function test2()
  {
    $rows = $this->dataLayer->tstTestRowsWithIndex1(0);
    $this->assertInternalType('array', $rows);
    $this->assertCount(0, $rows);

  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

