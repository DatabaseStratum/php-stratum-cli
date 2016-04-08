<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * PhpStratum
 *
 * @copyright 2005-2015 Paul Water / Set Based IT Consultancy (https://www.setbased.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link
 */
//----------------------------------------------------------------------------------------------------------------------
class RowsWithIndexWithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type rows_with_index must return multi dimensional array.
   */
  public function test1()
  {
    $rows = DataLayer::testRowsWithIndex1WithLob(100, 'blob');
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
    $rows = DataLayer::testRowsWithIndex1(0);
    $this->assertInternalType('array', $rows);
    $this->assertCount(0, $rows);

  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

