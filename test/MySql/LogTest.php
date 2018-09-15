<?php
declare(strict_types=1);

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type log.
 */
class LogTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type none must return the number of rows affected.
   */
  public function test1()
  {
    $n = $this->dataLayer->tstTestLog();

    $this->expectOutputRegex('/^(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\sHello, world\s[1-2]\n){2}$/');
    self::assertEquals(2, $n);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
