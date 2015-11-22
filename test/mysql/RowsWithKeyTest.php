<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * phpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
class RowsWithKeyTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows_with_key must return multi dimensional array.
   */
  public function test1()
  {
    $rows = DataLayer::testRowsWithKey1(100);
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
    $rows = DataLayer::testRowsWithKey1(0);
    $this->assertInternalType('array', $rows);
    $this->assertCount(0, $rows);

  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

