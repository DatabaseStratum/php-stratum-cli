<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type map.
 */
class MapTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type map must return an array.
   */
  public function test1()
  {
    $map = $this->dataLayer->tstTestMap1(100);
    $this->assertInternalType('array', $map);
    $this->assertCount(3, $map);
    $this->assertEquals(1, $map['c1']);
    $this->assertEquals(2, $map['c2']);
    $this->assertEquals(3, $map['c3']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type map must return an empty array when no rows are selected.
   */
  public function test2()
  {
    $rows = $this->dataLayer->tstTestMap1(0);
    $this->assertInternalType('array', $rows);
    $this->assertCount(0, $rows);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

