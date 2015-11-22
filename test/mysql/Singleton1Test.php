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
class Singleton1Test extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton1 must return 1 value and 1 value only.
   */
  public function test1()
  {
    $ret = DataLayer::testSingleton1a(1);
    $this->assertEquals(1, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns 0 values.
   *
   * @expectedException SetBased\Stratum\Exception\ResultException
   */
  public function test2()
  {
    DataLayer::testSingleton1a(0);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns more than 1 values.
   *
   * @expectedException SetBased\Stratum\Exception\ResultException
   */
  public function test3()
  {
    DataLayer::testSingleton1a(2);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

