<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

use SetBased\Stratum\NameMangler\PsrNameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Test cases for class PsrNameMangler
 */
class PsrNameManglerTest extends \PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function testMethod1()
  {
    $camelCase = PsrNameMangler::getMethodName('abc_foo_bar');
    $this->assertSame('abcFooBar', $camelCase);

    $camelCase = PsrNameMangler::getMethodName('abc_auth_get_page_info');
    $this->assertSame('abcAuthGetPageInfo', $camelCase);

    $camelCase = PsrNameMangler::getMethodName('abc_abc123');
    $this->assertSame('abcAbc123', $camelCase);

    $camelCase = PsrNameMangler::getMethodName('abc_abc_123');
    $this->assertSame('abcAbc123', $camelCase);
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testParameter1()
  {
    $camelCase = PsrNameMangler::getParameterName('p_cmp_id');
    $this->assertSame('pCmpId', $camelCase);

    $camelCase = PsrNameMangler::getParameterName('p_crc32');
    $this->assertSame('pCrc32', $camelCase);

    $camelCase = PsrNameMangler::getParameterName('p_crc_32');
    $this->assertSame('pCrc32', $camelCase);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
