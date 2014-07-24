<?php

//----------------------------------------------------------------------------------------------------------------------
class CommandsOutOfSyncTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   */
  public function test1()
  {
    try
    {
      $e = null;
      DataLayer::testRow0a( 1 );
      DataLayer::testRows1( 1 );
      DataLayer::testRowsWithIndex1( 100 );
      DataLayer::testRowsWithKey1( 100 );
    }
    catch (Exception $e)
    {
      // Nothing to do.
    }

    $this->assertNotInstanceOf('Exception', $e);
    $this->assertEquals($e, null);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

