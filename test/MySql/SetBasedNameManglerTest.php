<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Stratum\Test\MySql;

use SetBased\Stratum\NameMangler\SetBasedNameMangler;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Test cases for class SetBasedNameMangler
 */
class SetBasedNameManglerTest extends \PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function testMethod1()
  {
    $camelCase = SetBasedNameMangler::getMethodName('abc_foo_bar');
    $this->assertSame('fooBar', $camelCase);

    $camelCase = SetBasedNameMangler::getMethodName('abc_auth_get_page_info');
    $this->assertSame('authGetPageInfo', $camelCase);

    $camelCase = SetBasedNameMangler::getMethodName('abc_abc123');
    $this->assertSame('abc123', $camelCase);

    $camelCase = SetBasedNameMangler::getMethodName('abc_abc_123');
    $this->assertSame('abc123', $camelCase);
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function testParameter1()
  {
    $camelCase = SetBasedNameMangler::getParameterName('p_cmp_id');
    $this->assertSame('pCmpId', $camelCase);

    $camelCase = SetBasedNameMangler::getParameterName('p_crc32');
    $this->assertSame('pCrc32', $camelCase);

    $camelCase = SetBasedNameMangler::getParameterName('p_crc_32');
    $this->assertSame('pCrc32', $camelCase);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
