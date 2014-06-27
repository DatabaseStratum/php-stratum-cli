<?php

//----------------------------------------------------------------------------------------------------------------------
class Singleton1WithLobTest extends PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type singleton1 must return 1 value and 1 value only.
   */
  public function test1()
  {
    $ret = DataLayer::testSingleton1aWithLob( 1, 'blob' );
    $this->assertEquals( '1', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns 0 values.
   *
   * @expectedException Exception
   */
  public function test2()
  {
    DataLayer::testSingleton1aWithLob( 0, 'blob' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type singleton1 returns more than 1 values.
   *
   * @expectedException Exception
   */
  public function test3()
  {
    DataLayer::testSingleton1aWithLob( 2, 'blob' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to the MySQL server.
   */
  protected function setUp()
  {
    DataLayer::connect( 'localhost', 'test', 'test', 'test' );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

