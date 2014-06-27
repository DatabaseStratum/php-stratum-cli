<?php
//----------------------------------------------------------------------------------------------------------------------
class Row1Test extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type row1 must return 1 row and 1 row only.
   */
  public function test1()
  {
    $ret = DataLayer::testRow1a( 1 );
    $ret = DataLayer::testRow1a( 1 );
    $this->assertInternalType( 'array', $ret );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type row1 returns 0 rows.
   *
   * @expectedException Exception
   */
  public function test2()
  {
    DataLayer::testRow1a( 0 );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * An exception must be thrown when a stored routine with designation type row1 returns more than 1 rows.
   *
   * @expectedException Exception
   */
  public function test3()
  {
    DataLayer::testRow1a( 2 );
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

