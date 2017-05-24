<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

//----------------------------------------------------------------------------------------------------------------------
class Row0WithLobTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type row0 must return null.
   */
  public function test1()
  {
    $ret = TestDataLayer::tstTestRow0aWithLob(0, 'blob');
    $this->assertInternalType('null', $ret);

  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type row0 must return 1 row.
   */
  public function test2()
  {
    $ret = TestDataLayer::tstTestRow0aWithLob(1, 'blob');
    $this->assertInternalType('array', $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type row0 returns more than 1 rows.
   *
   * @expectedException SetBased\Stratum\Exception\ResultException
   */
  public function test3()
  {
    TestDataLayer::tstTestRow0aWithLob(2, 'blob');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

