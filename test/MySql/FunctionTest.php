<?php
declare(strict_types=1);

namespace SetBased\Stratum\Test\MySql;

/**
 * Test cases for stored functions.
 */
class FunctionTest extends DataLayerTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function executes a stored function and return result.
   */
  public function test01()
  {
    $value = $this->dataLayer->tstTestFunction(2, 3);
    self::assertEquals(5, $value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function execute stored function and return result.
   */
  public function test02()
  {
    $value = $this->dataLayer->tstTestFunction(3, 4);
    $this->assertNotEquals(5, $value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function and return type bool must return false when value is null.
   */
  public function test11()
  {
    $value = $this->dataLayer->tstTestFunctionBool1(null);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function and return type bool must return false when value is 0.
   */
  public function test12()
  {
    $value = $this->dataLayer->tstTestFunctionBool1(0);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function and return type bool must return true when value is 123.
   */
  public function test13()
  {
    $value = $this->dataLayer->tstTestFunctionBool1(123);
    $this->assertTrue($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function and return type bool must return false when value is null.
   */
  public function test21()
  {
    $value = $this->dataLayer->tstTestFunctionBool2(null);
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function and return type bool must return false when value is empty string.
   */
  public function test22()
  {
    $value = $this->dataLayer->tstTestFunctionBool2('');
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function and return type bool must return false when value is '0'.
   */
  public function test23()
  {
    $value = $this->dataLayer->tstTestFunctionBool2('0');
    $this->assertFalse($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Stored routine with designation type function and return type bool must return true when value is 'hello'.
   */
  public function test24()
  {
    $value = $this->dataLayer->tstTestFunctionBool2('hello');
    $this->assertTrue($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
