<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

//----------------------------------------------------------------------------------------------------------------------
class Singleton0Test extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 must return null.
   */
  public function test1()
  {
    $ret = TestDataLayer::tstTestSingleton0a(0);
    $this->assertInternalType('null', $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton0 must return 1 value.
   */
  public function test2()
  {
    $ret = TestDataLayer::tstTestSingleton0a(1);
    $this->assertInternalType('string', $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton0 returns more than 1 values.
   *
   * @expectedException SetBased\Stratum\Exception\ResultException
   */
  public function test3()
  {
    TestDataLayer::tstTestSingleton0a(2);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

