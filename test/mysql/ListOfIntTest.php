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
/**
 * Unit test for a parameter with a list on integers.
 */
class ListOfIntTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with a valid list of integers in CSV format.
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
   * Test with a valid array of integers.
   */
  public function test2()
  {
    $ids = [2, 4];
    $ret = DataLayer::testListOfInt($ids);

    self::assertEquals(2, count($ret));
    self::assertArrayHasKey(2, $ret);
    self::assertArrayHasKey(4, $ret);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with a list with an invalid value in CSV format.
   *
   * @expectedException Exception
   */
  public function test3()
  {
    $ids = "2,not_int";
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with an array of with an invalid value.
   *
   * @expectedException Exception
   */
  public function test4a()
  {
    $ids = ['not_int', 3];
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with an array of with an invalid value.
   *
   * @expectedException Exception
   */
  public function test4b()
  {
    $ids = [[], 3];
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with an empty list of integers in CSV format.
   */
  public function test5()
  {
    $ids = null;
    $ret = DataLayer::testListOfInt($ids);
    self::assertEquals(0, count($ret));

    $ids = false;
    $ret = DataLayer::testListOfInt($ids);
    self::assertEquals(0, count($ret));

    $ids = '';
    $ret = DataLayer::testListOfInt($ids);
    self::assertEquals(0, count($ret));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with an empty array/.
   */
  public function test6()
  {
    $ids = [];
    $ret = DataLayer::testListOfInt($ids);
    self::assertEquals(0, count($ret));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with a list of integers and an empty value in CSV format.
   *
   * @expectedException SetBased\Affirm\Exception\RuntimeException
   */
  public function test7a()
  {
    $ids = "1,2,,3";
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with a list of integers and an empty value in CSV format.
   *
   * @expectedException SetBased\Affirm\Exception\RuntimeException
   */
  public function test7b()
  {
    $ids = "1,2,";
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with a list of integers and an empty value in CSV format.
   *
   * @expectedException SetBased\Affirm\Exception\RuntimeException
   */
  public function test7c()
  {
    $ids = ",1,2";
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with an array of integers and an empty value.
   *
   * @expectedException SetBased\Affirm\Exception\RuntimeException
   */
  public function test8a()
  {
    $ids = [1, 2, '', 3];
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with an array of integers and an empty value.
   *
   * @expectedException SetBased\Affirm\Exception\RuntimeException
   */
  public function test8b()
  {
    $ids = [1, 2, 3, null];
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test with an array of integers and an empty value.
   *
   * @expectedException SetBased\Affirm\Exception\RuntimeException
   */
  public function test8c()
  {
    $ids = [false, 1, 2, 3];
    DataLayer::testListOfInt($ids);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
