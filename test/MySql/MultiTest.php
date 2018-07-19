<?php
declare(strict_types=1);

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored routines with designation type row0.
 */
class MultiTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test results of multi query.
   */
  public function test1()
  {
    $results = $this->dataLayer->executeMulti(file_get_contents(__DIR__.'/sql/multi_test01.sql'));

    self::assertInternalType('array', $results);
    self::assertEquals(6, count($results));

    self::assertInternalType('int', $results[0]);
    self::assertEquals(0, $results[0]);

    self::assertInternalType('int', $results[1]);
    self::assertEquals(2, $results[1]);

    self::assertInternalType('array', $results[2]);
    self::assertEquals(2, count($results[2]));

    self::assertInternalType('int', $results[3]);
    self::assertEquals(1, $results[3]);

    self::assertInternalType('array', $results[4]);
    self::assertEquals(1, count($results[4]));

    self::assertInternalType('array', $results[5]);
    self::assertEquals(3, count($results[5]));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

