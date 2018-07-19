<?php
declare(strict_types=1);

namespace SetBased\Stratum\Test\MySql;

/**
 * Test multiple calls to wrapper functions don't cause "command out of sync" errors.
 */
class CommandsOutOfSyncTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function test1()
  {
    $this->dataLayer->tstTestRow0a(1);
    $this->dataLayer->tstTestRows1(1);
    $this->dataLayer->tstTestRowsWithIndex1(100);
    $this->dataLayer->tstTestRowsWithKey1(100);

    self::assertTrue(true);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
