<?php

namespace SetBased\Stratum\Test\MySql;

use SetBased\Exception\RuntimeException;
use SetBased\Stratum\MySql\StaticDataLayer;

/**
 * Test cases for class DataLayer.
 */
class StaticDataLayerTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteNum.
   */
  public function testQuoteNum1()
  {
    $value    = 123;
    $expected = '123';
    self::assertSame($expected, $this->dataLayer->quoteNum($value), var_export($value, true));

    $value    = '123';
    $expected = '123';
    self::assertSame($expected, $this->dataLayer->quoteNum($value), var_export($value, true));

    $value    = 0;
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteNum($value), var_export($value, true));

    $value    = '0';
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteNum($value), var_export($value, true));

    $value    = '';
    $expected = 'NULL';
    self::assertSame($expected, $this->dataLayer->quoteNum($value), var_export($value, true));

    $value    = null;
    $expected = 'NULL';
    self::assertSame($expected, $this->dataLayer->quoteNum($value), var_export($value, true));

    $value    = false;
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteNum($value), var_export($value, true));

    $value    = true;
    $expected = '1';
    self::assertSame($expected, $this->dataLayer->quoteNum($value), var_export($value, true));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteNum.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteNum2()
  {
    $this->dataLayer->quoteNum([]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteNum.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteNum3()
  {
    $this->dataLayer->quoteNum(['1', '2']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteNum.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteNum4()
  {
    $this->dataLayer->quoteNum(new StaticDataLayer());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteString.
   */
  public function testQuoteString1()
  {
    $value    = 123;
    $expected = "'123'";
    self::assertSame($expected, $this->dataLayer->quoteString($value), var_export($value, true));

    $value    = '123';
    $expected = "'123'";
    self::assertSame($expected, $this->dataLayer->quoteString($value), var_export($value, true));

    $value    = 0;
    $expected = "'0'";
    self::assertSame($expected, $this->dataLayer->quoteString($value), var_export($value, true));

    $value    = '0';
    $expected = "'0'";
    self::assertSame($expected, $this->dataLayer->quoteString($value), var_export($value, true));

    $value    = '';
    $expected = 'NULL';
    self::assertSame($expected, $this->dataLayer->quoteString($value), var_export($value, true));

    $value    = null;
    $expected = 'NULL';
    self::assertSame($expected, $this->dataLayer->quoteString($value), var_export($value, true));

    $value    = false;
    $expected = 'NULL';
    self::assertSame($expected, $this->dataLayer->quoteString($value), var_export($value, true));

    $value    = true;
    $expected = "'1'";
    self::assertSame($expected, $this->dataLayer->quoteString($value), var_export($value, true));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteString.
   *
   * @expectedException \TypeError
   */
  public function testQuoteString2()
  {
    $this->dataLayer->quoteString([]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteString.
   *
   * @expectedException \TypeError
   */
  public function testQuoteString3()
  {
    $this->dataLayer->quoteString(['hello', 'world']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteString.
   *
   * @expectedException \TypeError
   */
  public function testQuoteString4()
  {
    $this->dataLayer->quoteString(new StaticDataLayer());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

