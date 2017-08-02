<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type row0.
 */
class Row0Test extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type row0 must return null.
   */
  public function test1()
  {
    $ret = $this->dataLayer->tstTestRow0a(0);
    $this->assertInternalType('null', $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type row0 must return 1 row.
   */
  public function test2()
  {
    $ret = $this->dataLayer->tstTestRow0a(1);
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
    $this->dataLayer->tstTestRow0a(2);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

