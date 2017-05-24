<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

//----------------------------------------------------------------------------------------------------------------------
class RowsWithKeyWithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows_with_key must return multi dimensional array.
   */
  public function test1()
  {
    $rows = TestDataLayer::tstTestRowsWithKey1WithLob(100, 'blob');
    $this->assertInternalType('array', $rows);
    $this->assertCount(1, $rows);

    $this->assertArrayHasKey('a', $rows);
    $this->assertArrayHasKey('b', $rows['a']);

    $this->assertNotCount(0, $rows['a']['b']);

    $this->assertArrayHasKey('c1', $rows['a']['b']);

    $this->assertNotCount(0, $rows['a']['b']['c1']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows_with_key must return empty array when no rwos are selected.
   */
  public function test2()
  {
    $rows = TestDataLayer::tstTestRowsWithKey1WithLob(0, 'blob');
    $this->assertInternalType('array', $rows);
    $this->assertCount(0, $rows);

  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

