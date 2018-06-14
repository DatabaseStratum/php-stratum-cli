<?php

namespace SetBased\Stratum\Test\MySql;

use SetBased\Stratum\MySql\StaticDataLayer;

/**
 * Test cases for class DataLayer.
 */
class StaticDataLayerTest extends DataLayerTestCase
{

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteFloat.
   */
  public function testQuoteFloat1()
  {
    $value    = 123.123;
    $expected = '123.123';
    self::assertSame($expected, $this->dataLayer->quoteFloat($value), var_export($value, true));

    $value    = '123.123';
    $expected = '123.123';
    self::assertSame($expected, $this->dataLayer->quoteFloat($value), var_export($value, true));

    $value    = 0;
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteFloat($value), var_export($value, true));

    $value    = '0';
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteFloat($value), var_export($value, true));

    $value    = null;
    $expected = 'NULL';
    self::assertSame($expected, $this->dataLayer->quoteFloat($value), var_export($value, true));

    $value    = false;
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteFloat($value), var_export($value, true));

    $value    = true;
    $expected = '1';
    self::assertSame($expected, $this->dataLayer->quoteFloat($value), var_export($value, true));
  }
  
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteFloat.
   *
   * @expectedException \TypeError
   */
  public function testQuoteFloat2()
  {
    $this->dataLayer->quoteFloat([]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteFloat.
   *
   * @expectedException \TypeError
   */
  public function testQuoteFloat3()
  {
    $this->dataLayer->quoteFloat(['1', '2']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteFloat.
   *
   * @expectedException \TypeError
   */
  public function testQuoteFloat4()
  {
    $this->dataLayer->quoteFloat(new StaticDataLayer());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteInt.
   */
  public function testQuoteInt1()
  {
    $value    = 123;
    $expected = '123';
    self::assertSame($expected, $this->dataLayer->quoteInt($value), var_export($value, true));

    $value    = '123';
    $expected = '123';
    self::assertSame($expected, $this->dataLayer->quoteInt($value), var_export($value, true));

    $value    = 0;
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteInt($value), var_export($value, true));

    $value    = '0';
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteInt($value), var_export($value, true));

    $value    = null;
    $expected = 'NULL';
    self::assertSame($expected, $this->dataLayer->quoteInt($value), var_export($value, true));

    $value    = false;
    $expected = '0';
    self::assertSame($expected, $this->dataLayer->quoteInt($value), var_export($value, true));

    $value    = true;
    $expected = '1';
    self::assertSame($expected, $this->dataLayer->quoteInt($value), var_export($value, true));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteInt.
   *
   * @expectedException \TypeError
   */
  public function testQuoteInt2()
  {
    $this->dataLayer->quoteInt([]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteInt.
   *
   * @expectedException \TypeError
   */
  public function testQuoteInt3()
  {
    $this->dataLayer->quoteInt(['1', '2']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteInt.
   *
   * @expectedException \TypeError
   */
  public function testQuoteInt4()
  {
    $this->dataLayer->quoteInt(new StaticDataLayer());
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

