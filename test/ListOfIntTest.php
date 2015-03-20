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
class ListOfIntTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   */
  public function test1()
  {
    $ids = "1,3";
    $ret = DataLayer::testListOfInt($ids);

    self::assertEquals(2, count($ret));
    self::assertArrayHasKey(1, $ret);
    self::assertArrayHasKey(3, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   */
  public function test2()
  {
    $ids = array(2,4);
    $ret = DataLayer::testListOfInt($ids);

    self::assertEquals(2, count($ret));
    self::assertArrayHasKey(2, $ret);
    self::assertArrayHasKey(4, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @expectedException Exception
   */
  public function test3()
  {
    $ids = "2,not_int";
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @expectedException Exception
   */
  public function test4()
  {
    $ids = array('not_int',3);
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @expectedException Exception
   */
  public function test5()
  {
    $ids = null;
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @expectedException Exception
   */
  public function test6()
  {
    $ids = array();
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
