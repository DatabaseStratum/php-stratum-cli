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
use SetBased\Affirm\Exception\RuntimeException;
use SetBased\Stratum\MySql\StaticDataLayer;

//----------------------------------------------------------------------------------------------------------------------
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
    $this->assertSame($expected, StaticDataLayer::quoteNum($value), var_export($value, true));

    $value    = '123';
    $expected = '123';
    $this->assertSame($expected, StaticDataLayer::quoteNum($value), var_export($value, true));

    $value    = 0;
    $expected = '0';
    $this->assertSame($expected, StaticDataLayer::quoteNum($value), var_export($value, true));

    $value    = '0';
    $expected = '0';
    $this->assertSame($expected, StaticDataLayer::quoteNum($value), var_export($value, true));

    $value    = '';
    $expected = 'NULL';
    $this->assertSame($expected, StaticDataLayer::quoteNum($value), var_export($value, true));

    $value    = null;
    $expected = 'NULL';
    $this->assertSame($expected, StaticDataLayer::quoteNum($value), var_export($value, true));

    $value    = false;
    $expected = '0';
    $this->assertSame($expected, StaticDataLayer::quoteNum($value), var_export($value, true));

    $value    = true;
    $expected = '1';
    $this->assertSame($expected, StaticDataLayer::quoteNum($value), var_export($value, true));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteNum.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteNum2()
  {
    StaticDataLayer::quoteNum([]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteNum.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteNum3()
  {
    StaticDataLayer::quoteNum(['1', '2']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteNum.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteNum4()
  {
    StaticDataLayer::quoteNum(new StaticDataLayer());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteString.
   */
  public function testQuoteString1()
  {
    $value    = 123;
    $expected = "'123'";
    $this->assertSame($expected, StaticDataLayer::quoteString($value), var_export($value, true));

    $value    = '123';
    $expected = "'123'";
    $this->assertSame($expected, StaticDataLayer::quoteString($value), var_export($value, true));

    $value    = 0;
    $expected = "'0'";
    $this->assertSame($expected, StaticDataLayer::quoteString($value), var_export($value, true));

    $value    = '0';
    $expected = "'0'";
    $this->assertSame($expected, StaticDataLayer::quoteString($value), var_export($value, true));

    $value    = '';
    $expected = 'NULL';
    $this->assertSame($expected, StaticDataLayer::quoteString($value), var_export($value, true));

    $value    = null;
    $expected = 'NULL';
    $this->assertSame($expected, StaticDataLayer::quoteString($value), var_export($value, true));

    $value    = false;
    $expected = 'NULL';
    $this->assertSame($expected, StaticDataLayer::quoteString($value), var_export($value, true));

    $value    = true;
    $expected = "'1'";
    $this->assertSame($expected, StaticDataLayer::quoteString($value), var_export($value, true));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteString.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteString2()
  {
    StaticDataLayer::quoteString([]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteString.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteString3()
  {
    StaticDataLayer::quoteString(['hello', 'world']);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Tests for quoteString.
   *
   * @expectedException RuntimeException
   */
  public function testQuoteString4()
  {
    StaticDataLayer::quoteString(new StaticDataLayer());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

