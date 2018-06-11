<?php

namespace SetBased\Stratum\Test\MySql;

use PHPUnit\Framework\TestCase;
use SetBased\Stratum\NameMangler\PsrNameMangler;

/**
 * Test cases for class PsrNameMangler.
 */
class PsrNameManglerTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function testMethod1()
  {
    $camelCase = PsrNameMangler::getMethodName('abc_foo_bar');
    self::assertSame('abcFooBar', $camelCase);

    $camelCase = PsrNameMangler::getMethodName('abc_auth_get_page_info');
    self::assertSame('abcAuthGetPageInfo', $camelCase);

    $camelCase = PsrNameMangler::getMethodName('abc_abc123');
    self::assertSame('abcAbc123', $camelCase);

    $camelCase = PsrNameMangler::getMethodName('abc_abc_123');
    self::assertSame('abcAbc123', $camelCase);
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testParameter1()
  {
    $camelCase = PsrNameMangler::getParameterName('p_cmp_id');
    self::assertSame('pCmpId', $camelCase);

    $camelCase = PsrNameMangler::getParameterName('p_crc32');
    self::assertSame('pCrc32', $camelCase);

    $camelCase = PsrNameMangler::getParameterName('p_crc_32');
    self::assertSame('pCrc32', $camelCase);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
